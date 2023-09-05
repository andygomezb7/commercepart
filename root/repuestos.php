<?php
// Mensaje de éxito o error
$mensaje = '';

// Editar Repuesto
$codigosEditar = '';
if (isset($_GET['editar'])) {
    $idRepuestoEditar = $_GET['editar'];
    $repuestoEditar = $db->query("SELECT * FROM repuestos WHERE id = $idRepuestoEditar")->fetch_assoc();
    $codigosEditar = $db->query("SELECT codigo FROM codigos_repuesto WHERE id_repuesto = $idRepuestoEditar");
}

// Agregar o Actualizar Repuesto
if (isset($_POST['guardar'])) {

    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = @$_POST['precio'];
    $bodega = @$_POST['bodega'];
    $marca = $_POST['marca'];
    $categoria = $_POST['categoria'];
    $codigos = $_POST['codigos'];
    $estado = $_POST['estado'];
    // $proveedor = $_POST['proveedor'];
    $originalcode = $_POST['originalcode'];
    // $codigoproveedor = $_POST['codigoproveedor'];

    // Manejo de la imagen
    $imagen = '';
    if (isset($_FILES['imagen'])) {
        $imagenTmp = $_FILES['imagen']['tmp_name'];
        $imagenNombre = $_FILES['imagen']['name'];
        $imagenExtension = pathinfo($imagenNombre, PATHINFO_EXTENSION);
        $imagenNombreGuardado = 'uploads/' . uniqid() . '.' . $imagenExtension;
        
        if (move_uploaded_file($imagenTmp, $imagenNombreGuardado)) {
            $imagen = 'root/'.$imagenNombreGuardado;
            $mensaje = 'Imagen subida exitosamente';
        } else {
            $mensaje = 'Error al guardar la nueva imagen<br>';
        }
    }

    if (!empty($imagen) && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $db->query("UPDATE repuestos SET imagen = '$imagen' WHERE id = $id");
    }

    // Si se proporciona un ID, actualizar el repuesto existente
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $db->query("UPDATE repuestos SET nombre = '$nombre', descripcion = '$descripcion', precio = '$precio', marca_id = '$marca', categoria_id = '$categoria', estado = '$estado', codigo_original = '$originalcode' WHERE id = $id");
        $mensaje .= 'El repuesto se ha actualizado correctamente.';

         // Eliminar los codigos anteriores
        $queryDeleteRepuestosCodigos = "DELETE FROM codigos_repuesto WHERE id_repuesto = " . $id;
        $stmtDeleteRepuestos = $db->prepare($queryDeleteRepuestosCodigos);
        $stmtDeleteRepuestos->execute();

        // INSERTAR LOS NUEVOS CODIGOS
        foreach ($codigos as $codigo) {
            $queryInsertCodigo = "INSERT INTO codigos_repuesto (id_repuesto, codigo) VALUES ('".$id."', '".$codigo."')";
            $stmtInsertCodigo = $db->prepare($queryInsertCodigo);
            $stmtInsertCodigo->execute();
        }

    } else { // Si no se proporciona un ID, agregar un nuevo repuesto
        $fecha_creacion = date("Y-m-d");
        $result = $db->query("INSERT INTO repuestos (nombre, descripcion, precio, fecha_creacion, marca_id, categoria_id, imagen, estado, codigo_original,empresa_id) VALUES ('$nombre', '$descripcion', $precio, '$fecha_creacion', '$marca', '$categoria', '$imagen', '$estado', '$originalcode', '".$_SESSION['empresa_id']."')");
        if (!$result) {
            // INSERTAR LOS NUEVOS CODIGOS
            $id = $db->insert_id;
            foreach ($codigos as $codigo) {
                $queryInsertCodigo = "INSERT INTO codigos_repuesto (id_repuesto, codigo, empresa_id) VALUES ('".$id."', '".$codigo."', '".$_SESSION['empresa_id']."')";
                $stmtInsertCodigo = $db->prepare($queryInsertCodigo);
                $stmtInsertCodigo->execute();
            }
            // Ha ocurrido un error
            $error = mysqli_error($db);
            $mensaje .= "Error en la consulta: " . $error;
        } else {
            $mensaje .= 'El repuesto se ha agregado correctamente.';
        }
    }
}

