<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;

    if ($id > 0 && $cantidad > 0) {
        $stmt = $conn->prepare("UPDATE productos SET cantidad = cantidad + ? WHERE id = ?");
        $stmt->bind_param("ii", $cantidad, $id);

        if ($stmt->execute()) {
            mostrarMensaje("✅ Éxito", "Cantidad añadida correctamente.", "success", "../administrador.php");
        } else {
            mostrarMensaje("❌ Error", "No se pudo actualizar la cantidad.", "error", "../administrador.php");
        }

        $stmt->close();
    } else {
        mostrarMensaje("⚠️ Campos inválidos", "Los valores ingresados no son válidos.", "warning", "../administrador.php");
    }
} else {
    header("Location: ../administrador.php");
    exit();
}

function mostrarMensaje($titulo, $texto, $icono, $redireccion) {
    echo "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                title: '$titulo',
                text: '$texto',
                icon: '$icono',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '$redireccion';
            });
        </script>
    </body>
    </html>";
    exit();
}
?>


