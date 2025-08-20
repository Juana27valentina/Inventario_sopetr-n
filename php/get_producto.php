<?php
require_once __DIR__ . '/conexion.php';

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "ID no proporcionado"]);
    exit;
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM productos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["error" => "Producto no encontrado"]);
}

$stmt->close();
$conn->close();
