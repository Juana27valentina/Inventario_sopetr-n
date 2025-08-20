<?php
include 'conexion.php';

$accion = $_POST['accion'] ?? '';

if ($accion === 'listar') {
    $resultado = $conn->query("SELECT * FROM tareas ORDER BY fecha_creacion DESC");

    while ($row = $resultado->fetch_assoc()) {
        $estado = $row['estado'] === 'completada' ? 'completed' : 'not-completed';
        echo "<li class='$estado' data-id='{$row['id']}'>
                <p>" . htmlspecialchars($row['descripcion']) . "</p>
                <div class='dropdown'>
                    <i class='bx bx-dots-vertical-rounded dropdown-toggle'></i>
                    <div class='dropdown-content'>
                        <button onclick='completarTarea({$row['id']})'>Completar</button>
                        <button onclick='eliminarTarea({$row['id']})'>Eliminar</button>
                    </div>
                </div>
              </li>";
    }
} elseif ($accion === 'agregar') {
    $desc = trim($_POST['descripcion'] ?? '');
    if ($desc !== '') {
        $stmt = $conn->prepare("INSERT INTO tareas (descripcion) VALUES (?)");
        $stmt->bind_param("s", $desc);
        $stmt->execute();
        echo "ok";
    }
} elseif ($accion === 'completar') {
    $id = intval($_POST['id']);
    $conn->query("UPDATE tareas SET estado='completada' WHERE id=$id");
    echo "ok";
} elseif ($accion === 'eliminar') {
    $id = intval($_POST['id']);
    $conn->query("DELETE FROM tareas WHERE id=$id");
    echo "ok";
} else {
    echo "Acción inválida";
}

$conn->close();
?>
