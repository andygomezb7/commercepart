<?php
// Incluir la clase StockManager
include('../secure/class/stock.php');
$stockManager = new StockManager($db);

// Mensaje de éxito o error
$mensaje = '';

// Agregar Stock
if (isset($_POST['agregar_stock'])) {
    $facturaData = array(
        'serie_number' => $_POST['serie_number'],
        'invoice_number' => $_POST['invoice_number'],
        'autorization_number' => $_POST['autorization_number'],
        'certificate_number' => $_POST['certificate_number'],
        'inv_name' => $_POST['inv_name'],
        'inv_address' => $_POST['inv_address'],
        'inv_nit' => $_POST['inv_nit']
    );

    $repuestos = $_POST['repuestos'];

    $result = $stockManager->agregarStock($facturaData, $repuestos);

    if ($result) {
        $mensaje = 'Stock agregado correctamente.';
    } else {
        $mensaje = 'Error al agregar stock.';
    }
}
?>
    <?php if (!empty($mensaje)) : ?>
        <div class="alert alert-success" role="alert">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
    	<div class="form-row">
	        <div class="form-group col-md-3">
	            <label for="serie_number">Serie Number:</label>
	            <input type="text" name="serie_number" class="form-control" required>
	        </div>
	        <div class="form-group col-md-3">
	            <label for="invoice_number">Invoice Number:</label>
	            <input type="text" name="invoice_number" class="form-control" required>
	        </div>
	        <div class="form-group col-md-3">
	            <label for="autorization_number">Autorization Number:</label>
	            <input type="text" name="autorization_number" class="form-control" required>
	        </div>
	        <div class="form-group col-md-3">
	            <label for="certificate_number">Certificate Number:</label>
	            <input type="text" name="certificate_number" class="form-control" required>
	        </div>
    	</div>
    	<div class="form-row">
	        <div class="form-group col-md-6">
	            <label for="inv_name">Invoice Name:</label>
	            <input type="text" name="inv_name" class="form-control" required>
	        </div>
	        <div class="form-group col-md-6">
	            <label for="inv_nit">Invoice NIT:</label>
	            <input type="text" name="inv_nit" class="form-control" required>
	        </div>
    	</div>
        <div class="form-group">
            <label for="inv_address">Invoice Address:</label>
            <input type="text" name="inv_address" class="form-control" required>
        </div>

        <h2>Repuestos</h2>
        <div class="repuestos-container"></div>
        <div id="invoice">
            <div class="invoice overflow-auto" style="min-height: auto!important;">
                <div style="min-width: 600px">
                    <main>
					    <table class="table">
					        <thead>
					            <tr>
					                <th>ID</th>
					                <th class="text-left">Descripción</th>
					                <th class="text-right">Código</th>
					                <th class="text-right" style="width: 12%;">Cantidad</th>
					                <th class="text-right">Valor</th>
					                <th class="text-right" style="width: 7%;">Acción</th>
					            </tr>
					        </thead>
					        <tbody class="repuestos-list">
					        </tbody>
					        <tfoot style="display:none!important;">
					            <tr>
					                <td colspan="2"></td>
					                <td colspan="2">SUBTOTAL</td>
					                <td class="invoice_subtotal">Q0</td>
					            </tr>
					            <tr>
					                <td colspan="2"></td>
					                <td colspan="2">IVA 12%</td>
					                <td class="invoice_iva">Q0</td>
					            </tr>
					            <tr>
					                <td colspan="2"></td>
					                <td colspan="2">TOTAL A PAGAR</td>
					                <td class="invoice_total">Q0</td>
					            </tr>
					        </tfoot>
					    </table>
					    <!-- <div class="thanks">Thank you!</div> -->
                        <div class="notices">
                            <div>ALERTA:</div>
                            <div class="notice">Una vez se ingrese el stock en estado completado estará disponible para su venta.</div>
                        </div>
                    </main>
                    <footer>Este no es un documento fiscal, es un recibo interno.</footer>
                </div>
                <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
                <div></div>
            </div>
        </div>

        <button type="submit" name="agregar_stock" class="btn btn-primary">Agregar Stock</button>
    </form>

    <script type="text/javascript" src="../scripts/pedidos_query.js"></script>
    <script>
        $(document).ready(function() {
            $('.repuestos-container').repuestoDropdown({
                url: 'ajax/get_data_dropdown.php?method=repuestos',
                stock: true,
                callback: function(repuestoId, element) {
                    toastr.success(`${element.titulo} (${element.codigos}) Agregado correctamente`);
                    $('.repuestos-list').agregarOrden(element.id, element.titulo, element.descripcion, element.codigos, true, element.valor, element.cantidad);
                }
            });

            // Código para obtener las órdenes y actualizar los totales
            $('.repuestos-list').on('ordenesActualizadas', function() {
            	const ordenes = $(this).obtenerOrdenes();
                let elements = '';
                ordenes.forEach(function(element) {
                    elements += `<tr>
                                    <td class="no">${element.id}</td>
                                    <td class="text-left">
                                        <h3>
                                            <a target="_blank" href="javascript:void(0);">
                                                ${element.titulo}
                                            </a>
                                        </h3>
                                        ${element.descripcion}</td>
                                    <td class="unit">${element.codigos}</td>
                                    <td class="qty">
                                        <input name="repuestos[${element.id}][precio]" type="hidden" value="${element.costo}" />
                                        <input class="form-control" name="repuestos[${element.id}][cantidad]" oninput="$('.repuestos-list').modificarOrden(${element.id}, false, false, this.value);" type="number" value="${element.cantidad}" />
                                    </td>
                                    <td class="total">Q${element.costo}</td>
                                    <td><a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="$('.repuestos-list').eliminarOrden(${element.id});"><i class="fas fa-times"></i></a></td>
                                </tr>`;
                })
                $(this).html(elements);

                //
                const totales = $(this).obtenerTotales();
                $('.invoice_subtotal').text(Number(totales.totalCosto).toFixed(2));
                $('.invoice_iva').text(Number((totales.totalConImpuestos-totales.totalCosto)).toFixed(2));
                $('.invoice_total').text(Number(totales.totalConImpuestos).toFixed(2));
            });

        });
    </script>
