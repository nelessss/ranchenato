<?php
include '../Modelo/db_connection.php';

// Crear instancia de la base de datos
$database = new Database();
$conn = $database->getConnection();

if (isset($_GET['CodRol'])) {
    $CodRol = $_GET['CodRol'];

    // Preparar la consulta de eliminación
    $sql = "DELETE FROM rol WHERE CodRol = :CodRol";
    $stmt = $conn->prepare($sql);

    // Ejecutar la consulta
    if ($stmt->execute([':CodRol' => $CodRol])) {
        header("Location: ../Vistas/roles.php");
        exit();
    } else {
        echo "Error al eliminar rol: " . implode(", ", $stmt->errorInfo());
    }
}

$conn = null; // Cerrar la conexión
?>
