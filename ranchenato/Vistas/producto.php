<?php
session_start();
include '../Modelo/db_connection.php';

// Crear una instancia de la clase Database
$db = new Database();
$conn = $db->getConnection();

// Procesar el formulario de búsqueda
$searchTerm = '';
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranchenato</title>
    <link rel="stylesheet" href="../aseets/css/producto.css">
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
        .search-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 20px;
        }
        .search-form {
            border-radius: 5px;
            padding: 5px;
        }
        .search-form input[type="text"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-right: 10px;
            width: 250px;
        }
        .search-form button {
            padding: 10px;
            background-color: #fff;
            border-radius: 10px;
            border: none;
            cursor: pointer;
        }
        .search-form button i {
            color: #555;
        }
        .search-form button:hover i {
            color: #333;
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
    <div class="search-container">
        <form action="" method="post" class="search-form">
            <input type="text" name="search" placeholder="Buscar productos...">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <br>
    <div class="productos">
        <div class="product-list">
            <?php
            // Preparar la consulta para obtener los productos con búsqueda
            $sql = "SELECT CodPro, NomPro, PrePro, FotPro FROM Producto";
            if ($searchTerm != '') {
                $sql .= " WHERE NomPro LIKE :searchTerm";
            }
            $stmt = $conn->prepare($sql);
            if ($searchTerm != '') {
                $searchTerm = '%' . $searchTerm . '%';
                $stmt->bindParam(':searchTerm', $searchTerm);
            }
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Verificar si hay resultados
            if (count($result) > 0) {
                foreach ($result as $row) {
                    $imgPath = '../uploads/' . $row['FotPro']; // Ajusta la ruta según tu estructura de archivos

                    // Comprobar si la imagen existe
                    if (!file_exists($imgPath)) {
                        $imgPath = '../Vistas/Imagenes/placeholder.png'; // Ruta a una imagen placeholder en caso de que la imagen no exista
                    }

                    // Mostrar información del producto
                    echo '<div class="product-item">';
                    echo '<img src="' . htmlspecialchars($imgPath) . '" alt="' . htmlspecialchars($row['NomPro']) . '" width="300px" height="300px">';
                    echo '<h3>' . htmlspecialchars($row['NomPro']) . '</h3>';
                    echo '<p>$' . number_format($row['PrePro']) . ' COP</p>';
                    echo '<a href="../Controlador/detalle_producto.php?CodPro=' . $row['CodPro'] . '" class="btn"><i class="fas fa-info-circle"></i> Ver detalles</a>';
                    echo '<a href="../Controlador/añadir_carrito.php?CodPro=' . $row['CodPro'] . '" class="btn"><i class="fas fa-shopping-cart"></i> Añadir al carrito</a>';
                    echo '</div>';
                }
            } else {
                echo '<p style="color: white;">No se encontraron productos.</p>';
            }
            $conn = null;
            ?>
        </div>
    </div>
</body>
</html>
