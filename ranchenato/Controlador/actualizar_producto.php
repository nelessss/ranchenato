<?php
include '../Modelo/db_connection.php';

if (isset($_POST['CodPro'])) {
    $CodPro = $_POST['CodPro'];
    $CatPro = $_POST['CatPro'];
    $NomPro = $_POST['NomPro'];
    $DesPro = $_POST['DesPro'];
    $PrePro = $_POST['PrePro'];
    $StoPro = $_POST['StoPro'];

    $sql_update = "UPDATE Producto SET CatPro=?, NomPro=?, DesPro=?, PrePro=?, StoPro=? WHERE CodPro=?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("sssdii", $CatPro, $NomPro, $DesPro, $PrePro, $StoPro, $CodPro);

    if ($stmt->execute()) {
        header("Location: ../Vistas/productos.php");
        exit();
    } else {
        echo "Error al actualizar el producto: " . $conn->error;
    }
} else {
    header("Location: ../Vistas/productos.php");
    exit();
}

$stmt->close();
$conn->close();
?>
