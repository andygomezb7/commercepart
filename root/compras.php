<?php
// Incluir la clase ComprasManager
include('../secure/class/compras.php');
$comprasManager = new ComprasManager($db);

// Mensaje de éxito o error
$mensaje = '';
$agregar = @$_REQUEST['agregar'];

// Mensaje de éxito o error
$mensaje = '';

// Determinar si se está editando una compra existente o agregando una nueva
$editando = false;
$compraId = null;

if (isset($_GET['editar'])) {
    $editando = true;
    $compraId = $_GET['editar'];
}

// Obtener datos de la compra si se está editando
$compraExistente = array();

if ($editando && $compraId) {
    $compraExistente = $comprasManager->obtenerCompraPorId($compraId);

    if (empty($compraExistente)) {
        // Manejar el caso en que no se encuentra la compra a editar
        echo "La compra a editar no se encuentra.";
        exit;
    }
}

// Agregar o editar Compra
if (isset($_POST['guardar_compra'])) {
    $compraData = array(
        'cliente_id' => $_POST['cliente'],
        'nombre' => $_POST['nombre'],
        'vendedor_id' => $_POST['vendedor'],
        'fecha_documento' => $_POST['fecha_documento'],
        'fecha_ofrecido' => $_POST['fecha_ofrecido'],
        'tipo_cambio' => $_POST['tipo_cambio'],
        'niveles_precio' => $_POST['niveles_precio']
    );

    $repuestos = $_POST['repuestos'];

    if ($editando && $compraId) {
        // Si se está editando, actualiza la compra existente
        $result = $comprasManager->editarCompra($compraId, $compraData, $repuestos);
        $mensaje = 'Compra editada correctamente.';
    } else {
        // Si no se está editando, agrega una nueva compra
        $result = $comprasManager->agregarCompra($compraData, $repuestos);
        $mensaje = 'Compra agregada correctamente.';
    }

    if (!$result) {
        $mensaje = 'Error al guardar la compra.';
    }
}
?>

