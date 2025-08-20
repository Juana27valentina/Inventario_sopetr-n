<?php
include 'conexion.php';

$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';

$sql = "SELECT id, nombre, descripcion, cantidad FROM productos 
        WHERE nombre LIKE ? OR descripcion LIKE ? OR sede LIKE ? 
        ORDER BY nombre ASC";

$param = "%$busqueda%";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $param, $param, $param);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
    echo "<td>" . htmlspecialchars($row['descripcion']) . "</td>";
    echo "<td>" . htmlspecialchars($row['cantidad']) . "</td>";
    echo "</tr>";
}

$stmt->close();
$conn->close();
?>
