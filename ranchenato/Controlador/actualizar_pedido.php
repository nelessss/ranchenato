<?php
include '../Modelo/db_connection.php';

if (isset($_POST['CodPed'])) {
    $CodPed = $_POST['CodPed'];
    $FecPed = $_POST['FecPed'];
    $TotPed = $_POST['TotPed'];
    $EstPed = $_POST['EstPed'];
    $CodUsu = $_POST['CodUsu'];

    // Conectar a la base de datos
    $database = new Database();
    $conn = $database->getConnection();

    // Preparar la consulta de actualización
    $sql_update = "UPDATE Pedido SET FecPed = ?, TotPed = ?, EstPed = ?, CodUsu = ? WHERE CodPed = ?";
    $stmt = $conn->prepare($sql_update);

    // Ejecutar la consulta con los parámetros
    try {
        $stmt->execute([$FecPed, $TotPed, $EstPed, $CodUsu, $CodPed]);
        header("Location: ../Vistas/pedidos.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al actualizar el pedido: " . $e->getMessage();
    }

    // Cerrar la declaración y la conexión
    $stmt = null;
    $conn = null;
} else {
    header("Location: ../Vistas/pedidos.php");
    exit();
}
?>
