<?php
require_once "conexion.php";

$mensaje = "";
$error = "";


if (isset($_POST["agregar"])) {
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);

   if ($nombre == "" || $descripcion == "") {
    $error = "El nombre y la descripción de la categoría son obligatorios.";
} else {
        $sql = "INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)";
        $stmt = $conexion->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ss", $nombre, $descripcion);
            if ($stmt->execute()) {
                $mensaje = "Categoria agregada correctamente.";
            } else {
                $error = "No se pudo agregar la categoria.";
            }
            $stmt->close();
        } else {
            $error = "Error en la consulta.";
        }
    }
}


if (isset($_POST["editar"])) {
    $id = intval($_POST["id"]);
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);

    if ($nombre == "" || $descripcion == "") {
        $error = "El nombre de la categoria es obligatorio.";
    } else {
        $sql = "UPDATE categorias SET nombre = ?, descripcion = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssi", $nombre, $descripcion, $id);
            if ($stmt->execute()) {
                $mensaje = "Categoría actualizada correctamente.";
            } else {
                $error = "No se pudo actualizar la categoría.";
            }
            $stmt->close();
        }
    }
}


if (isset($_GET["eliminar"])) {
    $id = intval($_GET["eliminar"]);


    $sql = "SELECT COUNT(*) AS cantidad FROM productos WHERE categoria_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $datos = $resultado->fetch_assoc();
    $stmt->close();

    if ($datos["cantidad"] > 0) {
        $error = "No puedes eliminar esta categoria porque tiene productos asociados.";
    } else {
        $sql = "DELETE FROM categorias WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $mensaje = "Categoria eliminada correctamente.";
        } else {
            $error = "No se pudo eliminar la categoria.";
        }
        $stmt->close();
    }
}


$categoriaEditar = null;
if (isset($_GET["editar"])) {
    $id = intval($_GET["editar"]);
    $sql = "SELECT * FROM categorias WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $categoriaEditar = $resultado->fetch_assoc();
    }
    $stmt->close();
}


$categorias = $conexion->query("SELECT * FROM categorias ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias - Mi Papelería</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        :root {
            --primario: #1B3A7A;
            --acento: #C0186A;
            --tercero: #7030A0;
            --blanco: #ffffff;
            --gris: #f4f4f4;
            --texto: #222222;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            color: var(--texto);
        }

        .navbar {
            background: var(--primario);
            color: white;
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .navbar nav {
            display: flex;
            gap: 25px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .navbar a:hover {
            color: #ffd0e7;
        }

        .contenedor {
            width: 90%;
            max-width: 1100px;
            margin: 35px auto;
        }

        h1 {
            color: var(--primario);
            margin-bottom: 25px;
        }

        h2 {
            color: var(--tercero);
            margin-top: 35px;
        }

        .mensaje {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .exito {
            background: #e7f7ed;
            color: #176b3a;
            border: 1px solid #9bd5b1;
        }

        .error {
            background: #ffe7ed;
            color: #dfd3d7;
            border: 1px solid #f29ab5;
        }

        .formulario {
            background: white;
            padding: 25px;
            border-radius: 12px;
            border-top: 5px solid var(--tercero);
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .formulario label {
            display: block;
            margin-top: 12px;
            margin-bottom: 6px;
            font-weight: bold;
            color: var(--primario);
        }

        .formulario input,
        .formulario textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 15px;
            box-sizing: border-box;
        }

        .formulario textarea {
            resize: vertical;
            min-height: 90px;
        }

        .boton {
            margin-top: 18px;
            background: var(--acento);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 7px;
            cursor: pointer;
            font-weight: bold;
        }

        .boton:hover {
            opacity: 0.9;
        }

        .boton-cancelar {
            display: inline-block;
            margin-top: 18px;
            margin-left: 8px;
            padding: 12px 20px;
            background: var(--primario);
            color: white;
            text-decoration: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .tabla-contenedor {
            overflow-x: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--primario);
            color: white;
            padding: 14px;
            text-align: left;
        }

        td {
            padding: 13px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background: #faf5fc;
        }

        .acciones a {
            text-decoration: none;
            font-weight: bold;
            margin-right: 10px;
        }

        .btn-editar {
            color: #000000;
        }

        .btn-eliminar {
            color: #000000;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">Mi Papeleria</div>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="productos.php">Productos</a>
            <a href="categorias.php">Categorias</a>
        </nav>
    </div>

    <div class="contenedor">
        <h1>Gestión de Categorias</h1>

       
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje exito"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="mensaje error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        
        <div class="formulario">
            <h2><?php echo $categoriaEditar ? 'Editar Categoria' : 'Agregar Nueva Categoria'; ?></h2>
            <form action="categorias.php" method="POST">
                
                <?php if ($categoriaEditar): ?>
                    <input type="hidden" name="id" value="<?php echo $categoriaEditar['id']; ?>">
                <?php endif; ?>

                <label for="nombre">Nombre de la Categoria:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo $categoriaEditar ? htmlspecialchars($categoriaEditar['nombre']) : ''; ?>" required>

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"required><?php echo $categoriaEditar ? htmlspecialchars($categoriaEditar['descripcion']) : ''; ?></textarea>

                <?php if ($categoriaEditar): ?>
                    <button type="submit" name="editar" class="boton">Actualizar Categoria</button>
                    <a href="categorias.php" class="boton-cancelar">Cancelar</a>
                <?php else: ?>
                    <button type="submit" name="agregar" class="boton">Guardar Categoria</button>
                <?php endif; ?>

            </form>
        </div>

        
        <h2>Listado de Categorias</h2>
        <div class="tabla-contenedor">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($categorias && $categorias->num_rows > 0): ?>
                        <?php while ($cat = $categorias->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $cat['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($cat['nombre']); ?></strong></td>
                                <td><?php echo htmlspecialchars($cat['descripcion']); ?></td>
                                <td class="acciones">
                                    <a href="categorias.php?editar=<?php echo $cat['id']; ?>" class="btn-editar">Editar</a>
                                    <a href="categorias.php?eliminar=<?php echo $cat['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Seguro que deseas eliminar esta categoría?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No hay categorias registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>