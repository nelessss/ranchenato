<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["CodUsu"])) {
    header("Location: login.php");
    exit();
}

// Incluir la conexión PDO
include '../Modelo/db_connection.php';
$database = new Database();
$conn = $database->getConnection();

// Obtener el nombre de usuario de la sesión
$CodUsu = $_SESSION["CodUsu"];
$sql = "SELECT CorUsu FROM usuario WHERE CodUsu = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$CodUsu]);
$usuario = $stmt->fetchColumn();
$stmt->closeCursor();

// Manejar la actualización de un domicilio
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
    $CodDom = $_POST['CodDom'];
    $EstDom = $_POST['EstDom'];
    $DesDom = $_POST['DesDom'];
    $DirDom = $_POST['DirDom'];
    $CodPed = $_POST['CodPed'];
    $CodUsu = $_POST['CodUsu'];

    $sql_update = "UPDATE domicilio 
                   SET EstDom = ?, DesDom = ?, DirDom = ?, CodPed = ?, CodUsu = ? 
                   WHERE CodDom = ?";
    $stmt = $conn->prepare($sql_update);

    try {
        $stmt->execute([$EstDom, $DesDom, $DirDom, $CodPed, $CodUsu, $CodDom]);
        header("Location: ../Vistas/domicilio.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al actualizar domicilio: " . $e->getMessage();
    }
}

// Consultar el domicilio a editar
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['CodDom'])) {
    $CodDom = $_GET['CodDom'];

    $sql_select = "SELECT * FROM domicilio WHERE CodDom = ?";
    $stmt = $conn->prepare($sql_select);
    $stmt->execute([$CodDom]);

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "No se encontró el domicilio con el código " . htmlspecialchars($CodDom);
        exit();
    }
    $stmt->closeCursor();
}

// Consultar los pedidos y empleados
$sql_pedidos = "SELECT CodPed FROM Pedido";
$result_pedidos = $conn->query($sql_pedidos);

$sql_empleados = "SELECT CodUsu FROM usuario";
$result_empleados = $conn->query($sql_empleados);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Domicilio</title>
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
                <h2>Editar Domicilio</h2>
                <form action="" method="POST">
                    <input type="hidden" name="CodDom" value="<?php echo htmlspecialchars($row['CodDom']); ?>">
                    <label for="EstDom">Estado:</label>
                    <input type="text" id="EstDom" name="EstDom" value="<?php echo htmlspecialchars($row['EstDom']); ?>" required>
                    <label for="DesDom">Descripción:</label>
                    <input type="text" id="DesDom" name="DesDom" value="<?php echo htmlspecialchars($row['DesDom']); ?>" required>
                    <label for="DirDom">Dirección:</label>
                    <input type="text" id="DirDom" name="DirDom" value="<?php echo htmlspecialchars($row['DirDom']); ?>" required>
                    <label for="CodPed">Código Pedido:</label>
                    <select id="CodPed" name="CodPed" required>
                        <?php
                        while($row_pedido = $result_pedidos->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($row_pedido['CodPed'] == $row['CodPed']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row_pedido['CodPed']) . "' $selected>" . htmlspecialchars($row_pedido['CodPed']) . "</option>";
                        }
                        ?>
                    </select>
                    <label for="CodEmp">Código Empleado:</label>
                    <select id="CodUsu" name="CodUsu" required>
                        <?php
                        while($row_empleado = $result_empleados->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($row_empleado['CodUsu'] == $row['CodUsu']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row_empleado['CodUsu']) . "' $selected>" . htmlspecialchars($row_empleado['CodUsu']) . "</option>";
                        }
                        ?>
                    </select>
                    <input type="submit" name="actualizar" value="Actualizar Domicilio">
                </form>
                <a href="../Vistas/domicilio.php" class="regresar">Regresar</a>
            </div>
        </div>
    </div>
</body>
</html>