// Eliminar Repuesto
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    $db->query("DELETE FROM repuestos WHERE id = $id");
    $mensaje = 'El repuesto se ha eliminado correctamente.';
}

// Obtener la lista actualizada de repuestos
// $repuestos = $db->query("SELECT r.*, b.nombre as ubicacion_bodega FROM repuestos AS r LEFT JOIN bodegas AS b ON r.ubicacion_bodega = b.id")->fetch_assoc();

if (isset($_GET['editar']) || isset($_GET['agregar'])) {
    // Obtener la lista de bodegas
    // $bodegas = $db->query("SELECT id,nombre FROM bodegas");
    // proveedores
    // $proveedores = $db->query("SELECT id,nombre FROM proveedores");
    // Obtener la lista de categorias
    $categorias = $db->query("SELECT id,nombre FROM categorias");
    // Obtener la lista de marcas
    $marcas = $db->query("SELECT id,nombre FROM marcas_codigos");
}
?>

        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['editar']) || isset($_GET['agregar'])) : ?>
            <!-- Formulario de agregar/editar -->
            <h2><?php echo isset($_GET['editar']) ? 'Editar Repuesto' : 'Agregar Repuesto'; ?></h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <?php if (isset($_GET['editar'])) : ?>
                    <input type="hidden" name="id" value="<?php echo $idRepuestoEditar; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="nombre">Nombre<span class="text-danger font-weight-bold">*</span>:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo isset($repuestoEditar['nombre']) ? $repuestoEditar['nombre'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea name="descripcion" id="descripcion" class="form-control" required><?php echo isset($repuestoEditar['descripcion']) ? $repuestoEditar['descripcion'] : ''; ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="categoria">Categoría<span class="text-danger font-weight-bold">*</span>:</label>
                        <select name="categoria" id="categoria" class="form-control" required>
                            <option>Selecciona una opción</option>
                            <?php foreach ($categorias as $categoria) : ?>
                                <option value="<?php echo $categoria['id']; ?>" <?php echo (isset($repuestoEditar['categoria_id']) && $repuestoEditar['categoria_id'] == $categoria['id']) ? 'selected' : ''; ?>><?php echo $categoria['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="marca">Marca<span class="text-danger font-weight-bold">*</span>:</label>
                        <select name="marca" id="marca" class="form-control" required>
                            <option>Selecciona una opción</option>
                            <?php foreach ($marcas as $marca) : ?>
                                <option value="<?php echo $marca['id']; ?>" <?php echo (isset($repuestoEditar['marca_id']) && $repuestoEditar['marca_id'] == $marca['id']) ? 'selected' : ''; ?>><?php echo $marca['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="estado">Estado<span class="text-danger font-weight-bold">*</span>:</label>
                        <select name="estado" id="estado" class="form-control" required>
                            <option>Selecciona una opción</option>
                            <option value="1" <?php echo (isset($repuestoEditar['estado']) && $repuestoEditar['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                            <option value="2" <?php echo (isset($repuestoEditar['estado']) && $repuestoEditar['estado'] == 2) ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="originalcode">Código original:</label>
                        <input type="text" class="form-control" name="originalcode" value="<?php echo isset($repuestoEditar['codigo_original']) ? $repuestoEditar['codigo_original'] : ''; ?>" />
                    </div>
                </div>
<!--                 <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="proveedor">Proveedor<span class="text-danger font-weight-bold">*</span>:</label>
                        <select name="proveedor" id="proveedor" class="form-control" required>
                            <option>Selecciona una opción</option>
                            <?php foreach ($proveedores as $proveedor) : ?>
                                <option value="<?php echo $proveedor['id']; ?>" <?php echo (isset($repuestoEditar['proveedor']) && $repuestoEditar['proveedor'] == $proveedor['id']) ? 'selected' : ''; ?>><?php echo $proveedor['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="codigoproveedor">Código proveedor:</label>
                        <input type="text" class="form-control" name="codigoproveedor" value="<?php echo isset($repuestoEditar['proveedor_codigo']) ? $repuestoEditar['proveedor_codigo'] : ''; ?>" />
                    </div>
                </div> -->
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="bodega">Asignar códigos/equivalentes<span class="text-danger font-weight-bold">*</span>:</label>
                        <div id="codigoInputContainer"></div>
                    </div>
<!--                     <div class="form-group col-md-6">
                        <label for="bodega">Bodega:</label>
                        <select name="bodega" id="bodega" class="form-control" required>
                            <option>Selecciona una opción</option>
                            <?php foreach ($bodegas as $bodega) : ?>
                                <option value="<?php echo $bodega['id']; ?>" <?php echo (isset($repuestoEditar['ubicacion_bodega']) && $repuestoEditar['ubicacion_bodega'] == $bodega['id']) ? 'selected' : ''; ?>><?php echo $bodega['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div> -->
                </div>
                <div class="form-group mb-5">
                    <label for="imagen">Imagen:</label>
                    <input type="file" name="imagen" id="imagen" class="form-control-file" accept="image/*" onchange="previewImage(this);">
                    <img id="imagen-preview" src="<?php echo isset($repuestoEditar['imagen']) ? '../'.$repuestoEditar['imagen'] : '../styles/images/empty.png'; ?>" alt="Vista previa" class="mt-2" style="max-width: 200px;">
                </div>
                <button type="submit" name="guardar" class="btn btn-primary btn-lg"><i class="fas fa-check-circle"></i> <?php echo isset($_GET['editar']) ? 'Actualizar' : 'Agregar'; ?></button>
                <a class="btn btn-light" href="?tipo=3">Regresar</a>
            </form>
            <script>
            function previewImage(input) {
                var preview = document.getElementById('imagen-preview');
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $(document).ready(function() {
                $("#codigoInputContainer").generarCodigos({
                    codigosPreCargados: [<?php
                        $codigosArray = [];
                        if ($codigosEditar) {
                            foreach ($codigosEditar AS $row) {
                                $codigosArray[] = "'" . $row['codigo'] . "'";
                            }
                        }
                        echo implode(',', $codigosArray); 
                        ?>]
                });
            });
            </script>
        <?php else : ?>
            <!-- Tabla de repuestos -->
            <h5>Filtros</h5>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="bodega">Bodega:</label>
                    <select name="bodega" id="bodegas" class="form-control" required>
                        <option value="">Todas</option>
                        <?php 
                            $bodegas = $db->query("SELECT id,nombre FROM bodegas");
                            foreach ($bodegas as $bodega) : ?>
                            <option value="<?php echo $bodega['id']; ?>"><?php echo $bodega['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <!-- <a href="?tipo=3&agregar=1" class="btn btn-success mb-3">Agregar Repuesto</a> -->
            <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="repuestosTable">
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Bodega</th>
                        <th>Códigos</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán dinámicamente utilizando DataTables -->
                </tbody>
            </table>
            <!-- Agregar DataTables y configurar AJAX para cargar los datos -->
            <script>
            function viewCode(code) {
                $(this).modalPlugin({
                    title: 'Tu codigo',
                    content: 'you view the code: <b>'+code+'</b>.',
                    positiveBtnText: 'Done',
                    negativeBtnText: 'Cerrar',
                    // alertType: 'done',
                    // customButtons: [
                    //     { text: 'Custom Button 1', class: 'btn-info', callback: function() { alert('Custom Button 1 clicked!'); } },
                    //     { text: 'Custom Button 2', class: 'btn-warning', callback: function() { alert('Custom Button 2 clicked!'); } }
                    // ]
                    callback: function(accepted) {
                        if (callback && typeof callback === 'function') {
                            callback(accepted);
                        }
                    }
                });
            }
            function viewBodegas(code, title) {
                $.ajax({
                    url: 'ajax/get_data_modal.php?method=bodegas', // Reemplaza con la URL correcta
                    type: 'POST',
                    data: {
                        bodega: code
                    },
                    success: function(data) {
                        let bodegas = '';
                        if (JSON.parse(data).length) {
                            JSON.parse(data).forEach (function (data) {
                                bodegas += '<li>' + data.bodeganame + ' (' + (data.cantidad - data.reserva) + ') Reserva('+ data.reserva +')</li>';
                            }) 
                        } else {
                            bodegas += '<div class="alert alert-info">Sin inventario</div>';
                        }
                        $(this).modalPlugin({
                            title: 'Viendo: ' + title,
                            content: `<div>
                                        <p>Cantidad por bodegas</p>
                                        <ul>
                                            ${bodegas}
                                        </ul>
                                    </div>`,
                            positiveBtnText: 'Done',
                            negativeBtnText: 'Cerrar',
                            // alertType: 'done',
                            // customButtons: [
                            //     { text: 'Custom Button 1', class: 'btn-info', callback: function() { alert('Custom Button 1 clicked!'); } },
                            //     { text: 'Custom Button 2', class: 'btn-warning', callback: function() { alert('Custom Button 2 clicked!'); } }
                            // ]
                            callback: function(accepted) {
                                if (callback && typeof callback === 'function') {
                                    callback(accepted);
                                }
                            }
                        });
                    }
                });
            }
            var repuestostable;
            $(document).ready(function() {
                repuestostable = $('#repuestosTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "ajax": {
                        "url": "ajax/get_data_table.php?method=repuestos", // Cambiar a la ruta correcta
                        "type": "POST",
                        "data": function (d) {
                            d.start = (d.search.value !== '') ? 1 : 1; // Establece start en 1 si hay una búsqueda, de lo contrario en 0
                            d.length = d.length || 10;
                            d.draw++; // Incrementa el valor del draw en cada solicitud
                            d.search = d.search.value || "";
                            // d.start = d.start || d.draw || 0;
                            // d.length = d.length || 10;
                            // d.search = d.search.value || "";
                            // Otros parámetros de búsqueda que quieras agregar
                        },
                        "dataSrc": "data"
                    },
                    "columns": [
                        // { "data": "id" },
                        { "data": "nombre" },
                        { "data": "descripcion" },
                        { "data": "precio" },
                        {
                            "data": null,
                            "render": function(data, type, row) {
                                return '<a href="javascript:void(0)" onclick="viewBodegas(\''+ row.id +'\',\''+ row.nombre +'\')">'+row.cantidad+' <i class="fas fa-search"></i></a>';
                            }
                        },
                        { "data": "ubicacion_bodega" },
                        // { "data": "codigos" },
                        {
                            "data": null,
                            "render": function(data, type, row) {
                                let information = '';
                                if (row.codigos.split(',').length) {
                                    row.codigos.split(',').forEach(function (elemento) {
                                        information += '<a href="javascript:void(0)" onclick="viewCode(\''+ elemento +'\')">'+elemento+'</a> &nbsp;';
                                    })
                                }
                                return information;
                            }
                        },
                        { "data": "estado" },
                        {
                            "data": null,
                            "render": function(data, type, row) {
                                return '<div class="btn-group btn-group-toggle" data-toggle="buttons"><a href="?tipo=3&editar=' + row.id + '" class="btn btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>' +
                                       '<form action="" method="POST" style="display: inline-block;">' +
                                       '<input type="hidden" name="id" value="' + row.id + '">' +
                                       '<button type="submit" name="eliminar" class="btn btn-danger rounded-0"><i class="fas fa-times"></i> Eliminar</button>' +
                                       '</form></div>';
                            }
                        }
                    ],
                });
                $('#bodegas').on('change', function() {
                    var bodegasfiltro = $(this).val();
                    
                    // Realiza una solicitud Ajax al servidor con el filtro seleccionado
                    repuestostable.ajax.url('ajax/get_data_table.php?method=repuestos&bodegas='+bodegasfiltro).load();
                    // $.ajax({
                    //     url: 'ajax/get_data_table.php?method=repuestos', // Reemplaza con la URL correcta
                    //     type: 'POST',
                    //     data: {
                    //         bodegas: bodegasfiltro,
                    //         start: '1',
                    //         length: '10',
                    //         search: '',
                    //         order: '',
                    //         draw: repuestostable.settings()[0].oAjaxData.draw
                    //     },
                    //     success: function(data) {
                    //         // Actualiza el DataTable con los datos filtrados del servidor
                    //         repuestostable.clear().rows.add(JSON.parse(data)).draw();
                    //     }
                    // });
                });
            });
            </script>
        <?php endif; ?>