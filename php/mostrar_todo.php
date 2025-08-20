<?php
include 'conexion.php';

$q = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if ($q !== '') {
    $sql = "SELECT id, nombre, cantidad FROM productos 
            WHERE nombre LIKE '%$q%' 
            ORDER BY nombre";
} else {
    $sql = "SELECT id, nombre, cantidad FROM productos ORDER BY nombre";
}

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nombre']}</td>";
        echo "<td>-</td>";  // Si no tienes columna descripción, puedes dejar un guion
        echo "<td>{$row['cantidad']}</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No se encontraron productos.</td></tr>";
}
?>


