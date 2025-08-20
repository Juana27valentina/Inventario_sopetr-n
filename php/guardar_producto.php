<?php
require_once __DIR__ . '/conexion.php';

$nombre = $_POST['nombre'];
$sede = $_POST['sede'];
$cantidad = $_POST['cantidad'];
$fecha = $_POST['fecha_entrega'];
$id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : null;

if ($id) {
    // 🔄 Editar producto existente
    $sql = "UPDATE productos SET nombre=?, sede=?, cantidad=?, fecha_entrega=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisi", $nombre, $sede, $cantidad, $fecha, $id);
    if ($stmt->execute()) {
        header("Location: ../mostrar_todo.php?msg=editado");
    } else {
        echo "❌ Error al editar producto: " . $conn->error;
    }
    $stmt->close();
} else {
    // ➕ Insertar nuevo producto
    $sql = "INSERT INTO productos (nombre, sede, cantidad, fecha_entrega) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $nombre, $sede, $cantidad, $fecha);
    if ($stmt->execute()) {
        header("Location: ../mostrar_todo.php?msg=agregado");
    } else {
        echo "❌ Error al agregar producto: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
