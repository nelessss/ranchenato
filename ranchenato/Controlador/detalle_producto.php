<?php
session_start();
include '../Modelo/db_connection.php';

// Verificar si se proporciona un ID de producto válido en la URL
if (!isset($_GET['CodPro'])) {
    header("Location: ../index.php");
    exit();
}

$CodPro = $_GET['CodPro'];

// Establecer conexión con PDO
$db = new Database();
$pdo = $db->getConnection();

// Obtener los detalles del producto desde la base de datos
$sql = "SELECT * FROM Producto WHERE CodPro = :CodPro";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':CodPro', $CodPro, PDO::PARAM_INT);

try {
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontró el producto
    if (!$row) {
        header("Location: ../index.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error al ejecutar la consulta: " . $e->getMessage());
}

$stmt->closeCursor();
$pdo = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Detalles del Producto</title>
    <style>
        body {
            margin: 0px;
            font-family: 'Gill Sans', 'Gill Sans MT', 'Trebuchet MS', sans-serif;
            background: url(../Vistas/Imagenes/fondobeige.jpg);
            color: #fff;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px;
        }
        .logo {
            display: flex;
            align-items: center;
            margin-right: 10px;
        }
        a {
            text-decoration: none;
            color: #fff;
        }
        .logo img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .nombre {
            font-size: 24px;
            margin: 0;
        }
        nav {
            padding-right: 10px;
        }
        .link {
            backdrop-filter: blur(15px);
            border: 1px white solid;
            padding: 10px;
            border-radius: 10px;
            filter: opacity(50%);
        }
        .link:hover {
            filter: opacity(100%);
        }
        .detalle-producto {
            background: url(../Vistas/Imagenes/fondo.jpg);
            border: 1px solid #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 50px auto;
            max-width: 800px;
            display: flex;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .detalle-img {
            flex: 0 0 400px;
            margin-right: 20px;
            border-radius: 10px;
            overflow: hidden;
        }
        .detalle-img img {
            width: 100%;
            height: auto;
            background-color: #fff;
        }
        .detalle-info {
            flex: 1;
        }
        .detalle-info h2 {
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .detalle-info p {
            margin-bottom: 10px;    
        }
        .btn {
            cursor: pointer;
            text-decoration: none;
            color: #fff;
            transition: background-color 0.3s ease;
            border: 1px white solid;
            padding: 10px;
            border-radius: 10px;
            filter: opacity(50%);
        }
        .btn:hover {
            filter: opacity(100%);
        }
    </style>
</head>
<body>
<div class="detalle-producto">
    <div class="detalle-img">
        <img src="../uploads/<?php echo htmlspecialchars($row['FotPro']); ?>" alt="<?php echo htmlspecialchars($row['NomPro']); ?>">
    </div>
    <div class="detalle-info">
        <h2><?php echo htmlspecialchars($row['NomPro']); ?></h2>
        <p><?php echo htmlspecialchars($row['DesPro']); ?></p>
        <p>Precio: $<?php echo number_format($row['PrePro']); ?> COP</p>
        <br>
        <?php if (isset($_SESSION['NomUsu']) && isset($_SESSION['ApeUsu'])): ?>
            <a href="../Controlador/añadir_carrito.php?CodPro=<?php echo htmlspecialchars($CodPro); ?>" class="btn">Añadir al carrito</a>
        <?php else: ?>
            <p>Inicia sesión para añadir al carrito</p>
        <?php endif; ?>
    </div>
</div>
<center>
    <a href="javascript:history.back()" class="link" style="color: #000; border: #000;">Regresar</a>
</center>
</body>
</html>
