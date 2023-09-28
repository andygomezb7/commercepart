<?php
include('../secure/class/clientes.php');
$aClientes = new Clientes($db);

$mensaje = '';

// Agregar Cliente
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $nit = $_POST['nit'];
    $email = $_POST['email'];

    if ($aClientes->agregarCliente($nombre, $direccion, $nit, $email)) {
        $mensaje = 'El cliente se ha agregado correctamente.';
    } else {
        $mensaje = 'Error al agregar el cliente.';
    }
}

// Editar Cliente
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $nit = $_POST['nit'];
    $email = $_POST['email'];

    if ($aClientes->editarCliente($id, $nombre, $direccion, $nit, $email)) {
        $mensaje = 'El cliente se ha actualizado correctamente.';
    } else {
        $mensaje = 'Error al actualizar el cliente.';
    }
}

// Eliminar Cliente
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    if ($aClientes->eliminarCliente($id)) {
        $mensaje = 'El cliente se ha eliminado correctamente.';
    } else {
        $mensaje = 'Error al eliminar el cliente.';
    }
}
?>

<?php if (!empty($mensaje)) : ?>
    <div class="alert alert-success" role="alert">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<div class="jumbotron py-4 bg-white border">
    <?php if (isset($_GET['editar'])) : ?>
        <?php
        $idEditar = $_GET['editar'];
        $clienteEditar = $aClientes->obtenerClientePorID($idEditar);
        ?>
        <p class="lead">Edita el cliente de forma rápida.</p>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $clienteEditar['nombre']; ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="direccion">Dirección:</label>
                    <input type="text" name="direccion" id="direccion" class="form-control" required value="<?php echo $clienteEditar['direccion']; ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nit">NIT:</label>
                    <input type="text" name="nit" id="nit" class="form-control" required value="<?php echo $clienteEditar['nit']; ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="email">Correo:</label>
                    <input type="email" name="email" id="email" class="form-control" required value="<?php echo $clienteEditar['email']; ?>">
                </div>
            </div>
            <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
        </form>
    <?php else : ?>
        <p class="lead">Crea un nuevo cliente.</p>
        <form action="" method="POST">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="direccion">Dirección:</label>
                    <input type="text" name="direccion" id="direccion" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nit">NIT:</label>
                    <input type="text" name="nit" id="nit" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="email">Correo:</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
            </div>
            <button type="submit" name="guardar" class="btn btn-primary">Agregar</button>
        </form>
    <?php endif; ?>
</div>

<table class="table table-striped table-bordered dt-responsive nowrap w-100" id="clientesTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Dirección</th>
            <th>NIT</th>
            <th>Correo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#clientesTable').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "ajax/get_data_table.php?method=clientes", // Cambiar a la ruta correcta
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
            { "data": "direccion" },
            { "data": "nit" },
            { "data": "email" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<div class="btn-group btn-group-toggle" data-toggle="buttons"><a href="?tipo=16&editar=' + row.id + '" class="btn btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>' +
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
