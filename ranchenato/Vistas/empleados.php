<?php
session_start();

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION["CodUsu"])) {
    header("Location: login.php");
    exit();
}

// Obtener el nombre de usuario de la sesión
include '../Modelo/db_connection.php';
$database = new Database();
$conn = $database->getConnection();

$CodUsu = $_SESSION["CodUsu"];
$sql = "SELECT CorUsu FROM usuario WHERE CodUsu = :CodUsu";
$stmt = $conn->prepare($sql);
$stmt->execute([':CodUsu' => $CodUsu]);
$usuario = $stmt->fetchColumn();

// Manejar la adición de un nuevo empleado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $DocEmp = $_POST['DocEmp'];
    $NomEmp = $_POST['NomEmp'];
    $ApeEmp = $_POST['ApeEmp'];
    $DirEmp = $_POST['DirEmp'];
    $TelEmp = $_POST['TelEmp'];
    $ConEmp = $_POST['ConEmp'];
    $CodRol = $_POST['CodRol'];

    $sql_insert = "INSERT INTO empleado (DocEmp, NomEmp, ApeEmp, DirEmp, TelEmp, ConEmp, CodRol) 
                   VALUES (:DocEmp, :NomEmp, :ApeEmp, :DirEmp, :TelEmp, :ConEmp, :CodRol)";
    $stmt = $conn->prepare($sql_insert);

    if ($stmt->execute([
        ':DocEmp' => $DocEmp,
        ':NomEmp' => $NomEmp,
        ':ApeEmp' => $ApeEmp,
        ':DirEmp' => $DirEmp,
        ':TelEmp' => $TelEmp,
        ':ConEmp' => $ConEmp,
        ':CodRol' => $CodRol
    ])) {
        header("Location: empleados.php");
        exit();
    } else {
        echo "Error al agregar empleado: " . implode(", ", $stmt->errorInfo());
    }
}

// Consultar los empleados existentes
$sql = "SELECT empleado.CodEmp, empleado.DocEmp, empleado.NomEmp, empleado.ApeEmp, empleado.DirEmp, empleado.TelEmp, empleado.ConEmp, rol.NomRol 
        FROM empleado 
        INNER JOIN rol ON empleado.CodRol = rol.CodRol";
$stmt = $conn->query($sql);

// Consultar roles para el formulario de adición de empleado
$sql_roles = "SELECT CodRol, NomRol FROM rol";
$stmt_roles = $conn->query($sql_roles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../aseets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                    <button type="submit">Cerrar Sesión</button>
                </form>
            </div>
        </div>
        <div class="content" style="background: url(./Imagenes/fondo.jpg);">
            <h2>Lista de Empleados</h2>
            <table>
                <tr>
                    <th>Código</th>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Contraseña</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
                <?php
                if ($stmt->rowCount() > 0) {
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>".$row["CodEmp"]."</td>";
                        echo "<td>".$row["DocEmp"]."</td>";
                        echo "<td>".$row["NomEmp"]."</td>";
                        echo "<td>".$row["ApeEmp"]."</td>";
                        echo "<td>".$row["DirEmp"]."</td>";
                        echo "<td>".$row["TelEmp"]."</td>";
                        echo "<td>".$row["ConEmp"]."</td>";
                        echo "<td>".$row["NomRol"]."</td>";
                        echo "<td>
                                <a href='../Controlador/editar_empleado.php?CodEmp=".$row["CodEmp"]."' class='btn'><i class='fas fa-edit'></i></a>
                                <a href='../Controlador/eliminar_empleado.php?CodEmp=".$row["CodEmp"]."' class='btn btn-delete'><i class='fas fa-trash-alt'></i></a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No se encontraron empleados.</td></tr>";
                }
                ?>
            </table>
            <br>
            <form action="" method="post">
                <input type="text" name="DocEmp" placeholder="Documento" required>
                <input type="text" name="NomEmp" placeholder="Nombre" required>
                <input type="text" name="ApeEmp" placeholder="Apellido" required>
                <input type="text" name="DirEmp" placeholder="Dirección" required>
                <input type="text" name="TelEmp" placeholder="Teléfono" required>
                <input type="text" name="ConEmp" placeholder="Contraseña" required>
                <select name="CodRol" required>
                    <option value="">Seleccionar Rol</option>
                    <?php
                    if ($stmt_roles->rowCount() > 0) {
                        while($row_role = $stmt_roles->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='".$row_role['CodRol']."'>".$row_role['NomRol']."</option>";
                        }
                    }
                    ?>
                </select>
                <input type="submit" name="agregar" value="Agregar Empleado">
            </form>
            <br>
            <a href="../Vistas/home_admin.php" class="a">Regresar</a>
        </div>
    </div>
</body>
</html>
