<?php
include 'conexion.php';

$sql = "SELECT COUNT(*) AS total FROM tareas WHERE estado = 'pendiente'";
$resultado = $conn->query($sql);
$fila = $resultado->fetch_assoc();

echo $fila['total'];
?>
