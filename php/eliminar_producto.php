<?php
require_once __DIR__ . '/conexion.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "✅ Producto eliminado correctamente";
    } else {
        echo "❌ Error al eliminar el producto";
    }
    $stmt->close();
} else {
    echo "❌ ID no válido";
}
?>
