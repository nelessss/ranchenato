<?php
session_start();
include '../Modelo/db_connection.php';

// Obtener el catálogo de la URL
$catpro = isset($_GET['catpro']) ? $_GET['catpro'] : '';

// Establecer conexión con PDO
$db = new Database();
$pdo = $db->getConnection();

// Consultar los productos que pertenecen al catálogo
$sql = "SELECT CodPro, NomPro, PrePro, FotPro FROM Producto WHERE CatPro = :catpro";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':catpro', $catpro, PDO::PARAM_STR);

try {
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al ejecutar la consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../aseets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .product-item {
            background: url(../Vistas/Imagenes/fondobeige.jpg);
            border-radius: 10px;
            padding: 20px;
            margin: 20px;
            text-align: center;
            width: 200px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            color: #000;
            border: 1px solid #fff;
        }
        .product-item img {
            background-color: #fff;
            border-radius: 10px;
            margin-bottom: 10px;
            max-width: 100%;
            height: auto;
        }
        .product-item h3 {
            margin: 10px 0;
        }
        .product-item p {
            font-size: 18px;
            color: #fff;
        }
        .product-item .btn {
            display: block;
            margin: 10px 0;
            padding: 10px;
            backdrop-filter: blur(15px);
            color: #000;
            text-decoration: none;
            border-radius: 5px;
        }
        .product-item .btn i {
            margin-right: 5px;
        }
        .product-item .btn:hover {
            backdrop-filter: blur(25px);
        }
    </style>
    <title>Productos - <?php echo htmlspecialchars($catpro); ?></title>
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
                <a href="../Controlador/micuenta.php" style="color: white;" class="link"><?php echo $_SESSION['NomUsu'] . ' ' . $_SESSION['ApeUsu']; ?></a>
                &nbsp;
                <?php if (isset($_SESSION['CodRol']) && $_SESSION['CodRol'] == 2): ?>
                    <a style="color: white;" href="../Vistas/home_admin.php" class="link">Administración</a>
                    &nbsp;
                <?php endif; ?>
                <a style="color: white;" href="../Vistas/carrito.php" class="link3"><i class="fas fa-shopping-cart"></i></a>
            <?php else: ?>
                <a style="color: white;" href="../Vistas/nosotros.php" class="link">Nosotros</a>
                &nbsp;
                <a style="color: white;" href="../Vistas/contacto.php" class="link">Contacto</a>
                &nbsp;
                <a style="color: white;" href="../Vistas/producto.php" class="link">Productos</a>
                &nbsp;
                <a style="color: white;" href="../Vistas/login.php" class="link">Iniciar sesión</a>
                &nbsp;
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <center><h1 style="color: #fff;">Productos - <?php echo htmlspecialchars($catpro); ?></h1></center>
        <div class="product-list">
            <?php if ($products && count($products) > 0): ?>
                <?php foreach($products as $row): ?>
                    <div class="product-item">
                        <img src="<?php echo htmlspecialchars($row['FotPro']); ?>" alt="">
                        <h2><?php echo htmlspecialchars($row['NomPro']); ?></h2>
                        <p style="color: #000;">Precio: <?php echo number_format($row['PrePro']); ?> COP</p>
                        <a href="../Controlador/detalle_producto.php?CodPro=<?php echo htmlspecialchars($row['CodPro']); ?>" class="btn"><i class="fas fa-info-circle"></i> Ver detalles</a>
                        <a href="../Controlador/añadir_carrito.php?CodPro=<?php echo htmlspecialchars($row['CodPro']); ?>" class="btn"><i class="fas fa-shopping-cart"></i> Añadir al carrito</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No se encontraron productos en esta categoría.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
