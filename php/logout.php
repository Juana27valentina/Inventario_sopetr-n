<?php
session_start();
session_unset();    // Borra todas las variables de sesión
session_destroy();  // Destruye la sesión actual

// Redirige al formulario de inicio de sesión (ruta absoluta relativa a la raíz del proyecto)
// header("Location: /inventario/inicio_de_sesion.html");
header("Location: ../inicio_de_sesion.html");
exit();
?>
