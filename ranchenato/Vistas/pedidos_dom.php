<?php
include_once "../Modelo/db_connection.php";

session_start();
if (!isset($_SESSION['CodUsu'])) {
    header("Location: ../Vistas/login.php");
    exit();
}

$CodUsu = $_SESSION['CodUsu'];

if ($CodUsu != 3) {
    header("location: ../index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$sqlDomicilios = "SELECT d.CodDom, d.DesDom, d.DirDom, d.EstDom, p.CodPed
                  FROM domicilio d
                  JOIN Pedido p ON d.CodPed = p.CodPed
                  WHERE d.CodUsu = :CodUsu";

$stmtDomicilios = $db->prepare($sqlDomicilios);
$stmtDomicilios->bindParam(':CodUsu', $CodUsu, PDO::PARAM_INT);
$stmtDomicilios->execute();
$domicilios = $stmtDomicilios->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos Realizados</title>
    <link rel="stylesheet" href="../aseets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Gill Sans', 'Gill Sans MT', 'Trebuchet MS', sans-serif;
            background: url(../Vistas/Imagenes/fondo.jpg) no-repeat center center fixed;
            background-size: cover;
            color: #fff;
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
    <div class="contenedor">
        <?php foreach ($domicilios as $domicilio): ?>
            <div class="pedido-contenedor" style="color: #000;">
                <div class="pedido-info">
                    <h3>Pedido ID: <?php echo htmlspecialchars($domicilio['CodPed']); ?></h3>
                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($domicilio['DesDom']); ?></p>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($domicilio['DirDom']); ?></p>
                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($domicilio['EstDom']); ?></p>
                </div>
                <div class="pedido-acciones">
                    <a href="domicilio_detalle.php?CodDom=<?php echo htmlspecialchars($domicilio['CodDom']); ?>">
                        <button>Ver Pedido</button>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
