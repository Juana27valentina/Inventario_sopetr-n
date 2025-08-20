<?php
ob_start();
session_start(); // 🔴 IMPORTANTE

include 'conexion.php';

$documento = $_POST['documento'] ?? '';
$clave = $_POST['clave'] ?? '';

if (empty($documento) || empty($clave)) {
    echo "<script>alert('Completa todos los campos'); window.location.href='../inicio_de_sesion.html';</script>";
    exit;
}

$sql = "SELECT * FROM usuarios WHERE documento = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $documento);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    if (password_verify($clave, $usuario['clave'])) {
        // ✅ Guardar sesión
        $_SESSION['usuario'] = $usuario['documento']; // puedes guardar más si deseas
        header("Location: ../administrador.php");
        exit;
    } else {
        echo "<script>alert('❌ Contraseña incorrecta'); window.location.href='../inicio_de_sesion.html';</script>";
    }
} else {
    echo "<script>alert('❌ Usuario no encontrado'); window.location.href='../inicio_de_sesion.html';</script>";
}
?>

















