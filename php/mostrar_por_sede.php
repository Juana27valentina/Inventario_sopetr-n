<?php
header('Content-Type: text/html; charset=UTF-8');
include 'conexion.php';

$sql = "SELECT sede, nombre, id, cantidad FROM productos ORDER BY sede, nombre";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><p>" . htmlspecialchars($row['sede']) . "</p></td>";
    echo "<td><p>" . htmlspecialchars($row['nombre']) . "</p></td>";
    echo "<td><p>" . htmlspecialchars($row['id']) . "</p></td>";
    echo "<td><span class='status'>" . $row['cantidad'] . "</span></td>";
    echo "</tr>";
}

$conn->close();
?>
