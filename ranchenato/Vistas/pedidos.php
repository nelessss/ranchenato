<?php
session_start();

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION["CodUsu"])) {
    header("Location: login.php");
    exit();
}

// Incluir el archivo de conexión a la base de datos
include '../Modelo/db_connection.php';
$database = new Database();
$conn = $database->getConnection();

// Obtener el nombre de usuario de la sesión
$CodUsu = $_SESSION["CodUsu"];
$sql = "SELECT CorUsu FROM usuario WHERE CodUsu = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$CodUsu]);
$usuario = $stmt->fetchColumn();

// Manejar la adición de un nuevo pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $FecPed = $_POST['FecPed'];
    $TotPed = $_POST['TotPed'];
    $EstPed = $_POST['EstPed'];
    $CodUsu = $_POST['CodUsu'];

    $sql_insert = "INSERT INTO Pedido (FecPed, TotPed, EstPed, CodUsu) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    if ($stmt->execute([$FecPed, $TotPed, $EstPed, $CodUsu])) {
        header("Location: pedidos.php");
        exit();
    } else {
        echo "Error al agregar pedido: " . implode(":", $stmt->errorInfo());
    }
}

// Consultar los pedidos existentes
$sql = "SELECT pedido.CodPed, pedido.FecPed, pedido.TotPed, pedido.EstPed, usuario.NomUsu 
        FROM Pedido 
        INNER JOIN usuario ON pedido.CodUsu = usuario.CodUsu";
$stmt = $conn->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql_usuarios = "SELECT CodUsu, NomUsu FROM usuario";
$stmt_usuarios = $conn->query($sql_usuarios);
$result_usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pedidos</title>
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
            <h2>Lista de Pedidos</h2>

            <table>
                <tr>
                    <th>Código</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Usuario</th>
                    <th>Acciones</th>
                </tr>
                <?php
                if (!empty($result)) {
                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>".$row["CodPed"]."</td>";
                        echo "<td>".$row["FecPed"]."</td>";
                        echo "<td>".number_format($row["TotPed"])." COP</td>";
                        echo "<td>".$row["EstPed"]."</td>";
                        echo "<td>".$row["NomUsu"]."</td>";
                        echo "<td>
                                <a href='../Controlador/editar_pedido.php?CodPed=".$row["CodPed"]."' class='btn'><i class='fas fa-edit'></i></a>
                                <a href='../Controlador/eliminar_pedido.php?CodPed=".$row["CodPed"]."' class='btn btn-delete'><i class='fas fa-trash-alt'></i></a>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No se encontraron pedidos.</td></tr>";
                }
                ?>
            </table>
            <br>
            <form action="" method="post">
                <input type="date" name="FecPed" required>
                <input type="number" name="TotPed" placeholder="Total Pedido" step="0.01" required>
                <input type="text" name="EstPed" placeholder="Estado Pedido" required>
                <select name="CodUsu" required>
                    <?php
                    if (!empty($result_usuarios)) {
                        foreach ($result_usuarios as $row_usuario) {
                            echo "<option value='".$row_usuario['CodUsu']."'>".$row_usuario['NomUsu']."</option>";
                        }
                    }
                    ?>
                </select>
                <input type="submit" name="agregar" value="Agregar Pedido">
            </form>
            <br>
            <a href="../Vistas/home_admin.php" class="a">Regresar</a>
        </div>
    </div>
</body>
</html>
