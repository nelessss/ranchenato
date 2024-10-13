<?php
session_start();

// Incluir la clase Database
include '../Modelo/db_connection.php';

// Crear una instancia de la clase Database
$db = new Database();
$conn = $db->getConnection();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Ranchenato</title>
    <style>
        body{
            margin: 10px;
            font-family: 'Gill Sans', 'Gill Sans MT','Trebuchet MS', sans-serif;
        }

        header{
            display: flex;
            justify-content: space-between;
            align-items: center;

            border-radius: 10px;
        }
        .logo{
            display: flex;
            align-items: center;
            margin-right: 10px;
        }

        a{
            text-decoration: none;
        }
        nav{
            padding-right: 10px;
        }

        .link{
            backdrop-filter: blur(15px);
            border: 1px white solid;
            padding: 10px;
            border-radius: 10px;
            filter: opacity(50%);
        }

        .link:hover{
            filter: opacity(100%);
        }
        /* END BANNER */

        .img{
            border-radius: 10px;
        }

        /* Catalogo */

        .container{
            position: relative;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
            gap: 1em;
            width: 700px;
            height: 500px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            transition: all 400ms;
            padding-left: 290px;
        }
        .box{
            position: relative;
            background: var(--img) center center;
            background-size: cover;
            transition: all 400ms;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            flex-basis: 200;
        }
        .box::after{
            content: attr(data-text);
            position: absolute;
            bottom: 20px;
            background: #000;
            color: #fff;
            padding: 10px 10px 10px 14px;
            letter-spacing: 4px;
            text-transform: uppercase;
            transform: translateY(60px);
            opacity: 0;
            transition: all 400ms;
        }

        .container:hover .box{
            filter: grayscale(100%) opacity(24%);
        }

        .box:hover::after{
            transform: translateY(0);
            opacity: 1;
            transition-delay: 400ms;
        }

        .container .box:hover{
            filter: grayscale(0%) opacity(100%);
        }

        .box:nth-child(odd){
            transform: translateY(-16px);
        }

        .box:nth-child(even){
            transform: translateY(16);
        }

        .link {
            border: 1px white solid;
            padding: 10px;
            border-radius: 10px;
            filter: opacity(50%);
        }

        .link3 {
            padding: 10px;
            border-radius: 10px;
            filter: opacity(50%);
        }
        .link:hover {
            filter: opacity(100%);
        }
        .link3:hover {
            filter: opacity(100%);
        }
        .contacto {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px auto;
            max-width: 800px;
            padding: 20px;
            border-radius: 10px;
            color: #fff;
        }
        .contacto h2 {
            margin-bottom: 20px;
            font-size: 2rem;
            color: #fff;
        }
        .contacto form {
            width: 100%;
        }
        .contacto .form-group {
            margin-bottom: 15px;
        }
        .contacto label {
            display: block;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }
        .contacto input,
        .contacto textarea {
            width: 97%;
            padding: 10px;
            border-radius: 5px;
            background: transparent;
            color: #fff;
            border: none;
        }

        .contacto textarea{
            border: 1px solid #fff;
        }

        .contacto input{
            border: none;
            border-bottom: 2px solid #fff;
        }
        .contacto textarea {
            resize: vertical;
            height: 150px;
            
        }
        .contacto button {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background-color 0.3s;
        }
        .contacto button:hover {
            background-color: #555;
        }
        .contacto .informacion {
            margin-top: 20px;
            text-align: center;
        }
        .contacto .informacion p {
            margin: 5px 0;
        }
        .contacto .informacion a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }
        .contacto .informacion a:hover {
            text-decoration: underline;
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
    <section class="contacto">
        <form action="" method="POST">
            <center><h1>Contacto</h1></center>
            <input type="text" name="NomCon" placeholder="INGRESE NOMBRE Y APELLIDO" class="input-contact" required>
            <br><br>
            <input type="text" name="DocCon" placeholder="INGRESE DOCUMENTO" class="input-contact" required>
            <br><br>
            <input type="text" name="TelCon" placeholder="INGRESE TELEFONO" class="input-contact" required>
            <br><br>
            <input type="text" name="CorCon" placeholder="INGRESE CORREO" class="input-contact" required>
            <br><br>
            <input type="text" name="asuCon" placeholder="INGRESE ASUNTO" class="input-contact" required>
            <br><br>
            <textarea name="DesCon" id="DesCon" placeholder="DESCRIPCION"></textarea>
            <br><br>
            <center><button type="submit" class="boton">Ingresar</button></center>
            <br>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $NomCon = $_POST['NomCon'];
                $ApeCon = $_POST['ApeCon'];
                $DocCon = $_POST['DocCon'];
                $TelCon = $_POST['TelCon'];
                $CorCon = $_POST['CorCon'];
                $asuCon = $_POST['asuCon'];
                $DesCon = $_POST['DesCon'];

                // Preparar y ejecutar la consulta para insertar el contacto
                $sql = "INSERT INTO contacto (NomCon, ApeCon, DocCon, TelCon, CorCon, asuCon, DesCon) 
                        VALUES (:NomCon, :ApeCon, :DocCon, :TelCon, :CorCon, :asuCon, :DesCon)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':NomCon', $NomCon);
                $stmt->bindParam(':ApeCon', $ApeCon);
                $stmt->bindParam(':DocCon', $DocCon);
                $stmt->bindParam(':TelCon', $TelCon);
                $stmt->bindParam(':CorCon', $CorCon);
                $stmt->bindParam(':asuCon', $asuCon);
                $stmt->bindParam(':DesCon', $DesCon);

                if ($stmt->execute()) {
                    echo "Registro exitoso del PQR!";
                } else {
                    echo "Error: " . implode(" - ", $stmt->errorInfo());
                }
            }
            ?>
        </form>
    </section>
</body>
</html>
