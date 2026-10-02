<?php
require_once "conexion.php";

$categorias = $conexion->query("SELECT COUNT(*) AS total FROM categorias")->fetch_assoc()['total'];
$productos = $conexion->query("SELECT COUNT(*) AS total FROM productos")->fetch_assoc()['total'];
$ventas = $conexion->query("SELECT COUNT(*) AS total FROM ventas")->fetch_assoc()['total'];
$proveedores = $conexion->query("SELECT COUNT(*) AS total FROM proveedores")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Papelería</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

<header class="navbar">
    <div class="logo">📚 Mi Papelería</div>

    <nav>
        <a href="index.php">Inicio</a>
        <a href="categorias.php">Categorias</a>
        <a href="productos.php">Productos</a>
        <a href="ventas.php">Ventas</a>
        <a href="proveedores.php">Proveedores</a>
    </nav>
</header>

<main class="contenedor">

    <section class="bienvenida">
        <h1>Sistema de Inventario</h1>
        <p>Administra fácilmente los productos y ventas de Mi Papelería.</p>
    </section>

    <section class="tarjetas">

        <div class="tarjeta">
            <div class="icono"></div>
            <h2>Categorías</h2>
            <p><?= $categorias ?> registradas</p>
            <a href="categorias.php" class="boton">Administrar</a>
        </div>

        <div class="tarjeta">
            <div class="icono"></div>
            <h2>Productos</h2>
            <p><?= $productos ?> registrados</p>
            <a href="productos.php" class="boton">Administrar</a>
        </div>

        <div class="tarjeta">
            <div class="icono"></div>
            <h2>Ventas</h2>
            <p><?= $ventas ?> registradas</p>
            <a href="ventas.php" class="boton">Ver ventas</a>
        </div>

        <div class="tarjeta">
            <div class="icono"></div>
            <h2>Proveedores</h2>
            <p><?= $proveedores ?> registrados</p>
            <a href="proveedores.php" class="boton">Administrar</a>
        </div>

    </section>

</main>

<footer>
    <p>Mi Papelería · Sistema de Inventario</p>
</footer>

</body>
</html>