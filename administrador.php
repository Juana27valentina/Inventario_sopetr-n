<?php
session_start();
require_once __DIR__ . '/php/conexion.php';


if (!isset($_SESSION['usuario'])) {
    header("Location: inicio_de_sesion.html");
    exit;
}

// Estadísticas
$usuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
$productos = $conn->query("SELECT COUNT(*) AS total FROM productos")->fetch_assoc()['total'];
$tareas_pendientes = $conn->query("SELECT COUNT(*) AS total FROM tareas WHERE estado = 'pendiente'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Administrador</title>
	<link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/inventario/css/administrador.css">
	<style>
		.dropdown-content {
			display: none;
			position: absolute;
			background-color: white;
			box-shadow: 0 0 10px rgba(0,0,0,0.2);
			padding: 10px;
			z-index: 1;
		}
		.dropdown:hover .dropdown-content {
			display: block;
		}
	</style>
</head>
<body>

<section id="sidebar">
	<a href="#" class="brand"><i class='bx bxs-smile'></i><span class="text">Stock Control</span></a>
	<ul class="side-menu top">
		<li class="active"><a href="#"><i class='bx bxs-dashboard'></i><span class="text">Panel</span></a></li>
		<li><a href="/inventario/añadir.php"><i class='bx bx-plus'></i><span class="text">Añadir</span></a></li>
		<li><a href="/inventario/nuevo1.html"><i class='bx bxs-doughnut-chart'></i><span class="text">Nuevo</span></a></li>
		<li><a href="/inventario/mostrar_todo.php"><i class='bx bxs-message-dots'></i><span class="text">Mostrar Todo</span></a></li>
	</ul>
	<ul class="side-menu">
		<li><a href="/inventario/php/logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Cerrar Sesión</span></a></li>
	</ul>
</section>

<section id="content">
	<nav>
		<i class='bx bx-menu'></i>
		<a href="#" class="nav-link">Categorías</a>

		<form id="buscar-form" action="mostrar_todo.php" method="GET">
	<div class="form-input">
		<input type="search" name="q" id="busqueda" placeholder="Buscar..." required>
		<button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
	</div>
</form>


		<a href="#" class="notification"><i class='bx bxs-bell'></i><span class="num"><?= $tareas_pendientes ?></span></a>
		<a href="#" class="profile"><img src="/inventario/img/usuario.png"></a>
	</nav>

	<main>
		<div class="head-title">
			<div class="left">
				<h1>Panel</h1>
				<ul class="breadcrumb">
					<li><a href="#">Panel</a></li>
					<li><i class='bx bx-chevron-right'></i></li>
					<li><a class="active" href="#">Inicio</a></li>
				</ul>
			</div>
			<!-- <a href="/inventario/php/generar_pdf.php" class="btn-download" target="_blank">
				<i class='bx bxs-cloud-download'></i> <span class="text">Descargar PDF</span>
			</a> -->
		</div>

		<ul class="box-info">
			<li>
	<i class='bx bxs-calendar-check'></i>
	<span class="text">
		<h3 id="contador-tareas">0</h3>
		<p>Nueva Orden</p>
	</span>
</li>

			<li><i class='bx bxs-group'></i><span class="text"><h3><?= $usuarios ?></h3><p>Usuarios</p></span></li>
			<li><i class='bx bxs-box'></i><span class="text"><h3><?= $productos ?></h3><p>Total de elementos</p></span></li>
		</ul>

		<div class="table-data">
			<div class="todo">
				<!-- <div class="head">
					<h3>Tareas Pendientes</h3>
					<input type="text" id="nueva-tarea" placeholder="Escribe una tarea...">
					<button onclick="agregarTarea()">➕</button>
				</div> -->

<div class="form-input">
  <input type="text" id="nueva-tarea" placeholder=" ✍️  Nueva Tarea..." required>
  <button type="button" onclick="agregarTarea()" class="search-btn"> ➕ </button>
</div>
<br>
				<ul class="todo-list" id="lista-tareas">
	<!-- Aquí se cargarán dinámicamente las tareas -->
</ul>

			</div>
		</div>
	</main>
</section>

<script>
function cargarTareas() {
	fetch('php/obtener_tareas.php')
		.then(res => res.json())
		.then(data => {
			const lista = document.getElementById('lista-tareas');
			lista.innerHTML = '';

			data.forEach(tarea => {
				const li = document.createElement('li');
				li.className = tarea.estado === 'completada' ? 'completed' : 'not-completed';
				li.innerHTML = `
					<p>${tarea.descripcion}</p>
					<div class="dropdown">
						<i class='bx bx-dots-vertical-rounded dropdown-toggle'></i>
						<div class="dropdown-content">
							<button onclick="completarTarea(${tarea.id})">✅Completar</button>
							<button onclick="eliminarTarea(${tarea.id})">🗑️Eliminar</button>
						</div>
					</div>
				`;
				lista.appendChild(li);
			});
		});
}

function agregarTarea() {
	const desc = document.getElementById('nueva-tarea').value.trim();
	if (!desc) return alert("Escribe una tarea.");
	fetch('php/agregar_tarea.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: 'descripcion=' + encodeURIComponent(desc)
	})
	.then(() => {
		alert("📝 Tarea agregada");
		document.getElementById('nueva-tarea').value = '';
		cargarTareas();
	});
}

function completarTarea(id) {
	fetch('php/completar_tarea.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: 'id=' + id
	})
	.then(() => {
		alert("✅ Tarea completada");
		cargarTareas();
	});
}

function eliminarTarea(id) {
	if (confirm("¿Eliminar esta tarea?")) {
		fetch('php/eliminar_tarea.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: 'id=' + id
		})
		.then(() => {
			alert("🗑️ Tarea eliminada");
			cargarTareas();
		});
	}
}

document.addEventListener('DOMContentLoaded', cargarTareas);
</script>

<script>
function actualizarContadorTareas() {
	fetch('php/contar_tareas.php')
		.then(res => res.text())
		.then(total => {
			document.getElementById('contador-tareas').textContent = total;
		});
}

document.addEventListener('DOMContentLoaded', actualizarContadorTareas);

// También lo llamamos después de agregar/completar/eliminar
function agregarTarea() {
	const desc = document.getElementById('nueva-tarea').value.trim();
	if (!desc) return alert("Escribe una tarea.");
	fetch('php/agregar_tarea.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: 'descripcion=' + encodeURIComponent(desc)
	})
	.then(() => {
		alert("📝 Tarea agregada");
		document.getElementById('nueva-tarea').value = '';
		cargarTareas();
		actualizarContadorTareas(); // 🔄
	});
}

function completarTarea(id) {
	fetch('php/completar_tarea.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: 'id=' + id
	})
	.then(() => {
		alert("✅ Tarea completada");
		cargarTareas();
		actualizarContadorTareas(); // 🔄
	});
}

function eliminarTarea(id) {
	if (confirm("¿Eliminar esta tarea?")) {
		fetch('php/eliminar_tarea.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: 'id=' + id
		})
		.then(() => {
			alert("🗑️ Tarea eliminada");
			cargarTareas();
			actualizarContadorTareas(); // 🔄
		});
	}
}
</script>

</body>
</html>




