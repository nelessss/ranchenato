<?php
session_start();
include '../Modelo/db_connection.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['CodUsu'])) {
    header("Location: ../Vistas/login.php");
    exit();
}

// Obtener el código del pedido desde el formulario
$CodPed = isset($_POST['CodPed']) ? intval($_POST['CodPed']) : null;
if (!$CodPed) {
    die("No se especificó el código del pedido.");
}

// Validar y recibir los datos del formulario
$des_dom = isset($_POST['des_dom']) ? trim($_POST['des_dom']) : '';
$dir_dom = isset($_POST['dir_dom']) ? trim($_POST['dir_dom']) : '';

// Validar que los campos no estén vacíos
if (empty($des_dom) || empty($dir_dom)) {
    die("Debe ingresar la descripción y la dirección de envío.");
}

// Crear una instancia de la clase Database y obtener la conexión
$database = new Database();
$conn = $database->getConnection();

// Verificar si la conexión se realizó correctamente
if ($conn === null) {
    die('No se pudo conectar a la base de datos.');
}

// Insertar la dirección de envío en la base de datos
$sql_insertar_domicilio = "INSERT INTO Domicilio (CodPed, EstDom, DesDom, DirDom) VALUES (?, ?, ?, ?)";
$stmt_insertar_domicilio = $conn->prepare($sql_insertar_domicilio);

if ($stmt_insertar_domicilio === false) {
    die('Error en prepare para insertar dirección de domicilio: ' . implode(', ', $conn->errorInfo()));
}

// Establecer el valor de EstDom como "Pendiente"
$estado = 'Pendiente';

// Ejecutar la consulta de inserción
$result = $stmt_insertar_domicilio->execute([$CodPed, $estado, $des_dom, $dir_dom]);

// Verificar si se insertó correctamente
if (!$result) {
    die('Error al insertar la dirección de envío.');
}

// Redirigir al usuario a la página de confirmación de pedido
header("Location: pedido_confirmado.php?CodPed=$CodPed");
exit();
?>
