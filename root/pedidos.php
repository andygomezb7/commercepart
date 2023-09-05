<?php
include('../secure/class/pedidos.php');
$aPedidos = new Pedidos($db);

// Obtener el ID del usuario actual
$emailUsuario = $_SESSION['email'];
$queryUsuario = "SELECT id FROM usuarios WHERE email = '$emailUsuario'";
$resultadoUsuario = $db->query($queryUsuario);
$idUsuario = $resultadoUsuario->fetch_assoc()['id'];

// Obtener la lista de bodegas
$bodegas = $db->query("SELECT * FROM bodegas WHERE empresa_id = " . $_SESSION['empresa_id']);

// Obtener la lista de usuarios con tipo 3
$queryUsuariosTipo3 = "SELECT id, nombre FROM usuarios WHERE tipo = 3 AND empresa_id = " . $_SESSION['empresa_id'];
$resultUsuariosTipo3 = $db->query($queryUsuariosTipo3);
$usuariosTipo3 = $resultUsuariosTipo3;

// Mensaje de éxito o error
$mensaje = '';

// Agregar o Actualizar Pedido
$id = @$_POST['id'];
$savebutton = @$_POST['guardar'];
$id_usuario = $thisUser['id'];
$edit = intval(@$_GET['editar']);

if (isset($edit)) {
    $pedidoEditar = $db->query("SELECT * FROM pedidos WHERE id = " . $edit . " AND empresa_id = " . $_SESSION['empresa_id'])->fetch_assoc();
}

if (isset($savebutton)) {
    // Si se proporciona un ID, actualizar el pedido existente
    $id_empleado = $_REQUEST['id_empleado'];
    $id_cliente = $_REQUEST['cliente'];
    $dias_credito = $_REQUEST['dias_credito'];
    $id_transportista = $_REQUEST['id_transportista'];
    $detalle = $_REQUEST['detalles'];
    $fecha = $_REQUEST['fecha'];
    $id_pedido = $edit;

    $detalles = [];
    foreach ($detalle as $key => $value) {
        $detalles[] = array(
            'id_repuesto' => $key,
            'cantidad' => $value['cantidad'],
            'reserva' => $value['reserva'],
            'precio_unitario' => $value['precio'],
        );
    }
    if (empty($id)) {

        if ($aPedidos->ingresarPedido($id_usuario, $id_empleado, false, $dias_credito, $id_transportista,$fecha, $detalles)) {
            $mensaje = 'El pedido se ha ingresado correctamente.';
        } else {
            $mensaje = 'Error al ingresar el pedido.';
        }
    } else { // Si no se proporciona un ID, agregar un nuevo pedido
        if ($aPedidos->modificarPedido($id_pedido, $id_usuario, $id_empleado, false, $dias_credito, $id_transportista, $detalles)) {
            $mensaje =  "Pedido modificado exitosamente.";
        } else {
            $mensaje =  "Error al modificar el pedido.";
        }
    }
}

