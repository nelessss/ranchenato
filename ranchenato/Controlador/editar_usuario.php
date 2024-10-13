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

// Obtener el correo del usuario de la sesión
$CodUsu = $_SESSION["CodUsu"];
$sql = "SELECT CorUsu FROM usuario WHERE CodUsu = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$CodUsu]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
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
                <h2>Editar Usuario</h2>

                <?php
                if(isset($_GET['CodUsu'])) {
                    $CodUsu = $_GET['CodUsu'];
                    
                    $sql = "SELECT * FROM usuario WHERE CodUsu = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$CodUsu]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                ?>

                <form action="../Controlador/actualizar_usuario.php" method="post">
                    <input type="hidden" name="CodUsu" value="<?php echo htmlspecialchars($row['CodUsu']); ?>">
                    <label for="NomUsu">Nombre:</label>
                    <input type="text" id="NomUsu" name="NomUsu" value="<?php echo htmlspecialchars($row['NomUsu']); ?>" required>

                    <label for="ApeUsu">Apellido:</label>
                    <input type="text" id="ApeUsu" name="ApeUsu" value="<?php echo htmlspecialchars($row['ApeUsu']); ?>" required>

                    <label for="TelUsu">Teléfono:</label>
                    <input type="text" id="TelUsu" name="TelUsu" value="<?php echo htmlspecialchars($row['TelUsu']); ?>" required>

                    <label for="CorUsu">Correo:</label>
                    <input type="email" id="CorUsu" name="CorUsu" value="<?php echo htmlspecialchars($row['CorUsu']); ?>" required>

                    <label for="ConUsu">Contraseña:</label>
                    <input type="password" id="ConUsu" name="ConUsu" value="<?php echo htmlspecialchars($row['ConUsu']); ?>" required>

                    <label for="EstUsu">Estado:</label>
                    <select id="EstUsu" name="EstUsu" required>
                        <option value="Activo" <?php if($row['EstUsu'] == 'Activo') echo 'selected'; ?>>Activo</option>
                        <option value="inactivo" <?php if($row['EstUsu'] == 'inactivo') echo 'selected'; ?>>inactivo</option>
                    </select>

                    <label for="CodRol">Rol:</label>
                    <select id="CodRol" name="CodRol" required>
                        <?php
                        $sql_roles = "SELECT CodRol, NomRol FROM rol";
                        $stmt_roles = $conn->prepare($sql_roles);
                        $stmt_roles->execute();
                        $roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($roles as $role) {
                            $selected = $role['CodRol'] == $row['CodRol'] ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($role['CodRol']) . "' $selected>" . htmlspecialchars($role['NomRol']) . "</option>";
                        }
                        ?>
                    </select>

                    <input type="submit" class="a" value="Actualizar Usuario">
                </form>
                <a href="../Vistas/usuario.php" class="regresar">Regresar</a>
                <?php
                    } else {
                        echo "No se encontró el usuario con el código " . htmlspecialchars($CodUsu);
                    }
                }

                ?>
            </div>
        </div>
    </div>
</body>
</html>
