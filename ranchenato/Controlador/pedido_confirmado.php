<?php
session_start();
include '../Modelo/db_connection.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['CodUsu'])) {
    header("Location: ../Vistas/login.php");
    exit();
}

// Obtener el código del pedido desde la URL
$CodPed = isset($_GET['CodPed']) ? intval($_GET['CodPed']) : null;
if (!$CodPed) {
    die("No se especificó el código del pedido.");
}

// Crear una instancia de la clase Database y obtener la conexión
$database = new Database();
$conn = $database->getConnection();

// Verificar si la conexión se realizó correctamente
if ($conn === null) {
    die('No se pudo conectar a la base de datos.');
}

// Consultar detalles del pedido y dirección de domicilio
$sql_pedido = "SELECT p.CodPed, p.FecPed, p.TotPed, p.EstPed, d.DesDom, d.DirDom 
               FROM Pedido p
               LEFT JOIN Domicilio d ON p.CodPed = d.CodPed
               WHERE p.CodPed = ?";
$stmt_pedido = $conn->prepare($sql_pedido);

if ($stmt_pedido === false) {
    die('Error en prepare para obtener detalles del pedido: ' . implode(', ', $conn->errorInfo()));
}

$stmt_pedido->execute([$CodPed]);
$result_pedido = $stmt_pedido->fetch(PDO::FETCH_ASSOC);

if (!$result_pedido) {
    die("El pedido especificado no existe o no tiene acceso.");
}

$pedido = $result_pedido;

// Consultar productos asociados al pedido
$sql_productos = "SELECT dp.CodDpe, dp.CodPro, dp.CanPro, dp.PrePro, dp.SutPed, pr.NomPro 
                  FROM DetallePedido dp
                  INNER JOIN Producto pr ON dp.CodPro = pr.CodPro
                  WHERE dp.CodPed = ?";
$stmt_productos = $conn->prepare($sql_productos);

if ($stmt_productos === false) {
    die('Error en prepare para obtener productos del pedido: ' . implode(', ', $conn->errorInfo()));
}

$stmt_productos->execute([$CodPed]);
$result_productos = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);

// Verificar si ya existe una dirección de envío para este pedido
$direccion_existente = !empty($pedido['DesDom']) && !empty($pedido['DirDom']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos específicos para esta página */
        body {
            margin: 0px;
            font-family: 'Gill Sans', 'Gill Sans MT', 'Trebuchet MS', sans-serif;
            background: url(../Vistas/Imagenes/background.jpg);
            color: #fff;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px;
            padding: 10px;
        }
        .logo {
            display: flex;
            align-items: center;
            margin-right: 10px;
        }
        a {
            text-decoration: none;
            color: white;
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
        .container {
            background: url(../Vistas/Imagenes/fondobeige.jpg);
            border: 1px solid #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 800px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .pedido-info {
            margin-bottom: 30px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 20px;
        }
        .pedido-info ul {
            list-style-type: none;
            padding: 0;
        }
        .pedido-info ul li {
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        .productos-list {
            margin-top: 20px;
        }
        .productos-list ul {
            list-style-type: none;
            padding: 0;
        }
        .productos-list ul li {
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        .direccion-form {
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 20px;
        }
        .direccion-form label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .direccion-form input[type="text"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }
        .direccion-form button {
            backdrop-filter: blur(15px);
            border: 1px white solid;
            padding: 10px;
            border-radius: 10px;
            filter: opacity(50%);
        }
        .direccion-form button:hover {
            filter: opacity(100%);
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 0.9rem;
        }
    </style>
    <title>Confirmación del Pedido</title>
</head>
<body style="background: url(../Vistas/Imagenes/fondo.jpg);">
    <header>
        <a href="../index.php" class="logo" style="color: white;">
            <img width="100px" style="padding: 15px; border-radius: 100%;" src="../Vistas/Imagenes/logosinfondo.png"  alt="logo">
            <h2 class="nombre">Ranchenato</h2>
        </a>
        <nav class="links">
            <?php if (isset($_SESSION['NomUsu']) && isset($_SESSION['ApeUsu'])): ?>
                <a style="color: white;" href="../Vistas/contacto.php" class="link">Contacto</a>
                &nbsp;
                <a style="color: white;" href="../Vistas/producto.php" class="link">Productos</a>
                &nbsp;
                <a href="../Controlador/micuenta.php" style="color: white;" href="../Vistas/contacto.php" class="link"><?php echo $_SESSION['NomUsu'] . ' ' . $_SESSION['ApeUsu']; ?></a>
                &nbsp;
                <?php if (isset($_SESSION['CodRol']) && $_SESSION['CodRol'] == 2): ?>
                    <a style="color: white;" href="../Vistas/home_admin.php" class="link">Administración</a>
                    &nbsp;
                <?php endif; ?>
                <a style="color: white;" href="../Vistas/carrito.php" class="link3"><i class="fas fa-shopping-cart"></i></a>
            <?php else: ?>
                <a style="color: white;" href="../Vistas/contacto.php" class="link">Contacto</a>
                &nbsp;
                <a style="color: white;" href="../Vistas/producto.php" class="link">Productos</a>
                &nbsp;
                <a style="color: white;" href="../Vistas/login.php" class="link">Iniciar sesión</a>
                &nbsp;
            <?php endif; ?>
        </nav>
    </header>
<div class="container">
    <div class="header">
        <h1>Confirmación del Pedido</h1>
        <p>¡Gracias por tu pedido!</p>
    </div>
    
    <div class="pedido-info">
        <h2>Información del Pedido</h2>
        <ul>
            <li><strong>Código del Pedido:</strong> <?php echo htmlspecialchars($pedido['CodPed']); ?></li>
            <li><strong>Fecha del Pedido:</strong> <?php echo htmlspecialchars($pedido['FecPed']); ?></li>
            <li><strong>Total del Pedido:</strong> $<?php echo number_format($pedido['TotPed']); ?></li>
            <li><strong>Estado del Pedido:</strong> <?php echo htmlspecialchars($pedido['EstPed']); ?></li>
        </ul>
    </div>

    <div class="productos-list">
        <h2>Productos del Pedido</h2>
        <ul>
            <?php foreach ($result_productos as $producto): ?>
                <li>
                    <strong><?php echo htmlspecialchars($producto['NomPro']); ?></strong> -
                    Cantidad: <?php echo $producto['CanPro']; ?> -
                    Precio: $<?php echo number_format($producto['PrePro']); ?> -
                    Subtotal: $<?php echo number_format($producto['SutPed']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php if ($direccion_existente): ?>
        <div class="pedido-info">
            <h2>Dirección de Envío</h2>
            <ul>
                <li><strong>Descripción del Domicilio:</strong> <?php echo htmlspecialchars($pedido['DesDom']); ?></li>
                <li><strong>Dirección de Envío:</strong> <?php echo htmlspecialchars($pedido['DirDom']); ?></li>
            </ul>
        </div>
    <?php else: ?>
        <div class="direccion-form">
            <h2>Ingrese la Dirección de Envío</h2>
            <form action="procesar_direccion.php" method="POST">
                <input type="hidden" name="CodPed" value="<?php echo htmlspecialchars($CodPed); ?>">
                <label for="des_dom">Descripción del Domicilio:</label>
                <input type="text" id="des_dom" name="des_dom" required><br><br>
                
                <label for="dir_dom">Dirección de Envío:</label>
                <input type="text" id="dir_dom" name="dir_dom" required><br><br>

                <button type="submit">Confirmar Pedido</button>
            </form>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

<?php
$conn = null;
?>
