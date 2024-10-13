<?php
session_start();
include '../Modelo/db_connection.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['CodUsu'])) {
    header("Location: ../Vistas/login.php");
    exit();
}

$CodUsu = $_SESSION['CodUsu'];

// Establecer conexión con PDO
$db = new Database();
$pdo = $db->getConnection();

// Obtener la información del usuario
$sql_user = "SELECT NomUsu, ApeUsu, CorUsu, EstUsu, ConUsu FROM usuario WHERE CodUsu = :CodUsu";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['CodUsu' => $CodUsu]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        $NomUsu = $_POST['NomUsu'];
        $ApeUsu = $_POST['ApeUsu'];
        $CorUsu = $_POST['CorUsu'];
        $ConUsu = $_POST['ConUsu'] ? $_POST['ConUsu'] : $user['ConUsu'];

        $update_sql = "UPDATE usuario SET NomUsu = :NomUsu, ApeUsu = :ApeUsu, CorUsu = :CorUsu, ConUsu = :ConUsu WHERE CodUsu = :CodUsu";
        $update_stmt = $pdo->prepare($update_sql);
        if ($update_stmt->execute([
            'NomUsu' => $NomUsu,
            'ApeUsu' => $ApeUsu,
            'CorUsu' => $CorUsu,
            'ConUsu' => $ConUsu,
            'CodUsu' => $CodUsu
        ])) {
            $_SESSION['NomUsu'] = $NomUsu;
            $_SESSION['ApeUsu'] = $ApeUsu;
            $update_message = "Información actualizada correctamente.";
        } else {
            $update_error = "Error al actualizar la información.";
        }
    } elseif (isset($_POST['change_password'])) {
        $new_password = $_POST['new_password'];

        $update_password_sql = "UPDATE usuario SET ConUsu = :ConUsu WHERE CodUsu = :CodUsu";
        $update_password_stmt = $pdo->prepare($update_password_sql);
        if ($update_password_stmt->execute([
            'ConUsu' => $new_password,
            'CodUsu' => $CodUsu
        ])) {
            $password_message = "Contraseña actualizada correctamente.";
        } else {
            $password_error = "Error al actualizar la contraseña.";
        }
    } elseif (isset($_POST['disable'])) {
        $disable_sql = "UPDATE usuario SET EstUsu = 'inactivo' WHERE CodUsu = :CodUsu";
        $disable_stmt = $pdo->prepare($disable_sql);
        if ($disable_stmt->execute(['CodUsu' => $CodUsu])) {
            session_destroy();
            header("Location: ../index.php");
            exit();
        } else {
            $disable_error = "Error al inhabilitar la cuenta.";
        }
    } elseif (isset($_POST['delete'])) {
        // Primero elimina los pedidos asociados
        $delete_orders_sql = "DELETE FROM pedido WHERE CodUsu = :CodUsu";
        $delete_orders_stmt = $pdo->prepare($delete_orders_sql);
        $delete_orders_stmt->execute(['CodUsu' => $CodUsu]);

        // Luego elimina el usuario
        $delete_sql = "DELETE FROM usuario WHERE CodUsu = :CodUsu";
        $delete_stmt = $pdo->prepare($delete_sql);
        if ($delete_stmt->execute(['CodUsu' => $CodUsu])) {
            session_destroy();
            header("Location: ../index.php");
            exit();
        } else {
            $delete_error = "Error al eliminar la cuenta.";
        }
    }
}

// Obtener los pedidos del usuario
$section = isset($_GET['section']) ? $_GET['section'] : 'info';

