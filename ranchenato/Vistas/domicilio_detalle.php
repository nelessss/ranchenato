<?php
// Incluir el archivo de conexión a la base de datos
include_once '../Modelo/db_connection.php';

session_start();
if (!isset($_SESSION['CodUsu'])) {
    header("Location: ../Vistas/login.php");
    exit();
}

// Obtener el código del pedido desde la URL
$CodDom = isset($_GET['CodDom']) ? intval($_GET['CodDom']) : null;

if (!$CodDom) {
    die('No se especificó el código del pedido.');
}

// Obtener el código del usuario (domiciliario) desde la sesión
$CodUsu = $_SESSION['CodUsu'];

// Crear la conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Consultar la información del domicilio
$query = "SELECT CodDom, EstDom, DesDom, DirDom, CodPed FROM domicilio WHERE CodDom = :CodDom";
$stmt = $db->prepare($query);
$stmt->bindParam(':CodDom', $CodDom);
$stmt->execute();
$domicilio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$domicilio) {
    die('No se encontró el domicilio especificado.');
}

// Si se presiona el botón para marcar como entregado
if (isset($_POST['entregar'])) {
    $CodDom = $_POST['CodDom'];
    $CodPed = $_POST['CodPed'];

    // Actualizar el estado del domicilio a 'Entregado' y asignar el CodUsu
    $query_actualizar_domicilio = "UPDATE domicilio SET EstDom = 'Entregado', CodUsu = :CodUsu WHERE CodDom = :CodDom";
    $stmt_actualizar_domicilio = $db->prepare($query_actualizar_domicilio);
    $stmt_actualizar_domicilio->bindParam(':CodDom', $CodDom);
    $stmt_actualizar_domicilio->bindParam(':CodUsu', $CodUsu);
    
    // Actualizar el estado del pedido a 'Entregado'
    $query_actualizar_pedido = "UPDATE pedido SET EstPed = 'Entregado' WHERE CodPed = :CodPed";
    $stmt_actualizar_pedido = $db->prepare($query_actualizar_pedido);
    $stmt_actualizar_pedido->bindParam(':CodPed', $CodPed);

    if ($stmt_actualizar_domicilio->execute() && $stmt_actualizar_pedido->execute()) {
        echo "El pedido ha sido marcado como 'Entregado'.";
        header("Location: domiciliario.php");
        exit();
    } else {
        echo "Error al actualizar el estado del pedido.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Pedido</title>
    <link rel="stylesheet" href="../aseets/css/style.css">
    <style>

        .contenedor {
            width: 400px;
            background: url(../Vistas/Imagenes/fondobeige.jpg);
            border: 0.5px white solid;
            margin: auto;
            margin-top: 100px;
            padding: 20px;
            border-radius: 10px;
            color: #000;
        }

        .input {
            width: 95%;
            background: transparent;
            padding: 10px;
            border-radius: 2px;
            border: none;
            border-bottom: 2px solid white;
            color: white;
        }

        .input::placeholder {
            color: white;
            filter: opacity(70%);
        }

        .boton {
            width: 30%;
            background: transparent;
            border-radius: 10px;
            padding: 10px;
            border: 1px solid #000;
            filter: opacity(100%);
            color: #000;
        }

        .boton:hover {
            filter: opacity(50%);
        }

        h1 {
            text-align: center;
        }

        p {
            font-size: 18px;
            margin: 10px 0;
        }

        form{
            border: none;
            backdrop-filter: blur(0);
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body style="background: url('../Vistas/Imagenes/fondo.jpg');">
    <div class="contenedor">
        <h1>Detalle del Pedido #<?php echo $domicilio['CodDom']; ?></h1>
        <p><strong>Descripción:</strong> <?php echo $domicilio['DesDom']; ?></p>
        <p><strong>Dirección:</strong> <?php echo $domicilio['DirDom']; ?></p>
        <p><strong>Estado:</strong> <?php echo $domicilio['EstDom']; ?></p>
        <?php
        if ($domicilio['EstDom']=='Entregado'){
            ?>
            <a href="./domiciliario.php">Regresar</a>
            <?php
        }else{
        ?>
        <form method="POST" action="domicilio_detalle.php?CodDom=<?php echo $domicilio['CodDom']; ?>" class="form">
            <input type="hidden" name="CodDom" value="<?php echo $domicilio['CodDom']; ?>">
            <input type="hidden" name="CodPed" value="<?php echo $domicilio['CodPed']; ?>">
            <center><button type="submit" class="boton" name="entregar">Marcar como Entregado</button></center>
        </form>
        <?php
        }
        ?>
    </div>
</body>
</html>
