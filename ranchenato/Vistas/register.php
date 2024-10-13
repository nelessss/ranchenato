<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../aseets/css/style.css">
    <title>Register</title>
    <style>
        .link2{
            color: red;
        } 
        .link2:hover{
            color: white;
        } 
    </style>
</head>
<body style="background: url(./Imagenes/fondo.jpg);">
    <header>
        <a href="../index.php" class="logo" style="color: white;">
            <img width="100px" style="padding: 15px; border-radius: 100%;" src="../Vistas/Imagenes/logosinfondo.png" alt="logo">
            <h2 class="nombre" >Ranchenato</h2>
        </a>
        <nav class="links">
            <?php
            session_start();
            if (isset($_SESSION['NomUsu']) && isset($_SESSION['ApeUsu'])): ?>
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
                <a style="color: white;" href="./Vistas/carrito.php" class="link3"><i class="fas fa-shopping-cart"></i></a>
            <?php else: ?>
                <a style="color: white;" href="./Vistas/contacto.php" class="link">Contacto</a>
                &nbsp;
                <a style="color: white;" href="./Vistas/producto.php" class="link">Productos</a>
                &nbsp;
                <a style="color: white;" href="./Vistas/login.php" class="link">Iniciar sesión</a>
                &nbsp;
            <?php endif; ?>
        </nav>
    </header>

    <br>

    <div class="contenido">
    <form action="" method="POST">
        <center><h1>REGISTER</h1></center>
        <input type="text" name="NomUsu" placeholder="INGRESE NOMBRE" class="input" required>
        <br><br>
        <input type="text" name="ApeUsu" placeholder="INGRESE APELLIDO" class="input" required>
        <br><br>
        <input type="text" name="TelUsu" placeholder="INGRESE NUMERO DE CELULAR" class="input" required>
        <br><br>
        <input type="email" name="CorUsu" placeholder="INGRESE CORREO" class="input" required>
        <br><br>
        <div style="position: relative;">
            <input type="password" id="password" name="ConUsu" placeholder="INGRESE CONTRASEÑA" class="input" required>
            <br><br>
            <a class="toggle-password" onclick="togglePasswordVisibility()">👁️ Ver Contraseña</a>
        </div>
        <p>¿Ya tienes cuenta? <a href="login.php" class="link2">Login</a></p>
        <center><button type="submit" class="boton">Ingresar</button></center>

        <?php
        include '../Modelo/db_connection.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $NomUsu = $_POST['NomUsu'];
            $ApeUsu = $_POST['ApeUsu'];
            $TelUsu = $_POST['TelUsu'];
            $CorUsu = $_POST['CorUsu'];
            $ConUsu = $_POST['ConUsu'];

            $CodRol = '1'; 
            $EstUsu = 'Activo';  

            $database = new Database();
            $conn = $database->getConnection();

            $sql = "INSERT INTO usuario (NomUsu, ApeUsu, TelUsu, CorUsu, ConUsu, EstUsu, CodRol) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$NomUsu, $ApeUsu, $TelUsu, $CorUsu, $ConUsu, $EstUsu, $CodRol]);

            if ($stmt->rowCount() > 0) {
                echo "Registro exitoso!";
            } else {
                echo "Error: No se pudo registrar el usuario.";
            }


        }
        ?>
    </form>
    </div>
    <script src="../aseets/js/script.js"></script>
</body>
</html>
