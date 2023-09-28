<?php
// Incluye los archivos necesarios y establece la conexión a la base de datos

// Asegúrate de incluir y configurar la conexión a la base de datos aquí.

$mensaje = '';

// Agregar Asignación de Bodega a Usuario
if (isset($_POST['guardar'])) {
    $usuario_id = $_POST['usuario_id'];
    $bodega_id = $_POST['bodega_id'];

    // Realiza la inserción en la base de datos
    $query = "INSERT INTO usuarios_bodegas (usuario_id, bodega_id, empresa_id) VALUES ('$usuario_id', '$bodega_id', '".$_SESSION['empresa_id']."')";
    
    if ($db->query($query)) {
        $mensaje = 'La bodega se ha asignado al usuario correctamente.';
    } else {
        $mensaje = 'Error al asignar la bodega al usuario: ' . $db->error;
    }
}

// Eliminar Asignación de Bodega a Usuario
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    // Realiza la eliminación en la base de datos
    $query = "DELETE FROM usuarios_bodegas WHERE id = '$id'";
    
    if ($db->query($query)) {
        $mensaje = 'La bodega se ha desasignado del usuario correctamente.';
    } else {
        $mensaje = 'Error al desasignar la bodega del usuario: ' . $db->error;
    }
}

// Obtener la lista de usuarios para el select
$queryUsuarios = "SELECT id, nombre FROM usuarios WHERE empresa_id = " . $_SESSION['empresa_id'];
$usuarios = $db->query($queryUsuarios);

// Obtener la lista de bodegas para el select
$bodegas = $db->query("SELECT id,nombre FROM bodegas WHERE empresa_id = " . $_SESSION['empresa_id']);
?>

<?php if (!empty($mensaje)) : ?>
    <!-- Mensaje de éxito o error -->
    <div class="alert alert-success" role="alert">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<div class="jumbotron py-4 bg-white border">
    <p class="lead">Asigna una bodega a un usuario.</p>
    <form action="" method="POST">
        <div class="form-group">
            <label for="usuario_id">Usuario:</label>
            <select name="usuario_id" id="usuario_id" class="form-control" required>
                <option value="">Selecciona un usuario</option>
                <?php foreach ($usuarios as $usuario) : ?>
                    <option value="<?php echo $usuario['id']; ?>"><?php echo $usuario['nombre']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="bodega_id">Bodega:</label>
            <select name="bodega_id" id="bodega_id" class="form-control" required>
                <option value="">Selecciona una bodega</option>
                <?php foreach ($bodegas AS $bodega) { ?>
                    <option value="<?php echo $bodega['id']; ?>"><?php echo $bodega['nombre']; ?></option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" name="guardar" class="btn btn-primary">Asignar</button>
    </form>
</div>

<table class="table table-striped table-bordered dt-responsive nowrap w-100" id="asignacionesTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Bodega</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#asignacionesTable').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "ajax/get_data_table.php?method=asignaciones", // Cambiar a la ruta correcta
            "type": "POST",
            "data": function (d) {
                // d.start = d.start || d.draw || 0;
                // d.length = d.length || 10;
                d.search = d.search.value || "";
                // Otros parámetros de búsqueda que quieras agregar
            },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id" },
            { "data": "usuario_nombre" },
            { "data": "bodega_name" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<form action="" method="POST" style="display: inline-block;">' +
                           '<input type="hidden" name="id" value="' + row.id + '">' +
                           '<button type="submit" name="eliminar" class="btn btn-danger rounded-0"><i class="fas fa-times"></i> Desasignar</button>' +
                           '</form>';
                }
            }
        ],
    });
});
</script>