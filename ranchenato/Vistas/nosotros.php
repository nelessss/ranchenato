

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranchenato</title>
    <link rel="stylesheet" href="../aseets/css/style.css">
    <style>
        .nosotros {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .mision{
            width: 500px;
            backdrop-filter: blur(15px);
            border: 0.5px white solid;
            margin: auto;
            margin-top: 100px;
            padding: 20px;
            border-radius: 10px;
            color: white;
        }
        .vision{
            width: 500px;
            backdrop-filter: blur(15px);
            border: 0.5px white solid;
            margin: auto;
            margin-top: 100px;
            padding: 20px;
            border-radius: 10px;
            color: white;
        }
        .mapa {
            margin-top: 30px;
            width: 100%;
            max-width: 800px;
            height: 200px;
            border: 0;
            border-radius: 10px;
            overflow: hidden;
            
        }
        .informacion {
            text-align: center;
            color: white;
            font-weight: bold;
        }
        .informacion p {
            margin: 5px 0;
        }
    
        

    </style>
</head>
<body style="background: url(./Imagenes/fondo.jpg);">
    <header>
        <a href="../index.php" class="logo" style="color: white; padding: 15px; margin-left: 150px; border-radius: 100%;">
        <h2 class="nombre">Ranchenato</h2>
        </a>
        <nav class="links">
            <?php
            session_start();
            if (isset($_SESSION['NomUsu']) && isset($_SESSION['ApeUsu'])): ?>
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
    <div class="nosotros">
    <a href="../index.php" class="logo" style="color: white;">
            <img width="350px" style="padding: 60px; border-radius: 100%;" src="../Vistas/Imagenes/logosinfondo.png" alt="logo">
        </a>
        <div class="mision">
        <h2>MISION</h2>
            <p> Ranchenato es un bar ubicado en Bogotá, Colombia, fundado con la misión de ofrecer una experiencia
             única en el disfrute de bebidas en un ambiente acogedor y vibrante. Con un enfoque en la excelencia 
             en el servicio, Ranchenato se destaca por su variedad de bebidas de la más alta calidad. 
             Nuestro compromiso con la satisfacción del cliente y el bienestar de nuestro equipo nos impulsa 
             a mantener los más altos estándares de calidad y servicio en la industria.</p>
        </div>
        <div class="vision">
        <h2>VISION</h2>
            <p> Ranchenato inició sus operaciones en Bogotá en el año 2020, con la visión de convertirse en un 
             referente en el sector de bares de la ciudad. Desde nuestros inicios, hemos buscado ofrecer una 
             experiencia memorable para nuestros clientes, gracias a nuestra variada oferta de bebidas, un equipo 
             humano altamente comprometido, y un ambiente único que combina música en vivo y un diseño acogedor.</p>
        </div>
    </div>
    <div class="informacion">
                <h2>Información de Contacto</h2>
                <p><strong>Teléfono:</strong> 3043740326 </p>
                <p><strong>Dirección:</strong> Cra. 10 No. 31 - 29 Sur, Bogotá, Colombia</p>
                <p><strong>Correo electrónico:</strong> ranchenatobar@gmail.com</p>
            </div>
            <center><iframe src="https://www.google.com/maps/embed?pb=!4v1725745124382!6m8!1m7!1sn3SxFQ88POcYrsCDxXsG6A!2m2!1d4.58346107410054!2d-74.10039058253845!3f112.42810992615523!4f-12.587034900634563!5f0.7820865974627469" width="800" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></center>
</body>
</html>