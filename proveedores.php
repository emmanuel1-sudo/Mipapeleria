<?php
require_once "conexion.php";

$mensaje = "";
$error = "";


if (isset($_POST['agregar'])) {

    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);

    if ($nombre == "" || $telefono == "" || $correo == "") {

        $error = "Todos los campos son obligatorios.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $error = "El correo electrónico no es válido.";

    } else {

        $sql = "INSERT INTO proveedores
                (nombre, telefono, correo)
                VALUES (?, ?, ?)";

        $stmt = $conexion->prepare($sql);

        $stmt->bind_param(
            "sss",
            $nombre,
            $telefono,
            $correo
        );

        if ($stmt->execute()) {

            $mensaje = "Proveedor agregado correctamente.";

        } else {

            $error = "No se pudo agregar el proveedor.";
        }
    }
}



$proveedores = $conexion->query(
    "SELECT *
     FROM proveedores
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Proveedores - Mi Papelería</title>

    <link rel="stylesheet" href="estilos.css">

</head>

<body>

<header class="navbar">

    <div class="logo">
        📚 Mi Papelería
    </div>

    <nav>

        <a href="index.php">
            Inicio
        </a>

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

    <h1>Gestión de Proveedores</h1>


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


    

    <div class="formulario">

        <h2>Agregar proveedor</h2>

        <form method="POST">

            <label>
                Nombre:
            </label>

            <input
                type="text"
                name="nombre"
                required
            >


            <label>
                Teléfono:
            </label>

            <input
                type="text"
                name="telefono"
                required
            >


            <label>
                Correo:
            </label>

            <input
                type="email"
                name="correo"
                required
            >


            <button
                type="submit"
                name="agregar"
                class="boton"
            >
                Agregar proveedor
            </button>

        </form>

    </div>




    <h2>Proveedores registrados</h2>

    <div class="tabla-contenedor">

        <table>

            <tr>

                <th>ID</th>

                <th>Nombre</th>

                <th>Teléfono</th>

                <th>Correo</th>

            </tr>


            <?php while ($proveedor = $proveedores->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?= $proveedor['id'] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($proveedor['nombre']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($proveedor['telefono']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($proveedor['correo']) ?>
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