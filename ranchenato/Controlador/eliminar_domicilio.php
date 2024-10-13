<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["CodUsu"])) {
    header("Location: login.php");
    exit();
}

// Incluir la conexión PDO
include '../Modelo/db_connection.php';
$database = new Database();
$conn = $database->getConnection();

// Verificar si el ID del domicilio está especificado
if (isset($_GET['CodDom'])) {
    $CodDom = $_GET['CodDom'];

    // Preparar y ejecutar la consulta de eliminación
    $sql_delete = "DELETE FROM domicilio WHERE CodDom = ?";
    $stmt = $conn->prepare($sql_delete);

    try {
        $stmt->execute([$CodDom]);
        header("Location: ../Vistas/domicilio.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al eliminar domicilio: " . $e->getMessage();
    }
} else {
    echo "ID de domicilio no especificado.";
}

// Cerrar la conexión
$conn = null;
?>
