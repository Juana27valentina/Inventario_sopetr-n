<?php
include 'conexion.php';

$result = $conn->query("SELECT id, descripcion, estado FROM tareas ORDER BY fecha_creacion DESC");
$tareas = [];

while ($row = $result->fetch_assoc()) {
    $tareas[] = $row;
}

echo json_encode($tareas);
?>

