<?php
session_start();

if (!isset($_SESSION["CodUsu"])) {
    header("Location: login.php");
    exit();
}

include '../Modelo/db_connection.php';

// Crear instancia de la base de datos
$database = new Database();
$conn = $database->getConnection();

$CodUsu = $_SESSION["CodUsu"];
$sql = "SELECT CorUsu FROM usuario WHERE CodUsu = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$CodUsu]);
$usuario = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../aseets/css/editar.css">
</head>
<body style="background: url(../Vistas/Imagenes/fondobeige.jpg);">
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
                <h2>Editar Rol</h2>

                <?php
                if(isset($_GET['CodRol'])) {
                    $CodRol = $_GET['CodRol'];

                    // Preparar la consulta
                    $sql = "SELECT * FROM rol WHERE CodRol = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$CodRol]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                        if(isset($_POST['guardar'])) {
                            $NomRol = $_POST['NomRol'];

                            // Preparar la consulta de actualización
                            $sql_update = "UPDATE rol SET NomRol = :NomRol WHERE CodRol = :CodRol";
                            $stmt = $conn->prepare($sql_update);

                            // Ejecutar la consulta
                            if ($stmt->execute([':NomRol' => $NomRol, ':CodRol' => $CodRol])) {
                                echo '<script>window.location.href = "../Vistas/roles.php";</script>';
                                exit();
                            } else {
                                echo "Error al actualizar rol: " . implode(", ", $stmt->errorInfo());
                            }
                        }
                ?>

                <form action="" method="post">
                    <input type="hidden" name="CodRol" value="<?php echo htmlspecialchars($row['CodRol']); ?>">
                    <label for="NomRol">Nombre del Rol:</label>
                    <input type="text" id="NomRol" name="NomRol" value="<?php echo htmlspecialchars($row['NomRol']); ?>" required>

                    <input type="submit" name="guardar" value="Guardar">
                </form>
                <?php
                    } else {
                        echo "No se encontró el rol con el código " . htmlspecialchars($CodRol);
                    }
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
