<?php
include 'conexion.php';

// Captura de datos
$documento = $_POST['documento'] ?? '';
$nombre = $_POST['usuario'] ?? '';
$contrasena = $_POST['clave'] ?? '';

// Encriptar contraseña
$hashed_pass = password_hash($contrasena, PASSWORD_DEFAULT);

// Ajustar nombre de columna si es "clave"
$sql = "INSERT INTO usuarios (documento, nombre, clave) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $documento, $nombre, $hashed_pass);

// Ejecutar
if ($stmt->execute()) {
     header("Location: ../administrador.php");
     exit;
    // echo "✅ Usuario registrado correctamente.";
} else {
    echo "❌ Error: " . $stmt->error;
}
?>




