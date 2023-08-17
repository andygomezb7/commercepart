<?php
// Mensaje de éxito o error
$mensaje = '';

// Agregar Marca
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];

    $db->query("INSERT INTO marcas (nombre) VALUES ('$nombre')");
    $mensaje = 'La marca se ha agregado correctamente.';
}

// Editar Marca
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];

    $db->query("UPDATE marcas SET nombre='$nombre' WHERE id='$id'");
    $mensaje = 'La marca se ha actualizado correctamente.';
}

// Obtener la lista actualizada de marcas
$marcas = $db->query("SELECT * FROM marcas")->fetch_all(MYSQLI_ASSOC);
?>
        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de agregar/editar marca -->
        <h2>Agregar/Editar Marca</h2>
        <?php if (isset($_GET['editar'])) : ?>
            <?php
            $idEditar = $_GET['editar'];
            $marcaEditar = $db->query("SELECT * FROM marcas WHERE id='$idEditar'")->fetch_assoc();
            ?>
            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $marcaEditar['nombre']; ?>">
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

        <!-- Tabla de marcas -->
        <h2>Listado de Marcas</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($marcas as $marca) : ?>
                    <tr>
                        <td><?php echo $marca['id']; ?></td>
                        <td><?php echo $marca['nombre']; ?></td>
                        <td>
                            <a href="?editar=<?php echo $marca['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <form action="" method="POST" style="display: inline-block;">
                                <input type="hidden" name="id" value="<?php echo $marca['id']; ?>">
                                <button type="submit" name="eliminar" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>