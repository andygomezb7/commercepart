<?php
include('../secure/class/marcas_codigos.php');  // Asegúrate de incluir el archivo correcto
$aMarcasCodigos = new MarcasCodigos($db);

$mensaje = '';
?>

<?php if (!empty($mensaje)) : ?>
    <!-- Mensaje de éxito o error -->
    <div class="alert alert-success" role="alert">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<table class="table table-striped table-bordered dt-responsive nowrap w-100" id="preciosTable">
    <thead>
        <tr>
            <th>No. Folio</th>
            <th>No. Factura</th>
            <th>ID interno</th>
            <th>Fecha</th>
            <th>Tipo de registro</th>
            <th>Descripción</th>
            <th>Sub cuenta contable</th>
            <th>Debe</th>
            <th>Haber</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#preciosTable').DataTable({
        "processing": true,
        "serverSide": true,
        // "responsive": true,
        "ajax": {
            "url": "ajax/get_data_table.php?method=librodebancos", // Cambiar a la ruta correcta
            "type": "POST",
            "data": function (d) {
                d.start = (d.search.value !== '') ? 1 : 1; // Establece start en 1 si hay una búsqueda, de lo contrario en 0
                d.length = d.length || 10;
                d.draw++; // Incrementa el valor del draw en cada solicitud
                d.search = d.search.value || "";
                // Otros parámetros de búsqueda que quieras agregar
            },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id" },
            { "data": "id" },
            { "data": "id" },
            { "data": "fecha" },
            { "data": "tipo" },
            { "data": "descripcion" },
            { "data": "cuenta_contable" },
            { "data": "debe" },
            { "data": "haber" },
        ],
    });
});
</script>