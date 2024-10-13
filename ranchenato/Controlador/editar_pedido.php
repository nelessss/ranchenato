<?php
session_start();

if (!isset($_SESSION["CodUsu"])) {
    header("Location: login.php");
    exit();
}

include '../Modelo/db_connection.php';
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
                <h2>Editar Pedido</h2>

                <?php
                if (isset($_GET['CodPed'])) {
                    $CodPed = $_GET['CodPed'];

                    $sql = "SELECT * FROM Pedido WHERE CodPed = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$CodPed]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                ?>

                <form action="../Controlador/actualizar_pedido.php" method="post">
                    <input type="hidden" name="CodPed" value="<?php echo htmlspecialchars($row['CodPed']); ?>">
                    <label for="FecPed">Fecha:</label>
                    <input type="date" id="FecPed" name="FecPed" value="<?php echo htmlspecialchars($row['FecPed']); ?>" required>

                    <label for="TotPed">Total:</label>
                    <input type="number" id="TotPed" name="TotPed" value="<?php echo htmlspecialchars($row['TotPed']); ?>" step="0.01" required>

                    <label for="EstPed">Estado:</label>
                    <input type="text" id="EstPed" name="EstPed" value="<?php echo htmlspecialchars($row['EstPed']); ?>" required>

                    <label for="CodUsu">Usuario:</label>
                    <select id="CodUsu" name="CodUsu" required>
                        <?php
                        $sql_usuarios = "SELECT CodUsu, NomUsu FROM usuario";
                        $stmt_usuarios = $conn->query($sql_usuarios);
                        $usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($usuarios as $row_usuario) {
                            $selected = $row_usuario['CodUsu'] == $row['CodUsu'] ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row_usuario['CodUsu']) . "' $selected>" . htmlspecialchars($row_usuario['NomUsu']) . "</option>";
                        }
                        ?>
                    </select>

                    <input type="submit" class="a" value="Guardar Cambios">
                </form>
                <br>
                <a href="../Vistas/pedidos.php" class="regresar">Regresar</a>
                <?php
                    } else {
                        echo "No se encontró el pedido con el código " . htmlspecialchars($CodPed);
                    }
                } else {
                    echo "ID de pedido no especificado.";
                }
                $conn = null;
                ?>
            </div>
        </div>
    </div>
</body>
</html>
