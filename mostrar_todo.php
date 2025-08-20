<?php
// archivo: mostrar_todo.php
include './php/conexion.php';

// Captura del parámetro de búsqueda (q)
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$busqueda_esc = $conn->real_escape_string($busqueda);

// Comprobar si existe la columna fecha_entrega para no romper la consulta
$has_fecha_entrega = false;
$check = $conn->query("SHOW COLUMNS FROM productos LIKE 'fecha_entrega'");
if ($check && $check->num_rows > 0) {
    $has_fecha_entrega = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Mostrar Todo</title>
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="./css/mostrar_todo.css">
	<style>
		.dropdown { position: relative; display: inline-block; }
		.dropdown-content { display: none; position: absolute; background-color: #fff; min-width: 120px; box-shadow: 0px 2px 8px rgba(0,0,0,0.2); z-index: 100; right: 0; border-radius: 5px; overflow: hidden; }
		.dropdown-content a { display: block; padding: 8px 12px; color: #333; text-decoration: none; font-size: 14px; }
		.dropdown-content a:hover { background-color: #f2f2f2; }
		.dots { cursor: pointer; font-size: 20px; }
		.no-results { text-align: center; color: #666; padding: 18px 0; }
	</style>
</head>
<body>

<!-- SIDEBAR -->
<section id="sidebar">
	<a href="#" class="brand"><i class='bx bxs-smile'></i><span class="text">Stock Control</span></a>
	<ul class="side-menu top">
		<li><a href="/inventario/administrador.php"><i class='bx bxs-dashboard'></i><span class="text">Panel</span></a></li>
		<li><a href="/inventario/añadir.php"><i class='bx bx-plus'></i><span class="text">Añadir</span></a></li>
		<li><a href="nuevo1.html"><i class='bx bxs-doughnut-chart'></i><span class="text">Nuevo</span></a></li>
		<li class="active"><a href="mostrar_todo.php"><i class='bx bxs-message-dots'></i><span class="text">Mostrar Todo</span></a></li>
	</ul>
	<ul class="side-menu">
		<li><a href="./php/logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Salir</span></a></li>
	</ul>
</section>

<!-- CONTENT -->
<section id="content">
	<nav>
		<i class='bx bx-menu'></i>
		<a href="#" class="nav-link">Categorías</a>

		<!-- Buscador -->
		<form id="buscar-form" method="GET" action="mostrar_todo.php">
			<div class="form-input">
				<input type="search" name="q" placeholder="Buscar sede o elemento..." id="busqueda" value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>">
				<button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
			</div>
		</form>

		<!-- <a href="#" class="notification"><i class='bx bxs-bell'></i><span class="num">8</span></a> -->
		<a href="#" class="btn-download">
					<i class='bx bxs-cloud-download' ></i>
					<span class="text">Descargar PDF</span>
				</a>
				<a href="#" class="profile"><img src="./img/usuario.png"></a>
	</nav>

	<main>
		<div class="head-title">
			<div class="left">
				<h1>Todo</h1>
				<ul class="breadcrumb">
					<li><a href="administrador.php">Panel</a></li>
					<li><i class='bx bx-chevron-right'></i></li>
					<li><a class="active" href="#">Todo</a></li>
				</ul>
			</div>
		</div>

		<div class="table-data">
			<!-- TABLA 1: Filtra por ELEMENTO -->
			<div class="order">
				<div class="head"><h3>Todos los Artículos</h3></div>
				<table>
					<thead>
						<tr>
							<th>Código</th>
							<th>Elemento</th>
							<th>Sede</th>
							<th>Cantidad</th>
							<?php if ($has_fecha_entrega) echo '<th>Fecha de Entrega</th>'; ?>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$select_left = "SELECT id, nombre, sede, cantidad";
						if ($has_fecha_entrega) $select_left .= ", fecha_entrega";
						$select_left .= " FROM productos";
						if ($busqueda !== "") {
							$select_left .= " WHERE nombre LIKE '%$busqueda_esc%'";
						}
						$select_left .= " ORDER BY id ASC";

						$resultado = $conn->query($select_left);
						if ($resultado && $resultado->num_rows > 0) {
							while ($row = $resultado->fetch_assoc()) {
								echo "<tr>";
								echo "<td>" . htmlspecialchars($row['id']) . "</td>";
								echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
								echo "<td>" . htmlspecialchars($row['sede']) . "</td>";
								echo "<td>" . htmlspecialchars($row['cantidad']) . "</td>";
								if ($has_fecha_entrega) echo "<td>" . htmlspecialchars($row['fecha_entrega']) . "</td>";
								echo "<td>
										<div class='dropdown'>
											<i class='bx bx-dots-vertical-rounded dots' onclick='toggleDropdown(this)'></i>
											<div class='dropdown-content'>
												<a href='nuevo1.html?id=" . urlencode($row['id']) . "'>✏️ Editar</a>
												<a href='#' onclick='eliminarProducto(" . htmlspecialchars($row['id']) . "); return false;'>🗑️ Eliminar</a>
											</div>
										</div>
									</td>";
								echo "</tr>";
							}
						} else {
							$colspan = 4 + ($has_fecha_entrega ? 1 : 0) + 1;
							echo "<tr><td colspan='{$colspan}' class='no-results'>No se encontraron resultados.</td></tr>";
						}
						?>
					</tbody>
				</table>
			</div>

			<!-- TABLA 2: Filtra por SEDE -->
			<div class="order">
				<div class="head"><h3>Artículos por Sede</h3></div>
				<table>
					<thead>
						<tr>
							<th>Sede</th>
							<th>Elemento</th>
							<th>Código</th>
							<th>Cantidad</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sql2 = "SELECT sede, nombre, id, cantidad FROM productos";
						if ($busqueda !== "") {
							$sql2 .= " WHERE sede LIKE '%$busqueda_esc%'";
						}
						$sql2 .= " ORDER BY sede ASC, id ASC";

						$resultado2 = $conn->query($sql2);
						if ($resultado2 && $resultado2->num_rows > 0) {
							while ($row = $resultado2->fetch_assoc()) {
								echo "<tr>";
								echo "<td>" . htmlspecialchars($row['sede']) . "</td>";
								echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
								echo "<td>" . htmlspecialchars($row['id']) . "</td>";
								echo "<td>" . htmlspecialchars($row['cantidad']) . "</td>";
								echo "<td>
										<div class='dropdown'>
											<i class='bx bx-dots-vertical-rounded dots' onclick='toggleDropdown(this)'></i>
											<div class='dropdown-content'>
												<a href='nuevo1.html?id=" . urlencode($row['id']) . "'>✏️ Editar</a>
												<a href='#' onclick='eliminarProducto(" . htmlspecialchars($row['id']) . "); return false;'>🗑️ Eliminar</a>
											</div>
										</div>
									</td>";
								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='5' class='no-results'>No se encontraron artículos para esa sede.</td></tr>";
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</main>
</section>

<script>
function toggleDropdown(element) {
	document.querySelectorAll('.dropdown-content').forEach(menu => menu.style.display = 'none');
	const menu = element.nextElementSibling;
	if (!menu) return;
	menu.style.display = 'block';
	document.addEventListener('click', function(e){
		if(!element.parentElement.contains(e.target)){
			menu.style.display = 'none';
		}
	}, {once:true});
}

function eliminarProducto(id) {
	if(confirm("¿Eliminar este producto?")) {
		fetch('php/eliminar_producto.php', {
			method: 'POST',
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			body: 'id=' + encodeURIComponent(id)
		})
		.then(res => res.text())
		.then(data => {
			alert(data);
			const params = new URLSearchParams(window.location.search);
			window.location.href = window.location.pathname + (params.toString() ? ('?' + params.toString()) : '');
		})
		.catch(err => {
			alert('Error al eliminar: ' + err);
		});
	}
}
</script>
</body>
</html>






