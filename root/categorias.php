<?php
// Mensaje de éxito o error
$mensaje = '';

// Agregar Categoría
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];

    $query = "INSERT INTO categorias (nombre) VALUES (?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $nombre);
    
    if ($stmt->execute()) {
        $mensaje = 'La categoría se ha agregado correctamente.';
    } else {
        $mensaje = 'Error al agregar la categoría.';
    }
}

// Editar Categoría
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];

    $query = "UPDATE categorias SET nombre = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('si', $nombre, $id);

    if ($stmt->execute()) {
        $mensaje = 'La categoría se ha actualizado correctamente.';
    } else {
        $mensaje = 'Error al actualizar la categoría.';
    }
}

// Eliminar Categoría
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    $query = "DELETE FROM categorias WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        $mensaje = 'La categoría se ha eliminado correctamente.';
    } else {
        $mensaje = 'Error al eliminar la categoría.';
    }
}

// Obtener la lista actualizada de categorías
$query = "SELECT * FROM categorias";
$result = $db->query($query);
$categorias = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Categorías</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>Dashboard - Listado de Categorías</h1>

        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de agregar/editar categoría -->
        <h2>Agregar/Editar Categoría</h2>
        <?php if (isset($_GET['editar'])) : ?>
            <?php
            $idEditar = $_GET['editar'];
            $categoriaEditar = $db->query("SELECT * FROM categorias WHERE id='$idEditar'")->fetch_assoc();
            ?>
            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $categoriaEditar['nombre']; ?>">
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

        <!-- Tabla de categorías -->
        <h2>Listado de Categorías</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria) : ?>
                    <tr>
                        <td><?php echo $categoria['id']; ?></td>
                        <td><?php echo $categoria['nombre']; ?></td>
                        <td>
                            <a href="?editar=<?php echo $categoria['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <form action="" method="POST" style="display: inline-block;">
                                <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
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
