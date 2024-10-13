<?php
include '../Modelo/db_connection.php';

// Conectar a la base de datos
$database = new Database();
$conn = $database->getConnection();

if (isset($_GET['CodPed'])) {
    $CodPed = $_GET['CodPed'];

    // Preparar la consulta de eliminación
    $sql_delete = "DELETE FROM Pedido WHERE CodPed = ?";
    $stmt = $conn->prepare($sql_delete);

    // Ejecutar la consulta con el parámetro
    try {
        $stmt->execute([$CodPed]);
        header("Location: ../Vistas/pedidos.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al eliminar pedido: " . $e->getMessage();
    }

    // Cerrar la declaración y la conexión
    $stmt = null;
    $conn = null;
} else {
    echo "ID de pedido no especificado.";
}
?>
