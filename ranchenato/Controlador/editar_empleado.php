<?php
session_start();

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION["CodUsu"])) {
    header("Location: login.php");
    exit();
}

// Obtener el nombre de usuario de la sesión
include '../Modelo/db_connection.php';
$database = new Database();
$conn = $database->getConnection();

$CodUsu = $_SESSION["CodUsu"];
$sql = "SELECT CorUsu FROM usuario WHERE CodUsu = :CodUsu";
$stmt = $conn->prepare($sql);
$stmt->execute([':CodUsu' => $CodUsu]);
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
<body>
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
                <h2>Editar Empleado</h2>

                <?php
                if (isset($_GET['CodEmp'])) {
                    $CodEmp = $_GET['CodEmp'];

                    $sql = "SELECT * FROM empleado WHERE CodEmp = :CodEmp";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([':CodEmp' => $CodEmp]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                        // Obtener los roles para el formulario
                        $sql_roles = "SELECT * FROM rol";
                        $stmt_roles = $conn->query($sql_roles);
                        $roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <form action="../Controlador/actualizar_empleado.php" method="post">
                    <input type="hidden" name="CodEmp" value="<?php echo htmlspecialchars($row['CodEmp']); ?>">
                    
                    <label for="DocEmp">Documento:</label>
                    <input type="text" id="DocEmp" name="DocEmp" value="<?php echo htmlspecialchars($row['DocEmp']); ?>" required>

                    <label for="NomEmp">Nombre:</label>
                    <input type="text" id="NomEmp" name="NomEmp" value="<?php echo htmlspecialchars($row['NomEmp']); ?>" required>

                    <label for="ApeEmp">Apellido:</label>
                    <input type="text" id="ApeEmp" name="ApeEmp" value="<?php echo htmlspecialchars($row['ApeEmp']); ?>" required>

                    <label for="DirEmp">Dirección:</label>
                    <input type="text" id="DirEmp" name="DirEmp" value="<?php echo htmlspecialchars($row['DirEmp']); ?>" required>

                    <label for="TelEmp">Teléfono:</label>
                    <input type="text" id="TelEmp" name="TelEmp" value="<?php echo htmlspecialchars($row['TelEmp']); ?>" required>

                    <label for="ConEmp">Contraseña:</label>
                    <input type="password" id="ConEmp" name="ConEmp" value="<?php echo htmlspecialchars($row['ConEmp']); ?>" required>

                    <label for="CodRol">Rol:</label>
                    <select id="CodRol" name="CodRol" required>
                        <?php
                        foreach ($roles as $row_rol) {
                            $selected = ($row_rol['CodRol'] == $row['CodRol']) ? 'selected' : '';
                            echo "<option value='".$row_rol['CodRol']."' $selected>".$row_rol['NomRol']."</option>";
                        }
                        ?>
                    </select>

                    <input type="submit" name="guardar" class="a" value="Guardar Cambios">
                </form>
                <a href="../Vistas/empleados.php" class="regresar">Regresar</a>
                <?php
                    } else {
                        echo "No se encontró el empleado con el código " . htmlspecialchars($CodEmp);
                    }
                }
                $conn = null;
                ?>
            </div>
        </div>
    </div>
</body>
</html>
