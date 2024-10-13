<?php
session_start();
include './Modelo/db_connection.php';

$db = new Database();
$conn = $db->getConnection();

$searchTerm = '';
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
}

// Consulta para obtener los 4 productos más pedidos
$sql = "SELECT p.CodPro, p.NomPro, p.PrePro, p.FotPro
        FROM Producto p
        JOIN (
            SELECT CodPro, COUNT(*) as cantidad
            FROM detallePedido
            GROUP BY CodPro
            ORDER BY cantidad DESC
            LIMIT 4
        ) dp ON p.CodPro = dp.CodPro";

if ($searchTerm != '') {
    $sql .= " AND p.NomPro LIKE :searchTerm";
}

$stmt = $conn->prepare($sql);
if ($searchTerm != '') {
    $searchTerm = '%' . $searchTerm . '%';
    $stmt->bindParam(':searchTerm', $searchTerm);
}
$stmt->execute();
$productosDestacados = $stmt->fetchAll(PDO::FETCH_ASSOC);

$conn = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./aseets/css/style.css">
    <title>Ranchenato</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="./aseets/js/scripts.js">
    <style>
        .manual {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            position: fixed;
            bottom: 0;
            left: 0;
            z-index: 1000;
        }

        a {
            text-decoration: none;
        }

        .img-manual {
            border-radius: 100%;
            margin-right: 100px;
        }

        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .product-item {
            background: url(./Vistas/Imagenes/fondobeige.jpg);
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
            color: #000;
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
</head>
<body style="background: url(./Vistas/Imagenes/fondo.jpg);">
    <header>
        <a href="index.php" class="logo" style="color: white;">
            <img width="100px" style="padding: 15px; border-radius: 100%;" src="./Vistas/Imagenes/logosinfondo.png" alt="logo">
            <h2 class="nombre">Ranchenato</h2>
        </a>
        <nav class="links">
            <?php if (isset($_SESSION['NomUsu']) && isset($_SESSION['ApeUsu'])): ?>
                <a style="color: white;" href="./Vistas/nosotros.php" class="link">Nosotros</a>
                &nbsp;
                <a style="color: white;" href="./Vistas/contacto.php" class="link">Contacto</a>
                &nbsp;
                <a style="color: white;" href="./Vistas/producto.php" class="link">Productos</a>
                &nbsp;
                <a href="./Controlador/micuenta.php" style="color: white;" class="link"><?php echo $_SESSION['NomUsu'] . ' ' . $_SESSION['ApeUsu']; ?></a>
                &nbsp;
                <?php if (isset($_SESSION['CodRol']) && $_SESSION['CodRol'] == 2): ?>
                    <a style="color: white;" href="./Vistas/home_admin.php" class="link">Administración</a>
                    &nbsp;
                <?php endif; ?>
                <?php if (isset($_SESSION['CodRol']) && $_SESSION['CodRol'] == 3): ?>
                    <a style="color: white;" href="./Vistas/domiciliario.php" class="link">Domicilios</a>
                    &nbsp;
                <?php endif; ?>
                
                <a style="color: white;" href="./Vistas/carrito.php" class="link3"><i class="fas fa-shopping-cart"></i></a>
            <?php else: ?>
                <a style="color: white;" href="./Vistas/nosotros.php" class="link">Nosotros</a>
                &nbsp;
                <a style="color: white;" href="./Vistas/contacto.php" class="link">Contacto</a>
                &nbsp;
                <a style="color: white;" href="./Vistas/producto.php" class="link">Productos</a>
                &nbsp;
                <a style="color: white;" href="./Vistas/login.php" class="link">Iniciar sesión</a>
                &nbsp;
            <?php endif; ?>
        </nav>
    </header>
    <center><h1 style="color: white;">NUESTRO CATÁLOGO DESTACADO</h1></center>
    <br>
    <div class="container">
        <div class="box box-1" data-text="Vinos">
            <a href="./Vistas/catalogo.php?catpro=Vinos"><img src="./Vistas/Imagenes/vino.jpg" width="250px" class="img"></a>
        </div>
        <div class="box box-2" data-text="Whisky">
            <a href="./Vistas/catalogo.php?catpro=Whisky"><img src="./Vistas/Imagenes/whisky.jpg" width="250px" class="img"></a>
        </div>
        <div class="box box-3" data-text="Vodka">
            <a href="./Vistas/catalogo.php?catpro=Vodka"><img src="./Vistas/Imagenes/vodka.jpg" width="250px" class="img"></a>
        </div>
        <div class="box box-4" data-text="Ron">
            <a href="./Vistas/catalogo.php?catpro=Ron"><img src="./Vistas/Imagenes/ron.jpg" width="250px" class="img"></a>
        </div>
        <div class="box box-5" data-text="Tequila">
            <a href="./Vistas/catalogo.php?catpro=Tequila"><img src="./Vistas/Imagenes/tequila.jpg" width="250px" class="img"></a>
        </div>
    </div>
    <br><br><br><br><br><br><br>
    <div class="product">
        <center><h1 style="color: white;">PRODUCTOS DESTACADOS</h1></center>
        <div class="product-list">
            <?php
            if (count($productosDestacados) > 0) {
                foreach ($productosDestacados as $row) {
                    $imgPath = './uploads/' . $row['FotPro']; 

                    if (!file_exists($imgPath)) {
                        $imgPath = './Vistas/Imagenes/placeholder.png'; 
                    }

                    echo '<div class="product-item">';
                    echo '<img src="' . htmlspecialchars($imgPath) . '" alt="' . htmlspecialchars($row['NomPro']) . '" width="300px" height="300px">';
                    echo '<h3>' . htmlspecialchars($row['NomPro']) . '</h3>';
                    echo '<p>$' . number_format($row['PrePro']) . ' COP</p>';
                    echo '<a href="./Controlador/detalle_producto.php?CodPro=' . $row['CodPro'] . '" class="btn"><i class="fas fa-info-circle"></i> Ver detalles</a>';
                    echo '<a href="./Controlador/añadir_carrito.php?CodPro=' . $row['CodPro'] . '" class="btn"><i class="fas fa-shopping-cart"></i> Añadir al carrito</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>No se encontraron productos destacados.</p>';
            }
            ?>
        </div>
    </div>
    <footer class="manual">
        <a href="Manual de usuario.pdf"><img class="img-manual" src="./Vistas/Imagenes/pregunta.png" width="80px" alt="manual"></a>
    </footer>
    
</body>
</html>
