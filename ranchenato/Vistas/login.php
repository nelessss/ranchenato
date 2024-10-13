<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../aseets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Login</title>
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
                    <a style="color: white;" href="./Vistas/home_admin.php" class="link">Administración</a>
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

    <br>

    <div class="contenido">
        <form action="" method="POST">
            <center><h1>LOGIN</h1></center>
            <input type="email" name="CorUsu" placeholder="INGRESE CORREO" class="input" required>
            <br><br>
            <input type="password" id="password" name="ConUsu" placeholder="INGRESE CONTRASEÑA" class="input" required>
            <br><br>
            <a class="toggle-password" onclick="togglePasswordVisibility()">👁️ Ver Contraseña</a>
            <p>¿No tienes cuenta? <a href="../Vistas/register.php" class="link2" >Registrarse</a></p>
            <br>
            <center><button type="submit" class="boton">Ingresar</button></center>
            <br>
            <?php
            include '../Modelo/db_connection.php';

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $CorUsu = $_POST['CorUsu'];
                $ConUsu = $_POST['ConUsu'];

                $database = new Database();
                $conn = $database->getConnection();

                $sql = "SELECT CodUsu, NomUsu, ApeUsu, ConUsu, CodRol, EstUsu FROM usuario WHERE CorUsu = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$CorUsu]);

                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row['EstUsu'] == 'Inactivo') {
                        echo "Error de iniciar sesión: cuenta inhabilitada. <a href='../Vistas/contacto.php' class='link2'>Mas info</a>";
                    } elseif ($ConUsu == $row['ConUsu']) {
                        $_SESSION['CodUsu'] = $row['CodUsu'];
                        $_SESSION['NomUsu'] = $row['NomUsu'];
                        $_SESSION['ApeUsu'] = $row['ApeUsu'];
                        $_SESSION['CodRol'] = $row['CodRol'];

                        if ($row['CodRol'] == 2) {
                            header("Location: home_admin.php");
                        } elseif ($row['CodRol'] == 1) {
                            header("Location: ../index.php");
                        } elseif ($row['CodRol'] == 3) {
                            header("Location: domiciliario.php");
                        }
                        exit();
                    } else {
                        echo "Contraseña incorrecta.";
                    }
                } else {
                    echo "Correo no registrado.";
                }
            }
            ?>
        </form>
    </div>
    <script src="../aseets/js/script.js"></script>
</body>
</html>
