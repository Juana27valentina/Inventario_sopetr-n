<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /inventario/inicio_de_sesion.html");
    exit();
}

// Muestra el archivo HTML del panel de administrador
readfile(__DIR__ . '/../administrador.php');
?>