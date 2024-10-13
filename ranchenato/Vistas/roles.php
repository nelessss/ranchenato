<?php
session_start();

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION["CodUsu"])) {
    header("Location: login.php");
    exit();
}

// Obtener el nombre de usuario de la sesión
include '../Modelo/db_connection.php';

// Crear instancia de la base de datos
$database = new Database();
$conn = $database->getConnection();

$CodUsu = $_SESSION["CodUsu"];
$sql = "SELECT CorUsu FROM usuario WHERE CodUsu = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$CodUsu]);
$usuario = $stmt->fetchColumn();

// Manejar la adición de un nuevo rol
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $NomRol = $_POST['NomRol'];

    // Preparar la consulta
    $sql_insert = "INSERT INTO rol (NomRol) VALUES (:NomRol)";
    $stmt = $conn->prepare($sql_insert);

    // Ejecutar la consulta con el parámetro NomRol
    if ($stmt->execute([':NomRol' => $NomRol])) {
        header("Location: roles.php");
        exit();
    } else {
        echo "Error al agregar rol: " . $stmt->errorInfo()[2];
    }
}

// Consultar los roles existentes
$sql = "SELECT CodRol, NomRol FROM rol";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <h2>Lista de Roles</h2>

            <table>
                <tr>
                    <th>Código</th>
                    <th>Nombre del Rol</th>
                    <th>Acciones</th>
                </tr>
                <?php
                if (!empty($result)) {
                    foreach($result as $row) {
                        echo "<tr>";
                        echo "<td>".$row["CodRol"]."</td>";
                        echo "<td>".$row["NomRol"]."</td>";
                        echo "<td>
                                <a href='../Controlador/editar_rol.php?CodRol=".$row["CodRol"]."' class='btn'><i class='fas fa-edit'></i></a>
                                <a href='../Controlador/eliminar_rol.php?CodRol=".$row["CodRol"]."' class='btn btn-delete'><i class='fas fa-trash-alt'></i></a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No se encontraron roles.</td></tr>";
                }
                ?>
            </table>
            <br>
            <!-- Formulario para agregar un nuevo rol -->
            <form action="" method="post">
                <input type="text" name="NomRol" placeholder="Nombre del nuevo rol" required>
                <input type="submit" name="agregar" value="Agregar Rol">
            </form>
            <a href="../Vistas/home_admin.php" class="a">Regresar</a>
        </div>
    </div>
</body>
</html>
