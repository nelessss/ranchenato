<?php

include_once '../Modelo/db_connection.php';

session_start();
if (!isset($_SESSION['CodUsu'])) {
    header("Location: ../Vistas/login.php");
    exit();
}

$CodUsu = $_SESSION['CodUsu'];

if ($CodUsu != 3){
    header("location: ../index.php");
}

$database = new Database();
$db = $database->getConnection();

if (isset($_POST['actualizar'])) {
    $CodDom = $_POST['CodDom'];

    $query = "UPDATE domicilio SET EstDom = 'En camino', CodUsu = :CodUsu WHERE CodDom = :CodDom";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':CodUsu', $CodUsu);
    $stmt->bindParam(':CodDom', $CodDom);
    if ($stmt->execute()) {
        echo "El pedido ha sido actualizado a 'En camino'.";
    } else {
        echo "Error al actualizar el pedido.";
    }
}

$query = "SELECT CodDom, EstDom, DesDom, DirDom FROM domicilio WHERE EstDom = 'Pendiente'";
$stmt = $db->prepare($query);
$stmt->execute();
$domiciliosPendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../aseets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Domiciliario - Pedidos Pendientes</title>
    <style>
        body {
            margin: 0;
            font-family: 'Gill Sans', 'Gill Sans MT', 'Trebuchet MS', sans-serif;
            background: url(../Vistas/Imagenes/fondo.jpg) no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            border-radius: 10px;
            color: white;
            margin-bottom: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            width: 100px;
            padding: 15px;
            border-radius: 100%;
        }

        .nombre {
            margin-left: 10px;
            font-size: 24px;
            font-weight: bold;
        }

        nav {
            display: flex;
            gap: 10px;
        }

        .link, .link3 {
            backdrop-filter: blur(15px);
            border: 1px solid white;
            padding: 10px;
            border-radius: 10px;
            color: white;
            transition: opacity 0.3s ease;
        }

        .link:hover, .link3:hover {
            opacity: 1;
        }

        .link3 {
            padding: 10px;
            border-radius: 10px;
            display: flex;
            align-items: center;
        }

        .link3 i {
            margin-right: 5px;
        }

        .contenedor {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            margin: 20px auto;
            width: 50%;
            padding: 20px;
            background: url("../Vistas/Imagenes/fondobeige.jpg");
            border-radius: 10px;
            color: #000;
        }

        .pedido-contenedor {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            padding: 15px;
            margin: 10px 0;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .pedido-acciones {
            display: flex;
            justify-content: center;
        }

        .pedido-acciones button {
            background: url("../Vistas/Imagenes/fondo.jpg");
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .pedido-acciones button:hover {
            opacity: 40%;
        }
    </style>
</head>
<body>
    <header>
        <a href="domiciliario.php" class="logo" style="color: white;">
            <img width="100px" style="padding: 15px; border-radius: 100%;" src="../Vistas/Imagenes/logosinfondo.png" alt="logo">
        </a>
        <h2>Bienvenid@ <?php echo $_SESSION['NomUsu'] . ' ' . $_SESSION['ApeUsu']; ?></h2>
        <nav class="links">
                <a style="color: white;" href="../Vistas/pedidos_dom.php" class="link">Pedidos Realizados</a>
                &nbsp;
                <a style="color: white;" href="../Controlador/micuenta.php" class="link">Mi Cuenta</a>
                &nbsp;
        </nav>
    </header>
    <div class="contenedor">
        <h1>Pedidos Pendientes</h1>
        <?php if (!empty($domiciliosPendientes)): ?>
            <?php foreach ($domiciliosPendientes as $domicilio): ?>
                <div class="pedido-contenedor">
                    <div class="pedido-info">
                        <h3>Pedido ID: <?php echo htmlspecialchars($domicilio['CodDom']); ?></h3>
                        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($domicilio['DesDom']); ?></p>
                        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($domicilio['DirDom']); ?></p>
                        <p><strong>Estado:</strong> <?php echo htmlspecialchars($domicilio['EstDom']); ?></p>
                    </div>
                    <div class="pedido-acciones">
                        <a href="domicilio_detalle.php?CodDom=<?php echo htmlspecialchars($domicilio['CodDom']); ?>">
                            <button>Entregar Pedido</button>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay pedidos disponibles</p>
        <?php endif; ?>
    </div>
    <script src="../aseets/js/script.js"></script>
</body>
</html>
