<?php
include '../Modelo/db_connection.php';

if (isset($_GET['CodUsu'])) {
    $CodUsu = $_GET['CodUsu'];

    // Crear instancia de la base de datos
    $database = new Database();
    $conn = $database->getConnection();

    // Preparar la consulta
    $sql = "DELETE FROM usuario WHERE CodUsu = :CodUsu";
    $stmt = $conn->prepare($sql);

    // Ejecutar la consulta con el parámetro CodUsu
    if ($stmt->execute([':CodUsu' => $CodUsu])) {
        header("Location: ../Vistas/usuario.php");
    } else {
        echo "Error al eliminar usuario: " . $stmt->errorInfo()[2];
    }
}
?>
