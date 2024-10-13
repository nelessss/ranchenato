
<?php
session_start();

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION["CodUsu"])) {
    header("Location: login.php");
    exit();
}

// Incluir la conexión a la base de datos
include '../Modelo/db_connection.php';
$database = new Database();
$conn = $database->getConnection();

// Obtener el nombre de usuario de la sesión
$CodUsu = $_SESSION["CodUsu"];
$sql = "SELECT CorUsu FROM usuario WHERE CodUsu = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$CodUsu]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Manejar la adición de un nuevo usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $NomUsu = $_POST['NomUsu'];
    $ApeUsu = $_POST['ApeUsu'];
    $TelUsu = $_POST['TelUsu'];
    $CorUsu = $_POST['CorUsu'];
    $ConUsu = $_POST['ConUsu'];
    $EstUsu = $_POST['EstUsu'];
    $CodRol = $_POST['CodRol'];

    $sql_insert = "INSERT INTO usuario (NomUsu, ApeUsu, TelUsu, CorUsu, ConUsu, EstUsu, CodRol) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    
    if ($stmt_insert->execute([$NomUsu, $ApeUsu, $TelUsu, $CorUsu, $ConUsu, $EstUsu, $CodRol])) {
        header("Location: usuario.php");
        exit();
    } else {
        echo "Error al agregar usuario: " . $stmt_insert->errorInfo()[2];
    }
}

// Consultar roles para el formulario de adición de usuario
$sql_roles = "SELECT CodRol, NomRol FROM rol";
$stmt_roles = $conn->prepare($sql_roles);
$stmt_roles->execute();
$roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);

$stmt_roles->closeCursor();
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
            <h2>Lista de Usuarios</h2>

            <table>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                <?php
                $sql = "SELECT usuario.CodUsu, usuario.NomUsu, usuario.ApeUsu, usuario.TelUsu, usuario.CorUsu, usuario.ConUsu, usuario.EstUsu, rol.NomRol 
                        FROM usuario 
                        INNER JOIN rol ON usuario.CodRol = rol.CodRol";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($usuarios) > 0) {
                    foreach ($usuarios as $row) {
                        echo "<tr>";
                        echo "<td>".$row["CodUsu"]."</td>";
                        echo "<td>".$row["NomUsu"]."</td>";
                        echo "<td>".$row["ApeUsu"]."</td>";
                        echo "<td>".$row["TelUsu"]."</td>";
                        echo "<td>".$row["CorUsu"]."</td>";
                        echo "<td>".$row["NomRol"]."</td>";
                        echo "<td>".$row["EstUsu"]."</td>";
                        echo "<td>
                            <a href='../Controlador/editar_usuario.php?CodUsu=".$row["CodUsu"]."' class='btn'><i class='fas fa-edit'></i></a>
                            <a href='../Controlador/eliminar_usuario.php?CodUsu=".$row["CodUsu"]."' class='btn btn-delete'><i class='fas fa-trash-alt'></i></a>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No se encontraron usuarios.</td></tr>";
                }
                ?>
            </table>
            <br>
            <form action="" method="post">
                <input type="text" name="NomUsu" placeholder="Nombre" required>
                <input type="text" name="ApeUsu" placeholder="Apellido" required>
                <input type="text" name="TelUsu" placeholder="Teléfono" required>
                <input type="text" name="CorUsu" placeholder="Correo" required>
                <input type="text" name="ConUsu" placeholder="Contraseña" required>
                <select name="EstUsu" id="EstUsu" required>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>
                <select name="CodRol" required>
                    <?php
                    foreach ($roles as $role) {
                        echo "<option value='".$role['CodRol']."'>".$role['NomRol']."</option>";
                    }
                    ?>
                </select>
                <input type="submit" name="agregar" value="Agregar Usuario">
            </form>

            <a href="../Vistas/home_admin.php" class="a">Regresar</a>
        </div>
    </div>
</body>
</html>
