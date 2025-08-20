<?php
include 'conexion.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $conn->query("DELETE FROM tareas WHERE id = $id");
}
?>

