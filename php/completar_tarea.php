<?php
include 'conexion.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $conn->query("UPDATE tareas SET estado = 'completada' WHERE id = $id");
}
?>


