<?php
session_start();

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION["CodUsu"])) {
    header("Location: ../Vistas/login.php");
    exit();
}

// Obtener el nombre de usuario de la sesión
include '../Modelo/db_connection.php';
$database = new Database();
$conn = $database->getConnection();

$CodUsu = $_SESSION["CodUsu"];
$sql = "SELECT CorUsu FROM usuario WHERE CodUsu = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$CodUsu]);
$usuario = $stmt->fetchColumn();
$stmt->closeCursor();

// Manejar la adición de un nuevo producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $CatPro = $_POST['CatPro'];
    $NomPro = $_POST['NomPro'];
    $DesPro = $_POST['DesPro'];
    $PrePro = $_POST['PrePro'];
    $StoPro = $_POST['StoPro'];

    // Manejar la carga de la imagen
    $imagen = $_FILES['FotPro']['name'];
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($imagen);

    // Verificar si el directorio no existe y crearlo si es necesario
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Intentar mover el archivo cargado al directorio de destino
    if (move_uploaded_file($_FILES['FotPro']['tmp_name'], $target_file)) {
        try {
            // Insertar información del producto en la base de datos
            $sql_insert = "INSERT INTO Producto (CatPro, NomPro, DesPro, PrePro, StoPro, FotPro) 
                           VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_insert);
            $stmt->execute([$CatPro, $NomPro, $DesPro, $PrePro, $StoPro, $target_file]);

            // Redirigir a la lista de productos si la inserción fue exitosa
            header("Location: productos.php");
            exit();
        } catch (PDOException $e) {
            // Mostrar mensaje de error si la inserción falla
            echo "Error al agregar producto: " . $e->getMessage();
        }
    } else {
        echo "Error al subir la imagen.";
    }
}

// Consultar los productos existentes
try {
    $sql = "SELECT * FROM Producto";
    $stmt = $conn->query($sql);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al consultar productos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../aseets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body style="background: url(./Imagenes/fondobeige.jpg);">
    <div class="container">
        <div class="sidebar" style="background: url(./Imagenes/fondo.jpg);">
            <h2>Bienvenido, <?php echo $_SESSION['NomUsu'] . ' ' . $_SESSION['ApeUsu']; ?></h2>
            <div class="table-links">
                <a href="usuario.php">Usuarios</a>
                <a href="roles.php">Roles</a>
                <a href="empleados.php">Empleados</a>
                <a href="productos.php">Productos</a>
                <a href="pedidos.php">Pedidos</a>
                <a href="domicilio.php">Domicilios</a>
                <a href="contactos.php">Contactos</a>
            </div>
            <div class="logout-btn">
                <form action="../Controlador/logout.php" method="post">
                    <button type="submit">Cerrar Sesión</button>
                </form>
            </div>
        </div>
        <div class="content" style="background: url(./Imagenes/fondo.jpg);">

            <h2>Lista de Productos</h2>

            <table>
                <tr>
                    <th>Código</th>
                    <th>Categoría</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>
                <?php
                if (isset($productos) && count($productos) > 0) {
                    foreach($productos as $producto) {
                        echo "<tr>";
                        echo "<td>".$producto["CodPro"]."</td>";
                        echo "<td>".$producto["CatPro"]."</td>";
                        echo "<td>".$producto["NomPro"]."</td>";
                        echo "<td>".$producto["DesPro"]."</td>";
                        echo "<td>".number_format($producto["PrePro"])." COP</td>";
                        echo "<td>".$producto["StoPro"]."</td>";
                        echo "<td><img src='".$producto["FotPro"]."' width='100px' height='100px'></td>";
                        echo "<td>
                            <a href='../Controlador/editar_producto.php?CodPro=".$producto["CodPro"]."' class='btn'><i class='fas fa-edit'></i></a>
                            <br><br>
                            <a href='../Controlador/eliminar_producto.php?CodPro=".$producto["CodPro"]."' class='btn btn-delete'><i class='fas fa-trash-alt'></i></a>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No se encontraron productos.</td></tr>";
                }
                ?>
            </table>
            <br>
            <form action="" method="post" enctype="multipart/form-data">
                <select name="CatPro" id="CatPro">
                    <option value="Vinos">Vinos</option>
                    <option value="Whisky">Whisky</option>
                    <option value="Ron">Ron</option>
                    <option value="Vinos">Tequila</option>
                    <option value="Vodka">Vodka</option>
                    <option value="Aguardiente">Aguardiente</option>
                </select>
                <input type="text" name="NomPro" placeholder="Nombre" required>
                <input type="text" name="DesPro" placeholder="Descripción" required>
                <input type="number" name="PrePro" placeholder="Precio" step="0.01" required>
                <input type="number" name="StoPro" placeholder="Stock" required>
                <input type="file" name="FotPro" accept="image/*" required>
                <input type="submit" name="agregar" value="Agregar Producto">
            </form>
            <br>
            <a href="../Vistas/home_admin.php" class="a">Regresar</a>
        </div>
    </div>
</body>
</html>
