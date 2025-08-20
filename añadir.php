<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Añadir Cantidad</title>
    <link rel="stylesheet" href="./css/style.css">
    
</head>
<body>
    <div class="formulario">
        <a href="administrador.php">
            <img src="./img/deshacer.png" alt="Volver">
        </a>

        <h1>Añadir Cantidad</h1>

        <form action="php/añadir_cantidad.php" method="POST">
            <div class="input-container">
                <input type="number" name="id" id="id" required placeholder=" ">
                <label for="id">ID del producto</label>
            </div>

            <div class="input-container">
                <input type="number" name="cantidad" id="cantidad" required placeholder=" ">
                <label for="cantidad">Cantidad a añadir</label>
            </div>

            <button type="submit">Agregar</button>
        </form>
    </div>
</body>
</html>