<?php if (!empty($mensaje)) : ?>
    <div class="alert alert-success" role="alert">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<?php if ($agregar || $editando) { ?>
<form action="" method="POST">
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="cliente">Cliente:</label>
            <select name="cliente" id="cliente" class="form-control" required>
                <option>Selecciona una opción</option>
                <?php
                // Reemplaza esto con tu lógica para cargar los clientes desde la base de datos
                $clientes = $comprasManager->obtenerClientes(); // Debes implementar esta función
                foreach ($clientes as $cliente) {
                    $selected = ($editando && $compraExistente['cliente_id'] == $cliente['id']) ? 'selected' : '';
                    echo "<option value='{$cliente['id']}' $selected>{$cliente['nombre']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="nit">NIT:</label>
            <input type="text" name="nit" id="nit" class="form-control" value="<?php echo ($editando) ? $compraExistente['nit'] : ''; ?>" readonly>
        </div>
        <div class="form-group col-md-3">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo ($editando) ? $compraExistente['nombre'] : ''; ?>" readonly>
        </div>
        <div class="form-group col-md-3">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo ($editando) ? $compraExistente['email'] : ''; ?>" readonly>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="vendedor">Vendedor:</label>
            <select name="vendedor" id="vendedor" class="form-control" required>
                <option>Selecciona una opción</option>
                <?php
                // Reemplaza esto con tu lógica para cargar los vendedores desde la base de datos
                $vendedores = $comprasManager->obtenerVendedores(); // Debes implementar esta función
                foreach ($vendedores as $vendedor) {
                    $selected = ($editando && $compraExistente['vendedor_id'] == $vendedor['id']) ? 'selected' : '';
                    echo "<option value='{$vendedor['id']}' $selected>{$vendedor['nombre']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="fecha_documento">Fecha de Documento:</label>
            <input type="date" name="fecha_documento" value="<?php echo date('Y-m-d'); ?>" id="fecha_documento" class="form-control" required>
        </div>
        <div class="form-group col-md-3">
            <label for="fecha_ofrecido">Fecha Ofrecido:</label>
            <input type="date" name="fecha_ofrecido" value="<?php echo ($editando) ? $compraExistente['fecha_ofrecido'] : ''; ?>" id="fecha_ofrecido" class="form-control" required>
        </div>
        <div class="form-group col-md-3">
            <label for="tipo_cambio">Tipo de Cambio:</label>
            <input type="text" name="tipo_cambio" value="<?php echo ($editando) ? $compraExistente['tipo_cambio'] : ''; ?>" id="tipo_cambio" class="form-control" required>
        </div>
    </div>

    <div class="form-group col-md-3 px-1">
        <label for="niveles_precio">Niveles de Precio:</label>
        <select name="niveles_precio" id="niveles_precio" class="form-control" required>
            <option>Selecciona una opción</option>
            <option value="1" <?php echo ($editando && $compraExistente['niveles_precio'] == 1) ? 'selected' : ''; ?> >Público</option>
            <!-- Agrega aquí otras opciones si las tienes -->
        </select>
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
                                <th class="text-left">Descripción</th>
                                <th class="text-right">Código</th>
                                <th class="text-right" style="width: 12%;">Cantidad</th>
                                <th class="text-right" style="width: 14%;">Valor</th>
                                <th class="text-right" style="width: 7%;">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="repuestos-list">
                            <?php
                            // Si estás editando una compra, obtén los repuestos asociados a esa compra
                            if ($editando && !empty($compraId)) {
                                $repuestosCompra = $comprasManager->obtenerRepuestosDeCompra($compraId);

                                foreach ($repuestosCompra as $repuesto) {
                                    $repuestoId = $repuesto['id'];
                                    $result = $db->query("SELECT codigo FROM codigos_repuesto WHERE id_repuesto='$repuestoId'");
                                    $codigosAsignados = [];
                                    foreach ($result as $row) {
                                        $codigosAsignados[] = $row['codigo'];
                                    }

                                    echo '<tr>';
                                    echo '<td class="text-left">';
                                    echo '<h3>';
                                    echo '<input name="repuestos[' . $repuesto['id'] . '][repuesto_id]" type="hidden" value="' . $repuesto['id'] . '">';
                                    echo '<a target="_blank" href="javascript:void(0);">' . $repuesto['nombre'] . '</a>';
                                    echo '</h3>';
                                    echo '</td>';
                                    echo '<td class="unit">' . implode(', ', $codigosAsignados) . '</td>';
                                    echo '<td class="qty">';
                                    echo '<input name="repuestos[' . $repuesto['id'] . '][precio]" type="hidden" value="' . $repuesto['precio'] . '">';
                                    echo '<input class="form-control" name="repuestos[' . $repuesto['id'] . '][cantidad]" onchange="$(\'.repuestos-list\').modificarOrden(' . $repuesto['id'] . ', false, false, this.value);" type="number" value="' . $repuesto['cantidad'] . '">';
                                    echo '</td>';
                                    echo '<td class="total">';
                                    echo '<div class="input-group mb-3">';
                                    echo '<div class="input-group-prepend">';
                                    echo '<span class="input-group-text" id="basic-addon1">Q</span>';
                                    echo '</div>';
                                    echo '<input type="text" class="form-control" onchange="$(\'.repuestos-list\').modificarCostoOrden(' . $repuesto['id'] . ', this.value)" name="repuestos[' . $repuesto['id'] . '][costo]" value="' . $repuesto['precio'] . '" placeholder="">';
                                    echo '</div>';
                                    echo '</td>';
                                    echo '<td><a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="$(\'.repuestos-list\').eliminarOrden(' . $repuesto['id'] . ');"><i class="fas fa-times"></i></a></td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
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
                    <div class="notices">
                        <div>ALERTA:</div>
                        <div class="notice">Una vez se ingrese la compra en estado completado estará disponible para su procesamiento.</div>
                    </div>
                </main>
                <footer>Este no es un documento fiscal, es un registro interno.</footer>
            </div>
            <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
            <div></div>
        </div>
    </div>

    <button type="submit" name="guardar_compra" class="btn btn-primary">
        <?php echo ($editando) ? 'Guardar Edición' : 'Agregar Compra'; ?>
    </button>
</form>

<script type="text/javascript" src="../scripts/pedidos_query.js"></script>
<script>

    // Función para obtener los datos del cliente seleccionado
    function obtenerDatosCliente() {
        var clienteId = document.getElementById("cliente").value;

        // Hacer una solicitud AJAX al servidor para obtener los datos del cliente
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "ajax/get_cliente.php?cliente_id=" + clienteId, true);

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Parsear la respuesta JSON
                var clienteData = JSON.parse(xhr.responseText);

                // Llenar los campos de entrada de texto con los datos del cliente
                document.getElementById("nit").value = clienteData.nit;
                document.getElementById("nombre").value = clienteData.nombre;
                document.getElementById("email").value = clienteData.email;
            }
        };

        xhr.send();
    }

    $(document).ready(function() {
        $('#cliente').change(function() {
            // Aquí debes implementar la lógica para cargar los datos del cliente seleccionado
            const clienteId = $(this).val();
            obtenerDatosCliente(clienteId); // Debes implementar esta función
        });
        $('.repuestos-container').repuestoDropdown({
            url: 'ajax/get_data_dropdown.php?method=repuestos',
            stock: true,
            callback: function(repuestoId, element) {
                toastr.success(`${element.titulo} (${element.codigos}) Agregado correctamente`);
                $('.repuestos-list').agregarOrden(element.id, element.titulo, element.descripcion, element.codigos, true, element.valor, element.cantidad, true);
            }
        });

        // Código para obtener las órdenes y actualizar los totales
        $('.repuestos-list').on('ordenesActualizadas', function() {
            const ordenes = $(this).obtenerOrdenes();
            let elements = '';
            ordenes.forEach(function(element) {
                // <td class="no">${element.id}</td>
                elements += `<tr>
                                <td class="text-left">
                                    <h3>
                                        <input name="repuestos[${element.id}][repuesto_id]" type="hidden" value="${element.id}" />
                                        <a target="_blank" href="javascript:void(0);">
                                            ${element.titulo}
                                        </a>
                                    </h3>
                                    ${element.descripcion}</td>
                                <td class="unit">${element.codigos}</td>
                                <td class="qty">
                                    <input name="repuestos[${element.id}][precio]" type="hidden" value="${element.costo}" />
                                    <input class="form-control" name="repuestos[${element.id}][cantidad]" onchange="$('.repuestos-list').modificarOrden(${element.id}, false, false, this.value);" type="number" value="${element.cantidad}" />
                                </td>
                                <td class="total">
                                    <div class="input-group mb-3">
                                      <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">Q</span>
                                      </div>
                                      <input type="text" class="form-control" onchange="$('.repuestos-list').modificarCostoOrden(${element.id}, this.value)" name="repuestos[${element.id}][costo]" value="${element.costo}" placeholder="" />
                                    </div>
                                <td><a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="$('.repuestos-list').eliminarOrden(${element.id});"><i class="fas fa-times"></i></a></td>
                            </tr>`;
            })
            $(this).html(elements);

            //
            const totales = $(this).obtenerTotales(true);
            $('.invoice_subtotal').text(Number(totales.totalCosto).toFixed(2));
            $('.invoice_iva').text(Number((totales.totalConImpuestos - totales.totalCosto)).toFixed(2));
            $('.invoice_total').text(Number(totales.totalConImpuestos).toFixed(2));
        });

    });
</script>

<?php } else { ?>
<table class="table table-striped table-bordered dt-responsive nowrap w-100" id="monedasTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Vendedor</th>
            <th>Fecha documento</th>
            <th>Fecha ofrecido</th>
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
            "url": "ajax/get_data_table.php?method=compras", // Cambiar a la ruta correcta
            "type": "POST",
            "data": function (d) {
                d.start = d.start || d.draw || 0;
                d.length = d.length || 10;
                d.search = d.search.value || "";
                // Otros parámetros de búsqueda que quieras agregar
            },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id" },
            { "data": "cliente" },
            { "data": "vendedor" },
            { "data": "fecha_documento" },
            { "data": "fecha_ofrecido" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<div class="btn-group btn-group-toggle" data-toggle="buttons"><a href="?tipo=14&editar=' + row.id + '" class="btn btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>' +
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

<?php } ?>