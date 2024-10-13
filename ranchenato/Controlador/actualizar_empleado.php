<?php
include '../Modelo/db_connection.php';

// Verificar si se ha enviado el formulario
if (isset($_POST['guardar'])) {
    // Obtener los datos del formulario
    $CodEmp = $_POST['CodEmp'];
    $DocEmp = $_POST['DocEmp'];
    $NomEmp = $_POST['NomEmp'];
    $ApeEmp = $_POST['ApeEmp'];
    $DirEmp = $_POST['DirEmp'];
    $TelEmp = $_POST['TelEmp'];
    $ConEmp = $_POST['ConEmp'];
    $CodRol = $_POST['CodRol'];

    try {
        // Conectar a la base de datos
        $database = new Database();
        $conn = $database->getConnection();

        // Preparar la consulta de actualización
        $sql_update = "UPDATE empleado SET DocEmp = :DocEmp, NomEmp = :NomEmp, ApeEmp = :ApeEmp, DirEmp = :DirEmp, TelEmp = :TelEmp, ConEmp = :ConEmp, CodRol = :CodRol WHERE CodEmp = :CodEmp";
        $stmt = $conn->prepare($sql_update);

        // Ejecutar la consulta con los parámetros
        $stmt->execute([
            ':DocEmp' => $DocEmp,
            ':NomEmp' => $NomEmp,
            ':ApeEmp' => $ApeEmp,
            ':DirEmp' => $DirEmp,
            ':TelEmp' => $TelEmp,
            ':ConEmp' => $ConEmp,
            ':CodRol' => $CodRol,
            ':CodEmp' => $CodEmp
        ]);

        // Redirigir a la lista de empleados si la actualización fue exitosa
        header("Location: ../Vistas/empleados.php");
        exit();
    } catch (PDOException $e) {
        // Mostrar mensaje de error si la actualización falla
        echo "Error al actualizar empleado: " . $e->getMessage();
    }

    // Cerrar la conexión
    $conn = null;
} else {
    // Redirigir si no se envió el formulario
    header("Location: ../Vistas/empleados.php");
    exit();
}
?>
