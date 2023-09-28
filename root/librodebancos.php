<?php
include('../secure/class/marcas_codigos.php');  // Asegúrate de incluir el archivo correcto
$aMarcasCodigos = new MarcasCodigos($db);

$mensaje = '';

$query = "SELECT
    b.id AS Banco_ID,
    b.nombre_cuenta AS Nombre_Banco,
    movimientos.TipoCuenta,
    COALESCE(saldo_anterior.saldo_inicial, 0) AS Saldo_Inicial_Mes_Actual,
    COALESCE(total_debe, 0) AS Total_Debe,
    COALESCE(total_haber, 0) AS Total_Haber,
    COALESCE(saldo_anterior.saldo_inicial, 0) + COALESCE(total_debe, 0) - COALESCE(total_haber, 0) AS Saldo_Final_Mes_Actual
FROM banco b
LEFT JOIN (
    SELECT
        SUM(
            CASE
                WHEN cc.TipoCuenta = 'Ingresos' AND im.tipo = 'venta' THEN im.cantidad
                WHEN cc.TipoCuenta = 'Egresos' AND im.tipo = 'compra' THEN im.cantidad
                ELSE 0
            END
        ) AS total_debe,
        SUM(
            CASE
                WHEN cc.TipoCuenta = 'Egresos' AND im.tipo = 'venta' THEN im.cantidad
                WHEN cc.TipoCuenta = 'Ingresos' AND im.tipo = 'compra' THEN im.cantidad
                ELSE 0
            END
        ) AS total_haber, cc.ID, cc.TipoCuenta
    FROM inventario_movimientos im
    LEFT JOIN cuenta_contable AS cc ON (cc.TipoCuenta = 'Ingresos' AND im.tipo = 'venta') OR (cc.TipoCuenta = 'Egresos' AND im.tipo = 'compra')
    WHERE YEAR(im.fecha) = YEAR(CURRENT_DATE()) AND MONTH(im.fecha) = MONTH(CURRENT_DATE())
) AS movimientos ON b.cuenta_contable_defecto_id = movimientos.id
LEFT JOIN (
    SELECT
        b.banco_id AS banco_id,
        b.saldo_inicial
    FROM banco b
    WHERE MONTH(b.fecha_inicio_saldo) = MONTH(CURRENT_DATE()) - 1 AND YEAR(b.fecha_inicio_saldo) = YEAR(CURRENT_DATE())
) AS saldo_anterior ON b.banco_id = saldo_anterior.banco_id";
$queryData = $db->query($query);
$result = $queryData;

$debe = 0;
$haber = 0;
$total_mes_inicial = 0;
$total_saldo_final = 0;
if($result) {
    foreach($result AS $res) {
        $debe += $res['Total_Debe'];
        $haber += $res['Total_Haber'];
        $total_mes_inicial = $res['Saldo_Inicial_Mes_Actual'];
        $total_saldo_final = $res['Saldo_Final_Mes_Actual'];
    }
}

?>

<?php if (!empty($mensaje)) : ?>
    <!-- Mensaje de éxito o error -->
    <div class="alert alert-success" role="alert">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<div>
    <table class="table table-striped table-dark table-bordered col-md-4 mx-auto">
        <tbody>
        <tr>
            <th></th>
            <th>Debe</th>
            <th>Haber</th>
        </tr>
        <tr>
            <th>Total de movimientos</th>
            <td>Q.<?php echo $debe; ?></td>
            <td style="color:red">Q.<?php echo $haber; ?></td>
        </tr>
        <tr>
            <th>Saldo inicial en Bancos</th>
            <th colspan="2">Q.<?php echo $total_mes_inicial; ?></th>
        </tr>
        <tr>
            <th>Saldo Final en Bancos</th>
            <th colspan="2">Q<?php echo $total_saldo_final; ?></th>
        </tr>
    </tbody></table>
</div>

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
                // d.length = d.length || 10;
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