<?php
session_start();
include '../Modelo/db_connection.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['CodUsu'])) {
    header("Location: ../Vistas/login.php");
    exit();
}

$CodUsu = $_SESSION['CodUsu'];

// Establece la conexión con PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=proyectobar", "root", ""); // Ajusta los parámetros de conexión
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los productos en el carrito del usuario
    $sql = "SELECT p.CodPro, p.NomPro, p.PrePro, c.Cantidad, p.FotPro 
            FROM Carrito c 
            JOIN Producto p ON c.CodPro = p.CodPro 
            WHERE c.CodUsu = :CodUsu";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['CodUsu' => $CodUsu]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Carrito de Compras</title>
    <style>
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
            backdrop-filter: blur(15px);
            border: 1px solid #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 800px;
        }
        .producto {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .producto img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 20px;
        }
        .producto-info {
            flex: 1;
        }
        .btn-eliminar {
            color: red;
        }

        .pedir{
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body style="background: url(../Vistas/Imagenes/fondo.jpg);">
    <header>
        <a href="../index.php" class="logo" style="color: white;">
            <img width="100px" style="padding: 15px; border-radius: 100%;" src="../Vistas/Imagenes/logosinfondo.png" alt="logo">
            <h2 class="nombre">Ranchenato</h2>
        </a>
        <nav class="links">
            <?php if (isset($_SESSION['NomUsu']) && isset($_SESSION['ApeUsu'])): ?>
                <a style="color: white;" href="../Vistas/nosotros.php" class="link">Nosotros</a>
                &nbsp;
                <a style="color: white;" href="../Vistas/contacto.php" class="link">Contacto</a>
                &nbsp;
                <a style="color: white;" href="../Vistas/producto.php" class="link">Productos</a>
                &nbsp;
                <a href="../Controlador/micuenta.php" style="color: white;" class="link"><?php echo htmlspecialchars($_SESSION['NomUsu'] . ' ' . $_SESSION['ApeUsu']); ?></a>
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
        <h1>Carrito de Compras</h1>
        <?php if (count($productos) > 0): ?>
            <?php foreach ($productos as $producto): ?>
                <div class="producto">
                    <img src="../uploads/<?php echo htmlspecialchars($producto['FotPro']); ?>" alt="<?php echo htmlspecialchars($producto['NomPro']); ?>" style="background: #fff; border-radius: 10px;">
                    <div class="producto-info">
                        <h2><?php echo htmlspecialchars($producto['NomPro']); ?></h2>
                        <p>Precio: $<?php echo number_format($producto['PrePro']); ?></p>
                        <p>Cantidad: <?php echo htmlspecialchars($producto['Cantidad']); ?></p>
                    </div>
                    <a href="../Controlador/eliminar_del_carrito.php?CodPro=<?php echo htmlspecialchars($producto['CodPro']); ?>" class='btn-eliminar btn-delete'><i class='fas fa-trash-alt'></i></a>
                </div>
            <?php endforeach; ?>
            <form action="../Controlador/confirmacion_pedido.php" method="post" class="pedir">
                <a href=""></a>
                <a href=""></a>
                <button type="submit" class="link">Pedir</button>
            </form>
            <a href="../Vistas/producto.php" class="link" style="color: #fff;">Regresar</a>
        <?php else: ?>
            <p>No hay productos en el carrito.</p>
            <br>
            <a href="../Vistas/producto.php" class="link" style="color: #fff;">Productos</a>
        <?php endif; ?>
    </div>
</body>
</html>
