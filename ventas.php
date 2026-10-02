<?php
require_once "conexion.php";

$mensaje = "";
$error = "";

/* REGISTRAR VENTA */
if (isset($_POST['registrar'])) {

    $producto_id = intval($_POST['producto_id']);
    $cantidad = intval($_POST['cantidad']);

    if ($cantidad <= 0) {

        $error = "La cantidad debe ser mayor a cero.";

    } else {

        /* Buscar producto */
        $stmt = $conexion->prepare(
            "SELECT precio, stock FROM productos WHERE id = ?"
        );

        $stmt->bind_param("i", $producto_id);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 0) {

            $error = "El producto no existe.";

        } else {

            $producto = $resultado->fetch_assoc();

            $precio = $producto['precio'];
            $stock = $producto['stock'];

            /* Verificar stock */
            if ($cantidad > $stock) {

                $error = "No hay suficiente stock disponible.";

            } else {

                $total = $precio * $cantidad;

                /* Iniciar transacción */
                $conexion->begin_transaction();

                try {

                    /* Registrar venta */
                    $insertar = $conexion->prepare(
                        "INSERT INTO ventas
                        (producto_id, cantidad, fecha, total)
                        VALUES (?, ?, NOW(), ?)"
                    );

                    $insertar->bind_param(
                        "iid",
                        $producto_id,
                        $cantidad,
                        $total
                    );

                    $insertar->execute();

                    /* Reducir stock */
                    $nuevo_stock = $stock - $cantidad;

                    $actualizar = $conexion->prepare(
                        "UPDATE productos
                         SET stock = ?
                         WHERE id = ?"
                    );

                    $actualizar->bind_param(
                        "ii",
                        $nuevo_stock,
                        $producto_id
                    );

                    $actualizar->execute();

                    /* Confirmar */
                    $conexion->commit();

                    $mensaje = "Venta registrada correctamente. Total: $" .
                               number_format($total, 2);

                } catch (Exception $e) {

                    $conexion->rollback();

                    $error = "No se pudo registrar la venta.";
                }
            }
        }
    }
}

/* PRODUCTOS */
$productos = $conexion->query(
    "SELECT id, nombre, precio, stock
     FROM productos
     ORDER BY nombre"
);

/* VENTAS */
$ventas = $conexion->query(
    "SELECT
        ventas.id,
        productos.nombre AS producto,
        ventas.cantidad,
        ventas.total,
        ventas.fecha
     FROM ventas
     INNER JOIN productos
     ON ventas.producto_id = productos.id
     ORDER BY ventas.id DESC"
);
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Ventas - Mi Papelería</title>

    <link rel="stylesheet" href="estilos.css">

</head>

<body>

<header class="navbar">

    <div class="logo">
        📚 Mi Papelería
    </div>

    <nav>

        <a href="index.php">Inicio</a>

        <a href="categorias.php">
            Categorías
        </a>

        <a href="productos.php">
            Productos
        </a>

        <a href="ventas.php">
            Ventas
        </a>

        <a href="proveedores.php">
            Proveedores
        </a>

    </nav>

</header>

<main class="contenedor">

    <h1>Registro de Ventas</h1>

    <?php if ($mensaje != ""): ?>

        <div class="exito">
            <?= htmlspecialchars($mensaje) ?>
        </div>

    <?php endif; ?>

    <?php if ($error != ""): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <!-- FORMULARIO -->

    <div class="formulario">

        <h2>Registrar nueva venta</h2>

        <form method="POST">

            <label>Producto:</label>

            <select name="producto_id" required>

                <option value="">
                    Selecciona un producto
                </option>

                <?php while ($producto = $productos->fetch_assoc()): ?>

                    <option value="<?= $producto['id'] ?>">

                        <?= htmlspecialchars($producto['nombre']) ?>

                        -
                        $<?= number_format($producto['precio'], 2) ?>

                        -
                        Stock:
                        <?= $producto['stock'] ?>

                    </option>

                <?php endwhile; ?>

            </select>


            <label>Cantidad:</label>

            <input
                type="number"
                name="cantidad"
                min="1"
                required
            >


            <button
                type="submit"
                name="registrar"
                class="boton"
            >
                Registrar venta
            </button>

        </form>

    </div>


    <!-- LISTA -->

    <h2>Ventas registradas</h2>

    <div class="tabla-contenedor">

        <table>

            <tr>

                <th>ID</th>

                <th>Producto</th>

                <th>Cantidad</th>

                <th>Total</th>

                <th>Fecha</th>

            </tr>

            <?php while ($venta = $ventas->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?= $venta['id'] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($venta['producto']) ?>
                    </td>

                    <td>
                        <?= $venta['cantidad'] ?>
                    </td>

                    <td>
                        $<?= number_format($venta['total'], 2) ?>
                    </td>

                    <td>
                        <?= $venta['fecha'] ?>
                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</main>

<footer>

    <p>
        Mi Papelería · Sistema de Inventario
    </p>

</footer>

</body>

</html>