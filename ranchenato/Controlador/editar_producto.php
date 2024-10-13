<?php
session_start();
include '../Modelo/db_connection.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["CodUsu"])) {
    header("Location: ../Vistas/login.php");
    exit();
}

// Obtener el nombre de usuario de la sesión
$database = new Database();
$conn = $database->getConnection();

$CodUsu = $_SESSION["CodUsu"];
$sql = "SELECT CorUsu FROM usuario WHERE CodUsu = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$CodUsu]);
$usuario = $stmt->fetchColumn();

if (isset($_GET['CodPro'])) {
    $CodPro = $_GET['CodPro'];

    $sql = "SELECT * FROM Producto WHERE CodPro = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$CodPro]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        echo "Producto no encontrado";
        exit();
    }
} else {
    echo "Código de producto no proporcionado";
    exit();
}

// Procesar la actualización del producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $CatPro = $_POST['CatPro'];
    $NomPro = $_POST['NomPro'];
    $DesPro = $_POST['DesPro'];
    $PrePro = $_POST['PrePro'];
    $StoPro = $_POST['StoPro'];

    // Manejar la carga de la imagen si se proporciona
    if (isset($_FILES['FotPro']) && $_FILES['FotPro']['error'] == 0) {
        $imagen = $_FILES['FotPro']['name'];
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($imagen);

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['FotPro']['tmp_name'], $target_file)) {
            $sql_update = "UPDATE Producto SET CatPro = ?, NomPro = ?, DesPro = ?, PrePro = ?, StoPro = ?, FotPro = ? WHERE CodPro = ?";
            $stmt = $conn->prepare($sql_update);
            if ($stmt->execute([$CatPro, $NomPro, $DesPro, $PrePro, $StoPro, $target_file, $CodPro])) {
                header("Location: ../Vistas/productos.php");
                exit();
            } else {
                echo "Error al actualizar el producto: " . implode(", ", $stmt->errorInfo());
            }
        } else {
            echo "Error al subir la imagen.";
            exit();
        }
    } else {
        $sql_update = "UPDATE Producto SET CatPro = ?, NomPro = ?, DesPro = ?, PrePro = ?, StoPro = ? WHERE CodPro = ?";
        $stmt = $conn->prepare($sql_update);
        if ($stmt->execute([$CatPro, $NomPro, $DesPro, $PrePro, $StoPro, $CodPro])) {
            header("Location: ../Vistas/productos.php");
            exit();
        } else {
            echo "Error al actualizar el producto: " . implode(", ", $stmt->errorInfo());
        }
    }
}
$conn = null; // Cerrar la conexión a la base de datos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../aseets/css/editar.css">
</head>
<body style="background: url('../Vistas/Imagenes/fondobeige.jpg');">
    <div class="container">
        <div class="sidebar" style="background: url(../Vistas/Imagenes/fondo.jpg);">
            <h2>Bienvenido, <?php echo $_SESSION['NomUsu'] . ' ' . $_SESSION['ApeUsu']; ?></h2>
            <div class="table-links">
                <a href="../Vistas/usuario.php">Usuarios</a>
                <a href="../Vistas/roles.php">Roles</a>
                <a href="../Vistas/empleados.php">Empleados</a>
                <a href="../Vistas/productos.php">Productos</a>
                <a href="../Vistas/pedidos.php">Pedidos</a>
                <a href="../Vistas/domicilio.php">Domicilios</a>
                <a href="../Vistas/contactos.php">Contactos</a>
            </div>
            <div class="logout-btn">
                <form action="../Controlador/logout.php" method="post">
                    <button type="submit">Cerrar Sesión</button>
                </form>
            </div>
        </div>
        <div class="content">
            <div class="container-edit" style="background: url(../Vistas/Imagenes/fondo.jpg);">
                <h2>Editar Producto</h2>
                <?php if (isset($producto)) { ?>
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="text" name="CatPro" value="<?php echo htmlspecialchars($producto['CatPro']); ?>" required>
                        <input type="text" name="NomPro" value="<?php echo htmlspecialchars($producto['NomPro']); ?>" required>
                        <input type="text" name="DesPro" value="<?php echo htmlspecialchars($producto['DesPro']); ?>" required>
                        <input type="text" name="PrePro" value="<?php echo htmlspecialchars($producto['PrePro']); ?>" step="0.01" required>
                        <input type="text" name="StoPro" value="<?php echo htmlspecialchars($producto['StoPro']); ?>" required>
                        <input type="file" name="FotPro" accept="image/*">
                        <input type="submit" name="editar" value="Actualizar Producto">
                    </form>
                    <br>
                    <a href="../Vistas/productos.php" class="regresar">Regresar</a>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>
