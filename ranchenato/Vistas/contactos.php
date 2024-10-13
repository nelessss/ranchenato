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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $NomCon = $_POST['NomCon'];
    $ApeCon = $_POST['ApeCon'];
    $DocCon = $_POST['DocCon'];
    $TelCon = $_POST['TelCon'];
    $CorCon = $_POST['CorCon'];
    $asuCon = $_POST['asuCon'];
    $DesCon = $_POST['DesCon'];

    $sql = "INSERT INTO contacto (NomCon, ApeCon, DocCon, TelCon, CorCon, asuCon, DesCon) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$NomCon, $ApeCon, $DocCon, $TelCon, $CorCon, $asuCon, $DesCon])) {
        echo "Registro exitoso del PQR!";
    } else {
        echo "Error: " . implode(", ", $stmt->errorInfo());
    }
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
            <h2>Bienvenido, <?php echo $_SESSION['NomUsu'] . ' ' . $_SESSION['ApeUsu']; ?> </h2>
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
            <h2>Lista de PQR</h2>

            <table>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Asunto</th>
                    <th>Acciones</th>
                </tr>
                <?php
                $sql = "SELECT CodCon, NomCon, asuCon FROM contacto";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($result) > 0) {
                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>".$row["CodCon"]."</td>";
                        echo "<td>".$row["NomCon"]."</td>";
                        echo "<td>".$row["asuCon"]."</td>";
                        echo "<td><a href='../Controlador/contacto_detalle.php?CodCon=".$row["CodCon"]."' class='btn'><i class='fas fa-info-circle'></i></a> &nbsp;";
                        echo "<a href='../Controlador/eliminar_contacto.php?CodCon=".$row["CodCon"]."' class='btn btn-delete'><i class='fas fa-trash-alt'></i></a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No se encontraron contactos.</td></tr>";
                }
                ?>
            </table>
            <br>
            <form action="" method="post">
                <input type="text" name="NomCon" placeholder="Nombre" required>
                <input type="text" name="ApeCon" placeholder="Apellido" required>
                <input type="text" name="DocCon" placeholder="Documento" required>
                <input type="text" name="TelCon" placeholder="Telefono" required>
                <input type="text" name="CorCon" placeholder="Correo" required>
                <input type="text" name="asuCon" placeholder="Asunto" required>
                <input type="text" name="DesCon" placeholder="Descripcion" required>
                <input type="submit" name="agregar" value="Agregar">
            </form>
            <a href="../Vistas/home_admin.php" class="a">Regresar</a>
        </div>
    </div>
</body>
</html>
