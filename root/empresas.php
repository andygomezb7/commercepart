<?php
include('../secure/class/empresas.php'); // Asegúrate de incluir el archivo correcto
$aEmpresas = new Empresas($db);

if ($_SESSION['usuario_id']!=1) {
    header('location: index.php');
}

$mensaje = '';

// Agregar Empresa
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $nit = $_POST['nit'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    if ($aEmpresas->agregarEmpresa($nombre, $direccion, $nit, $telefono, $email)) {
        $mensaje = 'La empresa se ha agregado correctamente.';
    } else {
        $mensaje = 'Error al agregar la empresa.';
    }
}

// Editar Empresa
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $nit = $_POST['nit'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    if ($aEmpresas->editarEmpresa($id, $nombre, $direccion, $nit, $telefono, $email)) {
        $mensaje = 'La empresa se ha actualizado correctamente.';
    } else {
        $mensaje = 'Error al actualizar la empresa.';
    }
}

// Eliminar Empresa
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    if ($aEmpresas->eliminarEmpresa($id)) {
        $mensaje = 'La empresa se ha eliminado correctamente.';
    } else {
        $mensaje = 'Error al eliminar la empresa.';
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
        $empresaEditar = $aEmpresas->obtenerEmpresaPorID($idEditar);
        ?>
        <p class="lead">Edita la empresa de forma rápida.</p>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $empresaEditar['nombre']; ?>">
            </div>
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" name="direccion" id="direccion" class="form-control" required value="<?php echo $empresaEditar['direccion']; ?>">
            </div>
            <div class="form-group">
                <label for="nit">NIT:</label>
                <input type="text" name="nit" id="nit" class="form-control" required value="<?php echo $empresaEditar['nit']; ?>">
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" id="telefono" class="form-control" required value="<?php echo $empresaEditar['telefono']; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required value="<?php echo $empresaEditar['email']; ?>">
            </div>
            <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
        </form>
    <?php else : ?>
        <p class="lead">Agrega una nueva empresa.</p>
        <form action="" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" name="direccion" id="direccion" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="nit">NIT:</label>
                <input type="text" name="nit" id="nit" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" id="telefono" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <button type="submit" name="guardar" class="btn btn-primary">Agregar</button>
        </form>
    <?php endif; ?>
</div>

<table class="table table-striped table-bordered dt-responsive nowrap w-100" id="empresasTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Dirección</th>
            <th>NIT</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#empresasTable').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "ajax/get_data_table.php?method=empresas", // Cambiar a la ruta correcta
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
            { "data": "telefono" },
            { "data": "email" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<div class="btn-group btn-group-toggle" data-toggle="buttons"><a href="?tipo=103&editar=' + row.id + '" class="btn btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>' +
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
