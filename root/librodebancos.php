<?php
include('../secure/class/marcas_codigos.php');  // Asegúrate de incluir el archivo correcto
$aMarcasCodigos = new MarcasCodigos($db);

$mensaje = '';

$start_date = (@$_POST['startdate'] ? $_POST['startdate'] : date('Y-m-d', strtotime("-3 Months")));
$end_date = (@$_POST['enddate'] ? $_POST['enddate'] : date('Y-m-d'));

$postdates = ($start_date && $end_date ? '&start=' . $start_date .'&end=' . $end_date : '');

// - INTERVAL 1 MONTH
$query = "SELECT
    b.id AS banco_id,
    b.nombre_cuenta AS Nombre_Banco,
    b.descripcion,
    b.cuenta_contable_defecto_id,
    movimientos.TipoCuenta,
    movimientos.NombreCuenta,
    movimientos.cuenta_contable_id,
    COALESCE(
        saldo_anterior.saldo_inicial,
        0
    ) AS Saldo_Inicial_Mes_Actual,
    COALESCE(movimientos.total_debe, 0) AS total_debe,
    COALESCE(movimientos.total_haber, 0) AS total_haber,
    COALESCE(
        saldo_anterior.saldo_inicial,
        0
    ) + COALESCE(movimientos.total_debe, 0) - COALESCE(movimientos.total_haber, 0) AS Saldo_Final_Mes_Actual
FROM
    Banco b
LEFT JOIN(SELECT im.id,
       SUM( CASE WHEN im.tipo = 'venta' THEN pd.cantidad * pd.precio_unitario ELSE 0 END ) AS total_debe,
       SUM( CASE WHEN im.tipo = 'compra' THEN ca.cantidad * ca.precio ELSE 0 END ) AS total_haber,
       (CASE WHEN im.tipo = 'venta' THEN 'Ingresos' WHEN im.tipo = 'compra' THEN 'Egresos' END) AS TipoCuenta,
       cc.NombreCuenta,
       cc.ID AS cuenta_contable_id
FROM inventario_movimientos im
LEFT JOIN cuenta_contable AS cc ON (cc.TipoCuenta = 'Ingresos' AND im.tipo = 'venta') OR (cc.TipoCuenta = 'Egresos' AND im.tipo = 'compra')
LEFT JOIN pedido_detalles AS pd ON im.pedido_id = pd.id_pedido AND im.repuesto_id = pd.id_repuesto AND im.tipo = 'venta'
LEFT JOIN compras_articulos AS ca ON im.compra_id = ca.compra_id AND im.repuesto_id = ca.repuesto_id AND im.tipo = 'compra'
WHERE (im.fecha BETWEEN '".$start_date."' AND '".$end_date."') AND im.empresa_id = ".$_SESSION['empresa_id']." AND cc.empresa_id = ".$_SESSION['empresa_id']."
      AND cc.CuentaContablePadreID IS NULL
GROUP BY im.id, TipoCuenta, cc.NombreCuenta) AS movimientos
ON
    b.cuenta_contable_defecto_id = movimientos.cuenta_contable_id
LEFT JOIN(
    SELECT
        b.id AS banco_id,
        b.saldo_inicial
    FROM
        Banco b
    WHERE
        b.fecha_inicio_saldo BETWEEN '".$start_date."' AND '".$end_date."') AS saldo_anterior
    ON
        b.id = saldo_anterior.banco_id";
// var_dump($query);
$queryData = $db->query($query);
$result = $queryData;

$debe = 0;
$haber = 0;
$total_mes_inicial = 0;
$total_saldo_final = 0;
$bancosarray = array();
if($result) {
    foreach($result AS $res) {
        $debe += $res['total_debe'];
        $haber += $res['total_haber'];
        if(!@$bancosarray[$res['banco_id']]) {
            $bancosarray[$res['banco_id']] = $res['banco_id'];
            $total_mes_inicial += $res['Saldo_Inicial_Mes_Actual'];
            $total_saldo_final += $res['Saldo_Final_Mes_Actual'];
        }
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

<h5>Filtros</h5>
<form method="post" action="">
    <div class="form-row">
        <div class="form-group col-md-4" data-select2-id="select2-data-4-1vuz">
                <div class="input-group">
                    <input type="date" class="form-control" value="<?php echo $start_date; ?>" name="startdate">
                    <span class="input-group-addon"> &nbsp; <i class="fa fa-calendar"></i>  &nbsp; </span>
                    <input type="date" class="form-control" value="<?php echo $end_date; ?>" name="enddate">
                </div>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-success" value="Filtrar" />
        </div>
    </div>
</form>

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
        "responsive": false,
        "ajax": {
            "url": "ajax/get_data_table.php?method=librodebancos<?php echo $postdates; ?>", // Cambiar a la ruta correcta
            "type": "POST",
            "data": function (d) {
                // d.length = d.length || 10;
                d.search = d.search.value || "";
                // Otros parámetros de búsqueda que quieras agregar
            },
            "dataSrc": "data"
        },
        scrollX   : true,
        "columns": [
            { "data": "id", "width": "5%" },
            { "data": "id", "width": "5%" },
            { "data": "id", "width": "5%" },
            { "data": "fecha" },
            { "data": "tipo" },
            { "data": "descripcion" },
            { "data": "cuenta_contable" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return 'Q.' + row.debe;
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                        return 'Q.' + row.haber;
                }
            }
        ],
    });
});
</script>