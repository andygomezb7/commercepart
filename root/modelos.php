<?php
// Mensaje de éxito o error
$mensaje = '';

// Agregar Modelo
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];

    $db->query("INSERT INTO modelos (nombre) VALUES ('$nombre')");
    $mensaje = 'El modelo se ha agregado correctamente.';
}

// Editar Modelo
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];

    $db->query("UPDATE modelos SET nombre='$nombre' WHERE id='$id'");
    $mensaje = 'El modelo se ha actualizado correctamente.';
}

// Obtener la lista actualizada de modelos
$modelos = $db->query("SELECT * FROM modelos")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Modelos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>Dashboard - Listado de Modelos</h1>

        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de agregar/editar modelo -->
        <h2>Agregar/Editar Modelo</h2>
        <?php if (isset($_GET['editar'])) : ?>
            <?php
            $idEditar = $_GET['editar'];
            $modeloEditar = $db->query("SELECT * FROM modelos WHERE id='$idEditar'")->fetch_assoc();
            ?>
            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $modeloEditar['nombre']; ?>">
                </div>
                <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
            </form>
        <?php else : ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>
                <button type="submit" name="guardar" class="btn btn-primary">Agregar</button>
            </form>
        <?php endif; ?>

        <!-- Tabla de modelos -->
        <h2>Listado de Modelos</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($modelos as $modelo) : ?>
                    <tr>
                        <td><?php echo $modelo['id']; ?></td>
                        <td><?php echo $modelo['nombre']; ?></td>
                        <td>
                            <a href="?editar=<?php echo $modelo['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <form action="" method="POST" style="display: inline-block;">
                                <input type="hidden" name="id" value="<?php echo $modelo['id']; ?>">
                                <button type="submit" name="eliminar" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
