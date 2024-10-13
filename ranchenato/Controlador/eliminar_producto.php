<?php
include '../Modelo/db_connection.php';

if (isset($_GET['CodPro'])) {
    $CodPro = $_GET['CodPro'];

    // Validar el ID del producto
    if (filter_var($CodPro, FILTER_VALIDATE_INT) === false) {
        echo "ID de producto no válido.";
        exit();
    }

    $database = new Database();
    $conn = $database->getConnection();

    try {
        $conn->beginTransaction();

        // Eliminar las dependencias en detallepedido
        $sql_delete_details = "DELETE FROM detallepedido WHERE CodPro = ?";
        $stmt = $conn->prepare($sql_delete_details);
        $stmt->execute([$CodPro]);

        // Eliminar el producto
        $sql_delete_product = "DELETE FROM Producto WHERE CodPro = ?";
        $stmt = $conn->prepare($sql_delete_product);
        $stmt->execute([$CodPro]);

        $conn->commit();
        header("Location: ../Vistas/productos.php");
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error al eliminar producto: " . $e->getMessage();
    }

    $conn = null; // Cerrar la conexión a la base de datos
} else {
    echo "ID de producto no especificado.";
}
?>
