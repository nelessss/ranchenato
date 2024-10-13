<?php
// Incluir la clase Database
include '../Modelo/db_connection.php';

// Crear una instancia de la clase Database
$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['CodCon'])) {
    $CodCon = $_GET['CodCon'];

    // Preparar y ejecutar la consulta para eliminar el contacto
    $sql = "DELETE FROM contacto WHERE CodCon = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$CodCon])) {
        header("Location: ../Vistas/contactos.php");
        exit(); // Asegurarse de que el script se detenga después del redireccionamiento
    } else {
        echo "Error al eliminar el contacto: " . $stmt->errorInfo()[2];
    }

    $stmt->closeCursor();
    $conn = null; // Cerrar la conexión
} else {
    echo "No se proporcionó un código de contacto válido.";
}
?>
