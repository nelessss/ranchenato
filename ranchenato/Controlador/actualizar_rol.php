<?php
include '../Modelo/db_connection.php';

if(isset($_POST['CodRol'])) {
    $CodRol = $_POST['CodRol'];
    $NomRol = $_POST['NomRol'];

    $sql = "UPDATE rol SET NomRol='$NomRol' WHERE CodRol=$CodRol";

    if ($conn->query($sql) === TRUE) {
        header("Location: rol.php");
        exit();
    } else {
        echo "Error al actualizar rol: " . $conn->error;
    }
}

$conn->close();
?>
