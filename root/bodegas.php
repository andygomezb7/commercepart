<?php
// Mensaje de éxito o error
$mensaje = '';

// Agregar Bodega
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];

    $db->query("INSERT INTO bodegas (nombre, direccion, empresa_id) VALUES ('$nombre', '$direccion', '".$_SESSION['empresa_id']."')");
    $mensaje = 'La bodega se ha agregado correctamente.';
}

// Editar Bodega
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];

    $db->query("UPDATE bodegas SET nombre='$nombre', direccion='$direccion' WHERE id='$id' AND empresa_id='".$_SESSION['empresa_id']."'");
    $mensaje = 'La bodega se ha actualizado correctamente.';
}

if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    $query = "DELETE FROM bodegas WHERE id = " . $id . " and empresa_id = " . $_SESSION['empresa_id'];
    $stmt = $db->prepare($query);

    if ($stmt->execute()) {
        $mensaje = 'La bodega se ha eliminado correctamente.';
    } else {
        $mensaje = 'Error al eliminar la bodega.';
    }
}

// Obtener la lista actualizada de bodegas
$bodegas = $db->query("SELECT * FROM bodegas WHERE empresa_id = " . $_SESSION['empresa_id']);
?>
        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de agregar/editar bodega -->
        <h2>Agregar/Editar Propiedades</h2>
        <?php if (isset($_GET['editar'])) : ?>
            <?php
            $idEditar = $_GET['editar'];
            $bodegaEditar = $db->query("SELECT * FROM bodegas WHERE id='$idEditar'")->fetch_assoc();
            ?>
            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $bodegaEditar['nombre']; ?>">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" name="direccion" id="direccion" class="form-control" required value="<?php echo $bodegaEditar['direccion']; ?>">
                </div>
                <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
            </form>
        <?php else : ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" name="direccion" id="direccion" class="form-control" required>
                </div>
                <button type="submit" name="guardar" class="btn btn-primary">Agregar</button>
            </form>
        <?php endif; ?>

        <!-- Tabla de bodegas -->
        <h2>Listado de Bodegas</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bodegas as $bodega) : ?>
                    <tr>
                        <td><?php echo $bodega['id']; ?></td>
                        <td><?php echo $bodega['nombre']; ?></td>
                        <td><?php echo $bodega['direccion']; ?></td>
                        <td>
                            <a href="?tipo=4&editar=<?php echo $bodega['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
<!--                             <form action="" method="POST" style="display: inline-block;">
                                <input type="hidden" name="id" value="<?php echo $bodega['id']; ?>">
                                <button type="submit" name="eliminar" value="Eliminar" class="btn btn-danger btn-sm">Eliminar</button>
                            </form> -->
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>