<?php
include '../Modelo/db_connection.php';

if (isset($_POST['CodUsu'])) {
    $CodUsu = $_POST['CodUsu'];
    $NomUsu = $_POST['NomUsu'];
    $ApeUsu = $_POST['ApeUsu'];
    $TelUsu = $_POST['TelUsu'];
    $CorUsu = $_POST['CorUsu'];
    $ConUsu = $_POST['ConUsu'];
    $EstUsu = $_POST['EstUsu'];
    $CodRol = $_POST['CodRol'];

    // Validar entradas
    if (empty($CodUsu) || empty($NomUsu) || empty($ApeUsu) || empty($TelUsu) || empty($CorUsu) || empty($ConUsu) || empty($EstUsu) || empty($CodRol)) {
        echo "Por favor, complete todos los campos.";
        exit();
    }

    // Crear instancia de la base de datos
    $database = new Database();
    $conn = $database->getConnection();

    // Preparar la consulta
    $sql = "UPDATE usuario SET NomUsu = :NomUsu, ApeUsu = :ApeUsu, TelUsu = :TelUsu, CorUsu = :CorUsu, ConUsu = :ConUsu, EstUsu = :EstUsu, CodRol = :CodRol WHERE CodUsu = :CodUsu";
    $stmt = $conn->prepare($sql);

    // Ejecutar la consulta con los datos del formulario
    if ($stmt->execute([
        ':NomUsu' => $NomUsu,
        ':ApeUsu' => $ApeUsu,
        ':TelUsu' => $TelUsu,
        ':CorUsu' => $CorUsu,
        ':ConUsu' => $ConUsu,
        ':EstUsu' => $EstUsu,
        ':CodRol' => $CodRol,
        ':CodUsu' => $CodUsu
    ])) {
        header("Location: ../Vistas/usuario.php");
        exit();
    } else {
        echo "Error al actualizar usuario: " . $stmt->errorInfo()[2];
    }
}
?>
