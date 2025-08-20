<?php
include 'conexion.php';

if (!empty($_POST['descripcion'])) {
    $descripcion = $_POST['descripcion'];
    $stmt = $conn->prepare("INSERT INTO tareas (descripcion, estado, fecha_creacion) VALUES (?, 'pendiente', NOW())");
    $stmt->bind_param("s", $descripcion);
    $stmt->execute();
}
?>

