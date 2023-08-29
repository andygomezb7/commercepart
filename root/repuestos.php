<?php
// Mensaje de éxito o error
$mensaje = '';

// Editar Repuesto
if (isset($_GET['editar'])) {
    $idRepuestoEditar = $_GET['editar'];
    $repuestoEditar = $db->query("SELECT * FROM repuestos WHERE id = $idRepuestoEditar")->fetch_assoc();
}

// Agregar o Actualizar Repuesto
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $bodega = $_POST['bodega'];
    $marca = $_POST['marca'];
    $categoria = $_POST['categoria'];

    // Si se proporciona un ID, actualizar el repuesto existente
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $db->query("UPDATE repuestos SET nombre = '$nombre', descripcion = '$descripcion', precio = $precio, ubicacion_bodega = '$bodega', marca_id = '$marca', categoria_id = '$categoria' WHERE id = $id");
        $mensaje = 'El repuesto se ha actualizado correctamente.';
    } else { // Si no se proporciona un ID, agregar un nuevo repuesto
        $fecha_creacion = date("Y-m-d");
        $result = $db->query("INSERT INTO repuestos (nombre, descripcion, precio, ubicacion_bodega, fecha_creacion, marca_id, categoria_id) VALUES ('$nombre', '$descripcion', $precio, '$bodega', '$fecha_creacion', '$marca', '$categoria')");
        if (!$result) {
            // Ha ocurrido un error
            $error = mysqli_error($db);
            $mensaje = "Error en la consulta: " . $error;
        } else {
            $mensaje = 'El repuesto se ha agregado correctamente.';
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
    $bodegas = $db->query("SELECT id,nombre FROM bodegas");
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
            <form action="" method="POST">
                <?php if (isset($_GET['editar'])) : ?>
                    <input type="hidden" name="id" value="<?php echo $idRepuestoEditar; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo isset($repuestoEditar['nombre']) ? $repuestoEditar['nombre'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea name="descripcion" id="descripcion" class="form-control" required><?php echo isset($repuestoEditar['descripcion']) ? $repuestoEditar['descripcion'] : ''; ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="precio">Precio:</label>
                        <input type="number" name="precio" id="precio" class="form-control" value="<?php echo isset($repuestoEditar['precio']) ? $repuestoEditar['precio'] : ''; ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="categoria">Categoría:</label>
                        <select name="categoria" id="categoria" class="form-control" required>
                            <option>Selecciona una opción</option>
                            <?php foreach ($categorias as $categoria) : ?>
                                <option value="<?php echo $categoria['id']; ?>" <?php echo (isset($repuestoEditar['categoria_id']) && $repuestoEditar['categoria_id'] == $categoria['id']) ? 'selected' : ''; ?>><?php echo $categoria['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="marca">Marca:</label>
                        <select name="marca" id="marca" class="form-control" required>
                            <option>Selecciona una opción</option>
                            <?php foreach ($marcas as $marca) : ?>
                                <option value="<?php echo $marca['id']; ?>" <?php echo (isset($repuestoEditar['marca_id']) && $repuestoEditar['marca_id'] == $marca['id']) ? 'selected' : ''; ?>><?php echo $marca['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="bodega">Bodega:</label>
                        <select name="bodega" id="bodega" class="form-control" required>
                            <option>Selecciona una opción</option>
                            <?php foreach ($bodegas as $bodega) : ?>
                                <option value="<?php echo $bodega['id']; ?>" <?php echo (isset($repuestoEditar['ubicacion_bodega']) && $repuestoEditar['ubicacion_bodega'] == $bodega['id']) ? 'selected' : ''; ?>><?php echo $bodega['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" name="guardar" class="btn btn-primary"><?php echo isset($_GET['editar']) ? 'Actualizar' : 'Agregar'; ?></button>
                <a class="btn btn-light" href="?tipo=3">Regresar</a>
            </form>
        <?php else : ?>
            <!-- Tabla de repuestos -->
            <!-- <a href="?tipo=3&agregar=1" class="btn btn-success mb-3">Agregar Repuesto</a> -->
            <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="repuestosTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Bodega</th>
                        <th>Códigos</th>
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
            $(document).ready(function() {
                $('#repuestosTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "ajax": {
                        "url": "ajax/get_data_table.php?method=repuestos", // Cambiar a la ruta correcta
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
                        { "data": "nombre" },
                        { "data": "descripcion" },
                        { "data": "precio" },
                        { "data": "ubicacion_bodega" },
                        // { "data": "codigos" },
                        {
                            "data": null,
                            "render": function(data, type, row) {
                                let information = '';
                                if (row.codigos.split(',').length) {
                                    row.codigos.split(',').forEach(function (elemento) {
                                        information += '<a href="javascript:void(0)" onclick="viewCode(\''+ elemento +'\')">'+elemento+'</a>';
                                    })
                                }
                                return information;
                            }
                        },
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
            });
            </script>
        <?php endif; ?>