<?php
session_start();
include '../Modelo/db_connection.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['CodUsu'])) {
    header("Location: ../Vistas/login.php");
    exit();
}

$CodUsu = $_SESSION['CodUsu'];

// Verificar si se proporciona un ID de producto válido en la URL
if (!isset($_GET['CodPro'])) {
    header("Location: ../Vistas/carrito.php");
    exit();
}

$CodPro = $_GET['CodPro'];

try {
    // Establece la conexión con PDO
    $pdo = new PDO("mysql:host=localhost;dbname=proyectobar", "root", ""); // Ajusta los parámetros de conexión
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Eliminar el producto del carrito del usuario
    $sql = "DELETE FROM Carrito WHERE CodUsu = :CodUsu AND CodPro = :CodPro";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['CodUsu' => $CodUsu, 'CodPro' => $CodPro]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Redirigir de vuelta a la página del carrito
header("Location: ../Vistas/carrito.php");
exit();
?>
