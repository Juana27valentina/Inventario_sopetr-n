<?php
include 'conexion.php';

$sql = "SELECT * FROM productos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Productos</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="formulario">
        <h1>Inventario</h1>
        <a href="administrador.html">Volver al panel</a>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Código</th>
                <th>Cantidad</th>
                <th>Fecha de entrega</th>
                <th>Sede</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                    <td><?= $row['cantidad'] ?></td>
                    <td><?= $row['fecha_entrega'] ?></td>
                    <td><?= htmlspecialchars($row['sede']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>