<?php
session_start();
include '../Modelo/db_connection.php';

if (!isset($_SESSION['CodUsu'])) {
    header("Location: ../Vistas/login.php");
    exit();
}

if (!isset($_GET['CodPro'])) {
    header("Location: ../index.php");
    exit();
}

$CodPro = $_GET['CodPro'];
$CodUsu = $_SESSION['CodUsu'];

try {
    // Establece la conexión con PDO
    $pdo = new PDO("mysql:host=localhost;dbname=proyectobar", "root", ""); // Ajusta los parámetros de conexión
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el producto ya está en el carrito
    $sql = "SELECT * FROM Carrito WHERE CodPro = :CodPro AND CodUsu = :CodUsu";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['CodPro' => $CodPro, 'CodUsu' => $CodUsu]);
    $productoEnCarrito = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($productoEnCarrito) {
        // Si el producto ya está en el carrito, actualizar la cantidad
        $sql = "UPDATE Carrito SET Cantidad = Cantidad + 1 WHERE CodPro = :CodPro AND CodUsu = :CodUsu";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['CodPro' => $CodPro, 'CodUsu' => $CodUsu]);
    } else {
        // Si el producto no está en el carrito, insertarlo
        $sql = "INSERT INTO Carrito (CodPro, CodUsu, Cantidad) VALUES (:CodPro, :CodUsu, 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['CodPro' => $CodPro, 'CodUsu' => $CodUsu]);
    }
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

header("Location: ../Vistas/carrito.php");
exit();
?>
