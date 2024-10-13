<?php
include '../Modelo/db_connection.php';

if (isset($_GET['CodEmp'])) {
    $CodEmp = $_GET['CodEmp'];

    try {
        // Conectar a la base de datos
        $database = new Database();
        $conn = $database->getConnection();

        // Preparar la consulta de eliminación
        $sql_delete = "DELETE FROM empleado WHERE CodEmp = :CodEmp";
        $stmt = $conn->prepare($sql_delete);

        // Ejecutar la consulta con el parámetro
        $stmt->execute([':CodEmp' => $CodEmp]);

        // Redirigir a la lista de empleados si la eliminación fue exitosa
        header("Location: ../Vistas/empleados.php");
        exit();
    } catch (PDOException $e) {
        // Mostrar mensaje de error si la eliminación falla
        echo "Error al eliminar empleado: " . $e->getMessage();
    }

    // Cerrar la conexión
    $conn = null;
} else {
    echo "ID de empleado no especificado.";
}
?>
