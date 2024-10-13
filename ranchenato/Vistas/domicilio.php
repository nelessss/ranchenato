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
$sql = "SELECT NomUsu FROM usuario WHERE CodUsu = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$CodUsu]);
$usuario = $stmt->fetchColumn();
$stmt->closeCursor();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $EstDom = $_POST['EstDom'];
    $DesDom = $_POST['DesDom'];
    $DirDom = $_POST['DirDom'];
    $CodPed = $_POST['CodPed'];
    $CodUsu = $_POST['CodUsu'];

    $sql_insert = "INSERT INTO domicilio (EstDom, DesDom, DirDom, CodPed, CodUsu) 
                   VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);

    try {
        $stmt->execute([$EstDom, $DesDom, $DirDom, $CodPed, $CodUsu]);
        header("Location: domicilio.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al agregar domicilio: " . $e->getMessage();
    }
}

// Consulta para obtener domicilios junto con el código y nombre del usuario
$sql = "SELECT d.CodDom, d.EstDom, d.DesDom, d.DirDom, d.CodPed, d.CodUsu, u.NomUsu 
        FROM domicilio d 
        JOIN usuario u ON d.CodUsu = u.CodUsu";
$result = $conn->query($sql);

$sql_pedidos = "SELECT CodPed FROM Pedido";
$result_pedidos = $conn->query($sql_pedidos);

$sql_empleados = "SELECT CodUsu, NomUsu FROM usuario";
$result_empleados = $conn->query($sql_empleados);
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
            <h2>Lista de Domicilios</h2>

            <table>
                <tr>
                    <th>Código</th>
                    <th>Estado</th>
                    <th>Descripción</th>
                    <th>Dirección</th>
                    <th>Código Pedido</th>
                    <th>Código Usuario</th>
                    <th>Nombre Usuario</th>
                    <th>Acciones</th>
                </tr>
                <?php
                if ($result->rowCount() > 0) {
                    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["CodDom"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["EstDom"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["DesDom"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["DirDom"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["CodPed"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["CodUsu"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["NomUsu"]) . "</td>";
                        echo "<td>
                                <a href='../Controlador/editar_domicilio.php?CodDom=" . htmlspecialchars($row["CodDom"]) . "' class='btn'><i class='fas fa-edit'></i></a>
                                <a href='../Controlador/eliminar_domicilio.php?CodDom=" . htmlspecialchars($row["CodDom"]) . "' class='btn btn-delete'><i class='fas fa-trash-alt'></i></a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No se encontraron domicilios.</td></tr>";
                }
                ?>
            </table>
            <br>
            <form action="" method="post">
                <input type="text" name="EstDom" placeholder="Estado" required>
                <input type="text" name="DesDom" placeholder="Descripción" required>
                <input type="text" name="DirDom" placeholder="Dirección" required>
                <select name="CodPed" required>
                    <?php
                    while($row_pedido = $result_pedidos->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . htmlspecialchars($row_pedido['CodPed']) . "'>" . htmlspecialchars($row_pedido['CodPed']) . "</option>";
                    }
                    ?>
                </select>
                <select name="CodUsu" required>
                    <?php
                    while($row_empleado = $result_empleados->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . htmlspecialchars($row_empleado['CodUsu']) . "'>" . htmlspecialchars($row_empleado['CodUsu']) . ' - '. htmlspecialchars($row_empleado['NomUsu']). "</option>";
                    }
                    ?>
                </select>
                <input type="submit" name="agregar" value="Agregar Domicilio">
            </form>
            <br>
            <a href="../Vistas/home_admin.php" class="a">Regresar</a>
        </div>
    </div>
</body>
</html>
