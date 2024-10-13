<?php
session_start();

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION["CodUsu"])) {
    header("Location: login.php");
    exit();
}

// Incluir la clase Database
include '../Modelo/db_connection.php';
$db = new Database();
$conn = $db->getConnection();

// Obtener el nombre de usuario de la sesión
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
    <title>Detalles del Contacto</title>
    <link rel="stylesheet" href="../aseets/css/admin.css">
</head>
<body style="background: url(../Vistas/Imagenes/fondobeige.jpg);">
    <div class="container">
        <div class="sidebar" style="background: url(../Vistas/Imagenes/fondo.jpg);">
            <h2>Bienvenido, <?php echo $_SESSION['NomUsu'] . ' ' . $_SESSION['ApeUsu']; ?></h2>
            <div class="table-links">
                <a href="usuario.php">Usuarios</a>
                <a href="roles.php">Roles</a>
                <a href="empleados.php">Empleados</a>
                <a href="productos.php">Productos</a>
                <a href="pedidos.php">Pedidos</a>
                <a href="domicilio.php">Domicilios</a>
            </div>
            <div class="logout-btn">
                <form action="../Controlador/logout.php" method="post">
                    <button type="submit">Cerrar Sesión</button>
                </form>
            </div>
        </div>
        <div class="content" style="background: url(../Vistas/Imagenes/fondo.jpg);">
            <h2>Detalles del Contacto</h2>

            <?php
            if (isset($_GET['CodCon'])) {
                $CodCon = $_GET['CodCon'];
                $sql = "SELECT * FROM contacto WHERE CodCon = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$CodCon]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row) {
                    echo "<p><strong>Código:</strong> ".htmlspecialchars($row["CodCon"])."</p>";
                    echo "<p><strong>Nombre:</strong> ".htmlspecialchars($row["NomCon"])." ".htmlspecialchars($row["ApeCon"])."</p>";
                    echo "<p><strong>Documento:</strong> ".htmlspecialchars($row["DocCon"])."</p>";
                    echo "<p><strong>Teléfono:</strong> ".htmlspecialchars($row["TelCon"])."</p>";
                    echo "<p><strong>Correo Electrónico:</strong> ".htmlspecialchars($row["CorCon"])."</p>";
                    echo "<p><strong>Asunto:</strong> ".htmlspecialchars($row["asuCon"])."</p>";
                    echo "<p><strong>Descripción:</strong> ".htmlspecialchars($row["DesCon"])."</p>";
                } else {
                    echo "<p>No se encontraron detalles para el contacto.</p>";
                }
            } else {
                echo "<p>No se ha proporcionado un código de contacto válido.</p>";
            }
            ?>

            <a href="../Vistas/contactos.php" class="a">Regresar</a>
        </div>
    </div>
</body>
</html>