// Eliminar Pedido
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    $db->query("DELETE FROM Pedidos WHERE id = $id AND empresa_id = " . $_SESSION['empresa_id']);
    $mensaje = 'El pedido se ha eliminado correctamente.';
}
?>
        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['editar']) || isset($_GET['agregar'])) : ?>
            <!-- Formulario de agregar/editar -->
            <!-- <h2><?php echo isset($_GET['editar']) ? 'Editar Pedido' : 'Agregar Pedido'; ?></h2> -->
            <form action="" method="POST">
                <?php if (isset($_GET['editar'])) : ?>
                    <input type="hidden" name="id" value="<?php echo $_GET['editar']; ?>">
                <?php endif; ?>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="fecha">Fecha:</label>
                        <input type="date" name="fecha" id="fecha" class="form-control" value="<?php echo isset($pedidoEditar['fecha']) ? $pedidoEditar['fecha'] : date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="estado">Estado:</label>
                        <select name="estado" id="estado" class="form-control" required>
                            <option value="1" <?php echo (isset($pedidoEditar['estado']) && $pedidoEditar['estado'] == '1') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="2" <?php echo (isset($pedidoEditar['estado']) && $pedidoEditar['estado'] == '2') ? 'selected' : ''; ?>>En proceso</option>
                            <option value="3" <?php echo (isset($pedidoEditar['estado']) && $pedidoEditar['estado'] == '3') ? 'selected' : ''; ?>>Completado</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="id_empleado">Empleado:</label>
                        <select name="id_empleado" id="id_empleado" class="form-control" required>
                            <option>Selecciona un empleado</option>
                            <?php foreach ($usuariosTipo3 as $usuarioTipo3) : ?>
                                <option value="<?php echo $usuarioTipo3['id']; ?>" <?php echo (isset($pedidoEditar['id_empleado']) && $pedidoEditar['id_empleado'] == $usuarioTipo3['id']) ? 'selected' : ''; ?>><?php echo $usuarioTipo3['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="estado">Dias de crédito:</label>
                        <input type="number" name="dias_credito" id="dias_credito" class="form-control" value="<?php echo isset($pedidoEditar['dias_credito']) ? $pedidoEditar['dias_credito'] : '0'; ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="id_empleado">Transportista:</label>
                        <select name="id_transportista" id="id_transportista" class="form-control" required>
                            <option>Selecciona un empleado</option>
                            <?php foreach ($usuariosTipo3 as $usuarioTipo3) : ?>
                                <option value="<?php echo $usuarioTipo3['id']; ?>" <?php echo (isset($pedidoEditar['id_transportista']) && $pedidoEditar['id_transportista'] == $usuarioTipo3['id']) ? 'selected' : ''; ?>><?php echo $usuarioTipo3['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label for="fecha">NIT:</label>
                        <input type="text" placeholder="NIT (5555555-5)" name="cliente[nit]" id="cliente_nit" class="form-control" value="<?php echo isset($pedidoEditar['cliente_nit']) ? $pedidoEditar['cliente_nit'] : ''; ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fecha">Nombre:</label>
                        <input type="text" placeholder="Juan Gonzales" name="cliente[nombre]" id="cliente_nombre" class="form-control" value="<?php echo isset($pedidoEditar['cliente_nombre']) ? $pedidoEditar['cliente_nombre'] : ''; ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="fecha">Dirección:</label>
                        <input type="text" placeholder="Ciudad de Guatemala" name="cliente[direccion]" id="cliente_direccion" class="form-control" value="<?php echo isset($pedidoEditar['cliente_nit']) ? $pedidoEditar['cliente_nit'] : ''; ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fecha">Correo:</label>
                        <input type="text" placeholder="juan@gmail.com" name="cliente[correo]" id="cliente_correo" class="form-control" value="<?php echo isset($pedidoEditar['cliente_correo']) ? $pedidoEditar['cliente_correo'] : ''; ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="fecha">Observaciones:</label>
                    <textarea name="cliente[obs]" id="cliente[obs]" class="form-control" required>
                        <?php echo isset($pedidoEditar['cliente_obs']) ? $pedidoEditar['cliente_obs'] : ''; ?>
                    </textarea>
                </div>
                <div class="form-group">
                    <label for="fecha">Agregar elementos al pedido:</label>
                    <div class="repuesto-container">
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div id="invoice">
<!--                             <div class="toolbar hidden-print">
                                <div class="text-end">
                                    <button type="button" class="btn btn-dark"><i class="fa fa-print"></i> Print</button>
                                    <button type="button" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Export as PDF</button>
                                </div>
                                <hr>
                            </div> -->
                            <div class="invoice overflow-auto" style="min-height: auto!important;">
                                <div style="min-width: 600px">
<!--                                     <header>
                                        <div class="row">
                                            <div class="col">
                                                <a href="javascript:;">
                                                                <img src="assets/images/logo-icon.png" width="80" alt="">
                                                            </a>
                                            </div>
                                            <div class="col company-details">
                                                <h2 class="name">
                                                    <a target="_blank" href="javascript:;">
                                                Arboshiki
                                                </a>
                                                </h2>
                                                <div>455 Foggy Heights, AZ 85004, US</div>
                                                <div>(123) 456-789</div>
                                                <div>company@example.com</div>
                                            </div>
                                        </div>
                                    </header> -->
                                    <main>
<!--                                         <div class="row contacts">
                                            <div class="col invoice-to">
                                                <div class="text-gray-light">INVOICE TO:</div>
                                                <h2 class="to">John Doe</h2>
                                                <div class="address">796 Silver Harbour, TX 79273, US</div>
                                                <div class="email"><a href="mailto:john@example.com">john@example.com</a>
                                                </div>
                                            </div>
                                            <div class="col invoice-details">
                                                <h1 class="invoice-id">INVOICE 3-2-1</h1>
                                                <div class="date">Date of Invoice: 01/10/2018</div>
                                                <div class="date">Due Date: 30/10/2018</div>
                                            </div>
                                        </div> -->
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="text-left">Descripción</th>
                                                    <th class="text-right">Código</th>
                                                    <th class="text-right" style="width: 12%;">Cantidad</th>
                                                    <th class="text-right">Valor</th>
                                                    <th class="text-right" style="width: 7%;">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody class="ordenes_list">
                                                <?php
                                                    // Si estás editando una compra, obtén los repuestos asociados a esa compra
                                                    if (!empty($edit)) {
                                                        $repuestosPedido = $aPedidos->obtenerRepuestosDePedido($edit);

                                                        // foreach ($repuestosPedido as $repuesto) {
                                                        //     $repuestoId = $repuesto['id'];
                                                        //     $result = $db->query("SELECT codigo FROM codigos_repuesto WHERE id_repuesto='$repuestoId'");
                                                        //     $codigosAsignados = [];
                                                        //     foreach ($result as $row) {
                                                        //         $codigosAsignados[] = $row['codigo'];
                                                        //     }

                                                        //     echo '<tr>';
                                                        //     echo '<td class="text-left">';
                                                        //     echo '<h3>';
                                                        //     echo '<input name="repuestos[' . $repuesto['id'] . '][repuesto_id]" type="hidden" value="' . $repuesto['id'] . '">';
                                                        //     echo '<a target="_blank" href="javascript:void(0);">' . $repuesto['nombre'] . '</a>';
                                                        //     echo '</h3>';
                                                        //     echo '</td>';
                                                        //     echo '<td class="unit">' . implode(', ', $codigosAsignados) . '</td>';
                                                        //     echo '<td class="qty">';
                                                        //     echo '<input name="repuestos[' . $repuesto['id'] . '][precio]" type="hidden" value="' . $repuesto['precio'] . '">';
                                                        //     echo '<input class="form-control" name="repuestos[' . $repuesto['id'] . '][cantidad]" onchange="$(\'.repuestos-list\').modificarOrden(' . $repuesto['id'] . ', false, false, this.value);" type="number" value="' . $repuesto['cantidad'] . '">';
                                                        //     echo '</td>';
                                                        //     echo '<td class="total">';
                                                        //     echo '<div class="input-group mb-3">';
                                                        //     echo '<div class="input-group-prepend">';
                                                        //     echo '<span class="input-group-text" id="basic-addon1">Q</span>';
                                                        //     echo '</div>';
                                                        //     echo '<input type="text" class="form-control" onchange="$(\'.repuestos-list\').modificarCostoOrden(' . $repuesto['id'] . ', this.value)" name="repuestos[' . $repuesto['id'] . '][costo]" value="' . $repuesto['precio'] . '" placeholder="">';
                                                        //     echo '</div>';
                                                        //     echo '</td>';
                                                        //     echo '<td><a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="$(\'.repuestos-list\').eliminarOrden(' . $repuesto['id'] . ');"><i class="fas fa-times"></i></a></td>';
                                                        //     echo '</tr>';
                                                        // }
                                                    }
                                                    ?>
                                            </tbody>
                                            <tfoot>
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
<!--                                         <div class="notices">
                                            <div>ALERTA:</div>
                                            <div class="notice">Cualquier crédito tiene 30 dias para finalizarse de lo contrario entrara en mora.</div>
                                        </div> -->
                                    </main>
                                    <footer>Este no es un documento fiscal, es un recibo interno.</footer>
                                </div>
                                <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
                                <div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" name="guardar" class="btn btn-primary btn-lg"><i class="fas fa-check-circle"></i> <?php echo isset($_GET['editar']) ? 'Actualizar' : 'Agregar'; ?></button>
            </form>
            <script type="text/javascript" src="../scripts/pedidos_query.js"></script>
            <script>
                $(document).ready(function() {
                    $('.repuesto-container').repuestoDropdown({
                        url: 'ajax/get_data_dropdown.php?method=repuestos',  // Ruta a tu archivo PHP
                        callback: function(repuestoId, element) {
                            console.log('Orden agregada', element);
                            toastr.success(`${element.titulo} (${element.codigos}) Agregado correctamente`);
                            $('.ordenes_list').agregarOrden(element.id, element.titulo, element.descripcion, element.codigos, true, element.valor, parseInt(element.cantidad), parseInt(element.reserva));
                            // console.log(`Agregado al pedido: ${cantidad} x Repuesto ID ${repuestoId}`);
                            // Lógica para agregar al pedido aquí
                        }
                    });

                    $('.ordenes_list').on('ordenesActualizadas', function() {
                        const ordenes = $(this).obtenerOrdenes();
                        let elements = '';
                        ordenes.forEach(function(element) {
                            elements += `<tr>
                                            <td class="text-left">
                                                <h3>
                                                    <a target="_blank" href="javascript:void(0);">
                                                        ${element.titulo}
                                                    </a>
                                                </h3>
                                                ${element.descripcion}</td>
                                            <td class="unit">${element.codigos}</td>
                                            <td class="qty">
                                                <input name="detalles[${element.id}][precio]" type="hidden" value="${element.costo}" />
                                                ${(parseInt(element.reserva)>0 ? `<input class="form-control border border-dark" name="detalles[${element.id}][reserva]" oninput="$('.ordenes_list').modificarOrden(${element.id}, false, false, false, this.value);"  type="number" value="${element.reserva}" />` : `<input class="form-control border border-dark" name="detalles[${element.id}][reserva]" type="hidden" value="0" />`)}
                                                <input class="form-control" name="detalles[${element.id}][cantidad]" oninput="$('.ordenes_list').modificarOrden(${element.id}, false, false, this.value);" type="number" value="${element.cantidad}" />
                                            </td>
                                            <td class="total">Q${element.costo}</td>
                                            <td><a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="$('.ordenes_list').eliminarOrden(${element.id});"><i class="fas fa-times"></i></a></td>
                                        </tr>`;
                        })
                        $(this).html(elements);

                        //
                        const totales = $(this).obtenerTotales();
                        $('.invoice_subtotal').text(Number(totales.totalCosto).toFixed(2));
                        $('.invoice_iva').text(Number((totales.totalConImpuestos-totales.totalCosto)).toFixed(2));
                        $('.invoice_total').text(Number(totales.totalConImpuestos).toFixed(2));
                    });
                    <?php
                        if(is_array($repuestosPedido)) {
                            foreach ($repuestosPedido as $repuesto) {
                                $result = $db->query("SELECT codigo FROM codigos_repuesto WHERE id_repuesto='".$repuesto['id']."'");
                                $codigosAsignados = [];
                                foreach ($result as $row) {
                                    $codigosAsignados[] = $row['codigo'];
                                }
                                $codigos = json_encode($codigosAsignados);
                                echo "$('.ordenes_list').agregarOrden(".$repuesto['id'].", '".$repuesto['nombre']."', '".$repuesto['descripcion']."', ".$codigos.", true, '".$repuesto['precio']."', ".$repuesto['cantidad'].", ".$repuesto['reserva'].");";
                            }
                        }
                    ?>
                });
            </script>
        <?php else : ?>
            <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="monedasTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Cliente</th>
                        <th>Empleado</th>
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
                        "url": "ajax/get_data_table.php?method=pedidos", // Cambiar a la ruta correcta
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
                        { "data": "fecha" },
                        { "data": "estado" },
                        { "data": "usuario_nombre" },
                        { "data": "empleado" },
                        {
                            "data": null,
                            "render": function(data, type, row) {
                                return '<div class="btn-group btn-group-toggle" data-toggle="buttons"><a href="?tipo=12&editar=' + row.id + '" class="btn btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>' +
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
        <?php endif; ?>
