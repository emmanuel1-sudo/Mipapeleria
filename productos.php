<?php
require_once "conexion.php";

$mensaje = "";
$error = "";


if (isset($_POST['agregar'])) {
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $categoria = intval($_POST['categoria_id']);
    $marca = trim($_POST['marca']);

    if (empty($nombre)) {
    $error = "El nombre del producto es obligatorio.";
} elseif (empty($marca)) {
    $error = "La marca del producto es obligatoria.";
} elseif ($precio <= 0) {
        $error = "El precio debe ser mayor a cero.";
    } elseif ($stock < 0) {
        $error = "El stock no puede ser negativo.";
    } elseif ($categoria <= 0) {
        $error = "Debes seleccionar una categoría válida.";
    } else {
        $sql = "INSERT INTO productos (nombre, precio, stock, categoria_id, marca) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sdiis", $nombre, $precio, $stock, $categoria, $marca);

        if ($stmt->execute()) {
            $mensaje = "Producto agregado correctamente.";
        } else {
            $error = "Error al agregar el producto.";
        }
        $stmt->close();
    }
}


if (isset($_POST['editar'])) {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $categoria = intval($_POST['categoria_id']);
    $marca = trim($_POST['marca']);

  if (empty($nombre)) {
    $error = "El nombre del producto es obligatorio.";
} elseif (empty($marca)) {
    $error = "La marca del producto es obligatoria.";
} elseif ($precio <= 0) {
        $error = "El precio debe ser mayor a cero.";
    } elseif ($stock < 0) {
        $error = "El stock no puede ser negativo.";
    } else {
        $sql = "UPDATE productos SET nombre = ?, precio = ?, stock = ?, categoria_id = ?, marca = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sdiisi", $nombre, $precio, $stock, $categoria, $marca, $id);

        if ($stmt->execute()) {
            $mensaje = "Producto actualizado correctamente.";
        } else {
            $error = "Error al actualizar el producto.";
        }
        $stmt->close();
    }
}


if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);

    $consulta = $conexion->prepare("SELECT COUNT(*) AS total FROM ventas WHERE producto_id = ?");
    $consulta->bind_param("i", $id);
    $consulta->execute();
    $resultado = $consulta->get_result()->fetch_assoc();
    $consulta->close();

    if ($resultado['total'] > 0) {
        $error = "No puedes eliminar este producto porque tiene ventas registradas.";
    } else {
        $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $mensaje = "Producto eliminado correctamente.";
        } else {
            $error = "Error al eliminar el producto.";
        }
        $stmt->close();
    }
}


$productoEditar = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $productoEditar = $res->fetch_assoc();
    }
    $stmt->close();
}


$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre");

$productos = $conexion->query(
    "SELECT productos.*, categorias.nombre AS categoria
     FROM productos
     INNER JOIN categorias ON productos.categoria_id = categorias.id
     ORDER BY productos.id DESC"
);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Mi Papeleria</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
       
        .acciones a {
            text-decoration: none;
            font-weight: bold;
            margin-right: 10px;
            color: #0c0c0c;
        }
        .acciones a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header class="navbar">
    <div class="logo">📚 Mi Papelería</div>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="categorias.php">Categorías</a>
        <a href="productos.php">Productos</a>
        <a href="ventas.php">Ventas</a>
        <a href="proveedores.php">Proveedores</a>
    </nav>
</header>

<main class="contenedor">

    <h1>Gestión de Productos</h1>

    <?php if ($mensaje != ""): ?>
        <div class="exito"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <?php if ($error != ""): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

   
    <div class="formulario">
        <h2><?= $productoEditar ? 'Editar producto' : 'Agregar producto' ?></h2>

        <form method="POST" action="productos.php">

            <?php if ($productoEditar): ?>
                <input type="hidden" name="id" value="<?= $productoEditar['id'] ?>">
            <?php endif; ?>

            <label for="nombre">Nombre:</label>
            <input 
                type="text" 
                id="nombre"
                name="nombre" 
                value="<?= $productoEditar ? htmlspecialchars($productoEditar['nombre']) : '' ?>" 
                required
            >

            <label for="precio">Precio:</label>
            <input 
                type="number" 
                id="precio"
                name="precio" 
                step="0.01" 
                min="0.01" 
                value="<?= $productoEditar ? $productoEditar['precio'] : '' ?>" 
                required
            >

            <label for="stock">Stock:</label>
            <input 
                type="number" 
                id="stock"
                name="stock" 
                min="0" 
                value="<?= $productoEditar ? $productoEditar['stock'] : '' ?>" 
                required
            >

            <label for="categoria_id">Categoría:</label>
            <select id="categoria_id" name="categoria_id" required>
                <option value="">Selecciona una categoría</option>
                <?php 
               
                $categorias->data_seek(0);
                while ($categoria = $categorias->fetch_assoc()): 
                    $selected = ($productoEditar && $productoEditar['categoria_id'] == $categoria['id']) ? 'selected' : '';
                ?>
                    <option value="<?= $categoria['id'] ?>" <?= $selected ?>>
                        <?= htmlspecialchars($categoria['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="marca">Marca:</label>
            <input 
                type="text" 
                id="marca"
                name="marca" 
                maxlength="50" 
                placeholder="Ej: Norma"
                value="<?= $productoEditar ? htmlspecialchars($productoEditar['marca']) : '' ?>"
                required
            >

            <?php if ($productoEditar): ?>
                <button type="submit" name="editar" class="boton">Actualizar producto</button>
                <a href="productos.php" class="boton-cancelar">Cancelar</a>
            <?php else: ?>
                <button type="submit" name="agregar" class="boton">Agregar producto</button>
            <?php endif; ?>

        </form>
    </div>

   
    <h2>Productos registrados</h2>

    <div class="tabla-contenedor">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Categoría</th>
                    <th>Marca</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($productos && $productos->num_rows > 0): ?>
                    <?php while ($producto = $productos->fetch_assoc()): ?>
                    <tr>
                        <td><?= $producto['id'] ?></td>
                        <td><?= htmlspecialchars($producto['nombre']) ?></td>
                        <td>$<?= number_format($producto['precio'], 2) ?></td>
                        <td><?= $producto['stock'] ?></td>
                        <td><?= htmlspecialchars($producto['categoria']) ?></td>
                        <td><?= htmlspecialchars($producto['marca']) ?></td>
                        <td class="acciones">
                            <a href="productos.php?editar=<?= $producto['id'] ?>">Editar</a>
                            <a 
                                href="productos.php?eliminar=<?= $producto['id'] ?>" 
                                onclick="return confirm('¿Eliminar este producto?')"
                            >
                                Eliminar
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No hay productos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<footer>
    <p>Mi Papelería · Sistema de Inventario</p>
</footer>

</body>
</html>