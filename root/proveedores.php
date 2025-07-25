<?php
include('../secure/class/proveedores.php');
$aProveedores = new Proveedores($db);

// Mensaje de éxito o error
$mensaje = '';

// Agregar Proveedor
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $nit = $_POST['nit'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    if ($aProveedores->agregarProveedor($nombre, $direccion, $nit, $telefono, $email)) {
        $mensaje = 'El proveedor se ha agregado correctamente.';
    } else {
        $mensaje = 'Error al agregar el proveedor.';
    }
}

// Editar Proveedor
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $nit = $_POST['nit'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    if ($aProveedores->editarProveedor($id, $nombre, $direccion, $nit, $telefono, $email)) {
        $mensaje = 'El proveedor se ha actualizado correctamente.';
    } else {
        $mensaje = 'Error al actualizar el proveedor.';
    }
}

// Eliminar Proveedor
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    if ($aProveedores->eliminarProveedor($id)) {
        $mensaje = 'El proveedor se ha eliminado correctamente.';
    } else {
        $mensaje = 'Error al eliminar el proveedor.';
    }
}
?>

<?php if (!empty($mensaje)) : ?>
    <div class="alert alert-success" role="alert">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<!-- Formulario de agregar/editar proveedor -->
<div class="jumbotron py-4 bg-white border">
    <?php if (isset($_GET['editar'])) : ?>
        <?php
        $idEditar = $_GET['editar'];
        $proveedorEditar = $aProveedores->obtenerProveedorPorID($idEditar);
        ?>
        <p class="lead">Edita el proveedor de forma rápida.</p>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $proveedorEditar['nombre']; ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="nombre">Dirección:</label>
                    <input type="text" name="direccion" id="direccion" class="form-control" required value="<?php echo $proveedorEditar['direccion']; ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nombre">NIT:</label>
                    <input type="number" name="nit" id="nit" class="form-control" required value="<?php echo $proveedorEditar['nit']; ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="nombre">Teléfono:</label>
                    <input type="text" name="telefono" id="telefono" class="form-control" required value="<?php echo $proveedorEditar['telefono']; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="nombre">Correo:</label>
                <input type="email" name="email" id="email" class="form-control" required value="<?php echo $proveedorEditar['email']; ?>">
            </div>
            <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
        </form>
    <?php else : ?>
        <div class="d-flex">
            <p class="lead flex-fill">Crear nuevo proveedor.</p>
            <a class="btn btn-success float-right text-light" href="javascript:void(0)" onclick="toggleForm($(this), '.formadding')"><i class="fa fa-plus"></i> Agregar</a>
        </div>
        <form action="" class="formadding" style="display:none;" method="POST">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="nombre">Dirección:</label>
                    <input type="text" name="direccion" id="direccion" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nombre">NIT:</label>
                    <input type="number" name="nit" id="nit" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="nombre">Teléfono:</label>
                    <input type="text" name="telefono" id="telefono" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label for="nombre">Correo:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <button type="submit" name="guardar" class="btn btn-primary">Agregar</button>
        </form>
    <?php endif; ?>
</div>

<!-- Tabla de proveedores -->
<table class="table table-striped table-bordered dt-responsive nowrap w-100" id="proveedoresTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>NIT</th>
            <!-- ... -->
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#proveedoresTable').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "ajax/get_data_table.php?method=proveedores", // Cambiar a la ruta correcta
            "type": "POST",
            "data": function (d) {
                // d.length = d.length || 10;
                d.search = d.search.value || "";
                // Otros parámetros de búsqueda que quieras agregar
            },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id" },
            { "data": "nombre" },
            { "data": "correo" },
            { "data": "nit" },
            // ...
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<div class="btn-group btn-group-toggle" data-toggle="buttons"><a href="?tipo=15&editar=' + row.id + '" class="btn btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>' +
                           '<form action="" method="POST" style="display: inline-block;">' +
                           '<input type="hidden" name="id" value="' + row.id + '">' +
                           '<button type="submit" name="eliminar" class="btn btn-danger rounded-0"><i class="fas fa-times"></i> Eliminar</button>' +
                           '</form></div>';
                }
            }
        ],
    });
});
</script>