if ($section == 'Ped') {
    $sql_orders = "SELECT CodPed, FecPed, EstPed, TotPed FROM pedido WHERE CodUsu = :CodUsu";
    $stmt_orders = $pdo->prepare($sql_orders);
    $stmt_orders->execute(['CodUsu' => $CodUsu]);
    $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Gill Sans', 'Gill Sans MT', 'Trebuchet MS', sans-serif;
            background: url(../Vistas/Imagenes/fondobeige.jpg);
            display: flex;
        }
        .menu {
            width: 250px;
            padding: 20px;
            background: url(../Vistas/Imagenes/fondo.jpg);
            color: white;
            height: 100vh;
            position: fixed;
        }
        .menu h2 {
            margin-top: 0;
            font-size: 24px;
        }
        .menu a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 0;
        }
        .menu a:hover {
            border-radius: 5px;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
            color: white;
            width: calc(100% - 270px);
        }
        .form-container {
            backdrop-filter: blur(15px);
            border: 0.5px white solid;
            padding: 20px;
            border-radius: 10px;
            color: white;
            background: url(../Vistas/Imagenes/fondo.jpg);
        }
        .input, .button {
            display: block;
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-bottom: 2px solid white;
            border-radius: 5px;
            color: white;
            background: transparent;
        }
        .button {
            background: transparent;
            border: 1px solid white;
            cursor: pointer;
            color: white;
        }
        .button:hover {
            filter: opacity(50%);
        }
        .terms {
            margin-top: 20px;
            border: 1px solid white;
            padding: 10px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #333;
            color: white;
            padding: 12px 15px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }
        td {
            padding: 10px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        a.button {
            background: #28a745;
            color: #fff;
            text-decoration: none;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
        }
        a.button:hover {
            background: #218838;
        }
    </style>
    <title>Mi Cuenta</title>
</head>
<body>
    <div class="menu">
        <h2>Mi Cuenta</h2>
        <?php if (isset($_SESSION['CodRol']) && $_SESSION['CodRol'] == 1): ?>
        <a style="color: white;" href="../index.php" class="link">Inicio</a>
        &nbsp;
        <?php endif; ?>
        <?php if (isset($_SESSION['CodRol']) && $_SESSION['CodRol'] == 2): ?>
        <a style="color: white;" href="../index.php" class="link">Inicio</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['CodRol']) && $_SESSION['CodRol'] == 3): ?>
        <a style="color: white;" href="../Vistas/domiciliario.php" class="link">Inicio</a>
        <?php endif; ?>
        <a href="?section=info">Información de la Cuenta</a>
        <a href="?section=Ped">Pedidos</a>
        <a href="?section=edit">Editar Cuenta</a>
        <a href="?section=disable">Inhabilitar Cuenta</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>
    <div class="content">
        <?php
        switch ($section) {
            case 'edit':
                ?>
                <div class="form-container">
                    <h1>Editar Información</h1>
                    <form action="" method="POST">
                        <label for="NomUsu">Nombre:</label>
                        <input type="text" id="NomUsu" name="NomUsu" value="<?php echo htmlspecialchars($user['NomUsu']); ?>" class="input" required>
                        <label for="ApeUsu">Apellido:</label>
                        <input type="text" id="ApeUsu" name="ApeUsu" value="<?php echo htmlspecialchars($user['ApeUsu']); ?>" class="input" required>
                        <label for="CorUsu">Correo:</label>
                        <input type="email" id="CorUsu" name="CorUsu" value="<?php echo htmlspecialchars($user['CorUsu']); ?>" class="input" required>
                        <label for="ConUsu">Contraseña:</label>
                        <input type="password" id="ConUsu" name="ConUsu" class="input" placeholder="********">
                        <button type="submit" name="update" class="button">Actualizar Información</button>
                    </form>
                    <?php if (isset($update_message)) { ?>
                        <p><?php echo $update_message; ?></p>
                    <?php } ?>
                    <?php if (isset($update_error)) { ?>
                        <p><?php echo $update_error; ?></p>
                    <?php } ?>
                </div>
                <?php
                break;

            case 'change_password':
                ?>
                <div class="form-container">
                    <h1>Cambiar Contraseña</h1>
                    <form action="" method="POST">
                        <label for="new_password">Nueva Contraseña:</label>
                        <input type="password" id="new_password" name="new_password" class="input" required>
                        <button type="submit" name="change_password" class="button">Cambiar Contraseña</button>
                    </form>
                    <?php if (isset($password_message)) { ?>
                        <p><?php echo $password_message; ?></p>
                    <?php } ?>
                    <?php if (isset($password_error)) { ?>
                        <p><?php echo $password_error; ?></p>
                    <?php } ?>
                </div>
                <?php
                break;

            case 'disable':
                ?>
                <div class="form-container">
                    <h1>Inhabilitar Cuenta</h1>
                    <form action="" method="POST">
                        <p>Si desactiva su cuenta, no podrá acceder a ella hasta que sea reactivada.</p>
                        <button type="submit" name="disable" class="button">Inhabilitar Cuenta</button>
                    </form>
                    <?php if (isset($disable_error)) { ?>
                        <p><?php echo $disable_error; ?></p>
                    <?php } ?>
                </div>
                <?php
                break;

            case 'delete':
                ?>
                <div class="form-container">
                    <h1>Eliminar Cuenta</h1>
                    <form action="" method="POST">
                        <p>Esta acción eliminará permanentemente su cuenta. No podrá recuperarla.</p>
                        <button type="submit" name="delete" class="button">Eliminar Cuenta</button>
                    </form>
                    <?php if (isset($delete_error)) { ?>
                        <p><?php echo $delete_error; ?></p>
                    <?php } ?>
                </div>
                <?php
                break;

            case 'Ped':
                ?>
                <div class="form-container">
                    <h1>Pedidos</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($orders) && !empty($orders)) {
                                foreach ($orders as $order) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($order['CodPed']) . "</td>";
                                    echo "<td>" . htmlspecialchars($order['FecPed']) . "</td>";
                                    echo "<td>" . htmlspecialchars($order['EstPed']) . "</td>";
                                    echo "<td>" . number_format($order['TotPed']) . " COP</td>";
                                    echo "<td><a href='../Controlador/pedido_confirmado.php?CodPed=" . htmlspecialchars($order['CodPed']) . "' class='' style='color: #fff;'><i class='fas fa-info-circle'></i></a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No hay pedidos para mostrar.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                break;

            default:
                ?>
                <div class="form-container">
                    <h1>Información de la Cuenta</h1>
                    <p>Nombre: <?php echo htmlspecialchars($user['NomUsu']); ?></p>
                    <p>Apellido: <?php echo htmlspecialchars($user['ApeUsu']); ?></p>
                    <p>Correo: <?php echo htmlspecialchars($user['CorUsu']); ?></p>
                    <p>Estado: <?php echo htmlspecialchars($user['EstUsu']); ?></p>
                </div>
                <?php
                break;
        }
        ?>
    </div>
</body>
</html>
