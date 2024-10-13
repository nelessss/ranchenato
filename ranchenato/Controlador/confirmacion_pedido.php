<?php
session_start();
include '../Modelo/db_connection.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['CodUsu'])) {
    header("Location: ../Vistas/login.php");
    exit();
}

$CodUsu = $_SESSION['CodUsu'];

// Establecer conexión con PDO
$db = new Database();
$pdo = $db->getConnection();

try {
    // Obtener los productos en el carrito del usuario
    $sql = "SELECT CodPro, Cantidad FROM Carrito WHERE CodUsu = :CodUsu";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':CodUsu', $CodUsu, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        // Inicializar el total del pedido
        $totPed = 0;

        // Crear un nuevo pedido
        $sql_pedido = "INSERT INTO Pedido (CodUsu, FecPed, EstPed, TotPed) VALUES (:CodUsu, NOW(), 'Pendiente', :TotPed)";
        $stmt_pedido = $pdo->prepare($sql_pedido);
        $stmt_pedido->bindParam(':CodUsu', $CodUsu, PDO::PARAM_INT);
        $stmt_pedido->bindParam(':TotPed', $totPed, PDO::PARAM_STR);
        $stmt_pedido->execute();
        $CodPed = $pdo->lastInsertId();

        // Insertar productos en la tabla DetallePedido
        $sql_detalle = "INSERT INTO DetallePedido (CodPro, CanPro, PrePro, SutPed, CodPed) VALUES (:CodPro, :CanPro, :PrePro, :SutPed, :CodPed)";
        $stmt_detalle = $pdo->prepare($sql_detalle);

        foreach ($result as $row) {
            $CodPro = $row['CodPro'];
            $Cantidad = $row['Cantidad'];

            // Obtener el precio del producto
            $sql_precio = "SELECT PrePro FROM Producto WHERE CodPro = :CodPro";
            $stmt_precio = $pdo->prepare($sql_precio);
            $stmt_precio->bindParam(':CodPro', $CodPro, PDO::PARAM_INT);
            $stmt_precio->execute();
            $row_precio = $stmt_precio->fetch(PDO::FETCH_ASSOC);

            if ($row_precio) {
                $PrePro = $row_precio['PrePro'];
                $SutPed = $Cantidad * $PrePro; // Calcular subtotal del pedido
                $totPed += $SutPed; // Sumar al total del pedido

                // Insertar detalles del pedido
                $stmt_detalle->bindParam(':CodPro', $CodPro, PDO::PARAM_INT);
                $stmt_detalle->bindParam(':CanPro', $Cantidad, PDO::PARAM_INT);
                $stmt_detalle->bindParam(':PrePro', $PrePro, PDO::PARAM_STR);
                $stmt_detalle->bindParam(':SutPed', $SutPed, PDO::PARAM_STR);
                $stmt_detalle->bindParam(':CodPed', $CodPed, PDO::PARAM_INT);
                $stmt_detalle->execute();
            } else {
                throw new Exception('Error al obtener el precio del producto.');
            }
        }

        // Actualizar el total del pedido en la tabla Pedido
        $sql_actualizar_total = "UPDATE Pedido SET TotPed = :TotPed WHERE CodPed = :CodPed";
        $stmt_actualizar_total = $pdo->prepare($sql_actualizar_total);
        $stmt_actualizar_total->bindParam(':TotPed', $totPed, PDO::PARAM_STR);
        $stmt_actualizar_total->bindParam(':CodPed', $CodPed, PDO::PARAM_INT);
        $stmt_actualizar_total->execute();

        // Vaciar el carrito del usuario
        $sql_vaciar_carrito = "DELETE FROM Carrito WHERE CodUsu = :CodUsu";
        $stmt_vaciar_carrito = $pdo->prepare($sql_vaciar_carrito);
        $stmt_vaciar_carrito->bindParam(':CodUsu', $CodUsu, PDO::PARAM_INT);
        $stmt_vaciar_carrito->execute();

        // Redirigir al usuario a la página de pedido_confirmado.php con el CodPed como parámetro
        header("Location: pedido_confirmado.php?CodPed=$CodPed");
        exit();
    } else {
        throw new Exception('No hay productos en el carrito.');
    }
} catch (PDOException $e) {
    die('Error en la base de datos: ' . $e->getMessage());
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

// Cerrar la conexión PDO (opcional, ya que se cierra automáticamente al final del script)
$pdo = null;
?>
