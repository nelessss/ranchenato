<?php
session_start();

if (!isset($_SESSION["CodUsu"])) {
    header("Location: login.php");
    exit();
}

include '../Modelo/db_connection.php';
$database = new Database();
$conn = $database->getConnection();

// Obtener los 3 productos más vendidos en el mes actual
$sqlProductos = "SELECT p.NomPro, SUM(dp.CanPro) AS total_vendido
                 FROM detallePedido dp
                 JOIN Producto p ON dp.CodPro = p.CodPro
                 JOIN Pedido ped ON dp.CodPed = ped.CodPed
                 WHERE MONTH(ped.FecPed) = MONTH(CURRENT_DATE()) 
                 AND YEAR(ped.FecPed) = YEAR(CURRENT_DATE())
                 GROUP BY p.CodPro
                 ORDER BY total_vendido DESC
                 LIMIT 3";
$stmtProductos = $conn->prepare($sqlProductos);
$stmtProductos->execute();
$productos = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);


$productosNombres = [];
$productosTotales = [];
foreach ($productos as $producto) {
    $productosNombres[] = $producto['NomPro'];
    $productosTotales[] = $producto['total_vendido'];
}

// Obtener los 3 usuarios que más hacen pedidos en el mes actual
$sqlUsuarios = "SELECT u.NomUsu, COUNT(p.CodPed) AS total_pedidos
                FROM Pedido p
                JOIN usuario u ON p.CodUsu = u.CodUsu
                WHERE MONTH(p.FecPed) = MONTH(CURRENT_DATE()) 
                AND YEAR(p.FecPed) = YEAR(CURRENT_DATE())
                GROUP BY u.CodUsu
                ORDER BY total_pedidos DESC
                LIMIT 3";
$stmtUsuarios = $conn->prepare($sqlUsuarios);
$stmtUsuarios->execute();
$usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);


$usuariosNombres = [];
$usuariosTotales = [];
foreach ($usuarios as $usuario) {
    $usuariosNombres[] = $usuario['NomUsu'];
    $usuariosTotales[] = $usuario['total_pedidos'];
}

// Obtener el usuario que más pedidos ha entregado en el mes actual
$sqlMaxDomicilio = "SELECT u.NomUsu, COUNT(d.CodDom) AS total_domicilios
                    FROM domicilio d
                    JOIN usuario u ON d.CodUsu = u.CodUsu
                    JOIN Pedido p ON d.CodPed = p.CodPed
                    WHERE MONTH(p.FecPed) = MONTH(CURRENT_DATE()) 
                    AND YEAR(p.FecPed) = YEAR(CURRENT_DATE())
                    GROUP BY u.CodUsu
                    ORDER BY total_domicilios DESC
                    LIMIT 1";
$stmtMaxDomicilio = $conn->prepare($sqlMaxDomicilio);
$stmtMaxDomicilio->execute();
$maxDomicilio = $stmtMaxDomicilio->fetch(PDO::FETCH_ASSOC);


$maxDomicilioNombre = $maxDomicilio['NomUsu'];
$maxDomicilioTotal = $maxDomicilio['total_domicilios'];

$stmtProductos->closeCursor();
$stmtUsuarios->closeCursor();
$stmtMaxDomicilio->closeCursor();
$conn = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../aseets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px; /* Espacio entre los gráficos */
            width: 80%;
            margin: auto;
            margin-bottom: 40px;
        }
        .chart-container canvas {
            width: 600px !important;
            height: 300px !important;
        }
        .chart-container.full-width canvas {
            width: 600px !important;
            height: 300px !important;
        }
    </style>
</head>
<body style="background: url(./Imagenes/fondobeige.jpg);">
    <div class="container">
        <div class="sidebar" style="background: url(./Imagenes/fondo.jpg);">
            <h2>Bienvenido, <?php echo $_SESSION['NomUsu'] . ' ' . $_SESSION['ApeUsu']; ?></h2>
            <div class="table-links">
                <a href="usuario.php">Usuarios</a>
                <a href="roles.php">Roles</a>
                <a href="empleados.php">Empleados</a>
                <a href="productos.php">Productos</a>
                <a href="pedidos.php">Pedidos</a>
                <a href="domicilio.php">Domicilios</a>
                <a href="contactos.php">Contactos</a>
            </div>
            <div class="logout-btn">
                <form action="../Controlador/logout.php" method="post">
                    <button type="submit" style="background-color: red;">Cerrar Sesión</button>
                </form>
                <br>
                <form action="../index.php" method="post">
                    <button type="submit">Inicio</button>
                </form>
            </div>
        </div>
        <div class="content" style="background: url(./Imagenes/fondo.jpg);">
            <h1>ESTADISTICAS DEL MES</h1>
            <div class="chart-container">
                <div class="" style="background: none; border:none;" >
                    <h3>Productos más vendidos</h3>
                    <canvas id="productosChart" style="background: url('../Vistas/Imagenes/fondobeige.jpg'); padding: 10px; border-radius:10px;"></canvas>
                </div>
                <div class="" style="background: none; border:none;" >
                    <h3>Clientes más frecuentes</h3>
                    <canvas id="usuariosChart" style="background: url('../Vistas/Imagenes/fondobeige.jpg'); padding: 10px; border-radius:10px;"></canvas>
                </div>
            </div>

            <div class="chart-container full-width">
                <div>
                    <h3>Domiciliario con Más Pedidos Entregados</h3>
                    <canvas id="maxDomicilioChart" style="background: url('../Vistas/Imagenes/fondobeige.jpg'); padding: 10px; border-radius:10px;"></canvas>
                </div>
            </div>

            <script>
                // Datos para el gráfico de productos más vendidos
                const productosData = {
                    labels: <?php echo json_encode($productosNombres); ?>,
                    datasets: [{
                        label: 'Cantidad Vendida',
                        data: <?php echo json_encode($productosTotales); ?>,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        fill: true
                    }]
                };

                // Crear el gráfico de productos más vendidos
                new Chart(document.getElementById('productosChart').getContext('2d'), {
                    type: 'bar',
                    data: productosData,
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Productos'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Cantidad Vendida'
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Datos para el gráfico de usuarios con más pedidos
                const usuariosData = {
                    labels: <?php echo json_encode($usuariosNombres); ?>,
                    datasets: [{
                        label: 'Total Pedidos',
                        data: <?php echo json_encode($usuariosTotales); ?>,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderWidth: 2,
                        fill: true
                    }]
                };

                // Crear el gráfico de usuarios con más pedidos
                new Chart(document.getElementById('usuariosChart').getContext('2d'), {
                    type: 'bar',
                    data: usuariosData,
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Usuarios'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Número de Pedidos'
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Datos para el gráfico del usuario con más pedidos entregados
                const maxDomicilioData = {
                    labels: ['<?php echo $maxDomicilioNombre; ?>'],
                    datasets: [{
                        label: 'Total Pedidos Entregados',
                        data: [<?php echo $maxDomicilioTotal; ?>],
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 2,
                        fill: true
                    }]
                };

                // Crear el gráfico del usuario con más pedidos entregados
                new Chart(document.getElementById('maxDomicilioChart').getContext('2d'), {
                    type: 'bar',
                    data: maxDomicilioData,
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Usuario'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Número de Pedidos Entregados'
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
            <a href="../Vistas/home_admin.php" class="regresar">Regresar al Panel</a>
        </div>
    </div>
</body>
</html>
