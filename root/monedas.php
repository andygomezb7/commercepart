<?php
include('../secure/class/monedas.php'); // Asegúrate de incluir el archivo correcto
$aMonedas = new Monedas($db);

$mensaje = '';

// Agregar Moneda
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $tipo_cambio_quetzal = $_POST['tipo_cambio_quetzal'];

    if ($aMonedas->agregarMoneda($nombre, $tipo_cambio_quetzal)) {
        $mensaje = 'La moneda se ha agregado correctamente.';
    } else {
        $mensaje = 'Error al agregar la moneda.';
    }
}

// Editar Moneda
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $tipo_cambio_quetzal = $_POST['tipo_cambio_quetzal'];

    if ($aMonedas->editarMoneda($id, $nombre, $tipo_cambio_quetzal)) {
        $mensaje = 'La moneda se ha actualizado correctamente.';
    } else {
        $mensaje = 'Error al actualizar la moneda.';
    }
}

// Eliminar Moneda
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    if ($aMonedas->eliminarMoneda($id)) {
        $mensaje = 'La moneda se ha eliminado correctamente.';
    } else {
        $mensaje = 'Error al eliminar la moneda.';
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
        $monedaEditar = $aMonedas->obtenerMonedaPorID($idEditar);
        ?>
        <p class="lead">Edita la moneda de forma rápida.</p>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $monedaEditar['nombre']; ?>">
            </div>
            <div class="form-group">
                <label for="tipo_cambio_quetzal">Tipo de Cambio a Quetzal:</label>
                <input type="number" step="0.01" name="tipo_cambio_quetzal" id="tipo_cambio_quetzal" class="form-control" required value="<?php echo $monedaEditar['tipo_cambio']; ?>">
            </div>
            <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
        </form>
    <?php else : ?>
        <div class="d-flex">
            <p class="lead flex-fill">Crear una nueva moneda.</p>
            <a class="btn btn-success float-right text-light" href="javascript:void(0)" onclick="toggleForm($(this), '.formadding')"><i class="fa fa-plus"></i> Agregar</a>
        </div>
        <form action="" style="display:none;" class="formadding" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="tipo_cambio_quetzal">Tipo de Cambio a Quetzal:</label>
                <input type="number" step="0.01" name="tipo_cambio_quetzal" id="tipo_cambio_quetzal" class="form-control" required>
            </div>
            <button type="submit" name="guardar" class="btn btn-primary">Agregar</button>
        </form>
    <?php endif; ?>
</div>

<table class="table table-striped table-bordered dt-responsive nowrap w-100" id="monedasTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo de Cambio a Quetzal</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#monedasTable').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "ajax/get_data_table.php?method=monedas", // Cambiar a la ruta correcta
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
            { "data": "tipo_cambio_quetzal" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<div class="btn-group btn-group-toggle" data-toggle="buttons"><a href="?tipo=19&editar=' + row.id + '" class="btn btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>' +
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
