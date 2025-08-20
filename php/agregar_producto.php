<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'conexion.php';

$nombre = $_POST['nombre'] ?? '';
$cantidad = $_POST['cantidad'] ?? '';
$sede = $_POST['sede'] ?? '';
$fecha_entrega = $_POST['fecha_entrega'] ?? null;

if ($nombre && $cantidad && $sede && $fecha_entrega) {
    // 1️⃣ Verificar si ya existe el producto en la misma sede
    $verificar = $conn->prepare("SELECT * FROM productos WHERE nombre = ? AND sede = ?");
    $verificar->bind_param("ss", $nombre, $sede);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows > 0) {
        mostrarMensaje("Producto ya existe", "Este producto ya está registrado en esta sede.", "warning", "../administrador.php");
    } else {
        // 2️⃣ Buscar el primer ID faltante en la tabla
        $sql_hueco = "
            SELECT t1.id+1 AS id_faltante
            FROM productos t1
            LEFT JOIN productos t2 ON t2.id = t1.id+1
            WHERE t2.id IS NULL
            ORDER BY t1.id
            LIMIT 1
        ";
        $res = $conn->query($sql_hueco);
        $id_manual = null;

        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $id_manual = $row['id_faltante'];
        }

        // 3️⃣ Insertar usando el ID faltante si existe
        if ($id_manual) {
            $stmt = $conn->prepare("INSERT INTO productos (id, nombre, cantidad, sede, fecha_entrega) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isiss", $id_manual, $nombre, $cantidad, $sede, $fecha_entrega);
        } else {
            $stmt = $conn->prepare("INSERT INTO productos (nombre, cantidad, sede, fecha_entrega) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siss", $nombre, $cantidad, $sede, $fecha_entrega);
        }

        if ($stmt->execute()) {
            mostrarMensaje("¡Producto agregado!", "Se ha registrado correctamente.", "success", "../administrador.php");
        } else {
            mostrarMensaje("Error", "No se pudo guardar el producto.", "error", "../administrador.php");
        }
    }
} else {
    mostrarMensaje("Campos incompletos", "Por favor llena todos los campos.", "info", "../administrador.php");
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


