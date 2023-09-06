<?php
include('../secure/class/marcas_codigos.php');  // Asegúrate de incluir el archivo correcto
$aMarcasCodigos = new MarcasCodigos($db);

$mensaje = '';

// Agregar Precio
if (isset($_POST['guardar'])) {
    $repuesto_id = $_POST['repuesto_id'];
    $precio = $_POST['precio'];
    $tipo_precio = $_POST['tipo_precio'];
    $precio_minimo = $_POST['precio_minimo'];
    $precio_sugerido = $_POST['precio_sugerido'];
    $precio_maximo = $_POST['precio_maximo'];
    $moneda_id = $_POST['moneda_id'];

    // Realiza la inserción en la base de datos
    $query = "INSERT INTO precios (repuesto_id, precio, tipo_precio, precio_minimo, precio_sugerido, precio_maximo, moneda_id, empresa_id) 
              VALUES ('$repuesto_id', '$precio', '$tipo_precio', '$precio_minimo', '$precio_sugerido', '$precio_maximo', '$moneda_id', '".$_SESSION['empresa_id']."')";
    
    if ($db->query($query)) {
        $mensaje = 'El precio se ha agregado correctamente.';
    } else {
        $mensaje = 'Error al agregar el precio: ' . $db->error;
    }
}

// Editar Precio
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $repuesto_id = $_POST['repuesto_id'];
    $precio = $_POST['precio'];
    $tipo_precio = $_POST['tipo_precio'];
    $precio_minimo = $_POST['precio_minimo'];
    $precio_sugerido = $_POST['precio_sugerido'];
    $precio_maximo = $_POST['precio_maximo'];
    $moneda_id = $_POST['moneda_id'];

    // Realiza la actualización en la base de datos
    $query = "UPDATE precios SET repuesto_id = '$repuesto_id', precio = '$precio', tipo_precio = '$tipo_precio', 
              precio_minimo = '$precio_minimo', precio_sugerido = '$precio_sugerido', precio_maximo = '$precio_maximo', 
              moneda_id = '$moneda_id' WHERE id = '$id'";
    
    if ($db->query($query)) {
        $mensaje = 'El precio se ha actualizado correctamente.';
    } else {
        $mensaje = 'Error al actualizar el precio: ' . $db->error;
    }
}

// Eliminar Precio
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    // Realiza la eliminación en la base de datos
    $query = "DELETE FROM precios WHERE id = '$id'";
    
    if ($db->query($query)) {
        $mensaje = 'El precio se ha eliminado correctamente.';
    } else {
        $mensaje = 'Error al eliminar el precio: ' . $db->error;
    }
}

// Obtener datos del precio a editar
function obtenerPrecioPorID($id) {
    global $db;
    
    $query = "SELECT * FROM precios WHERE id = '$id'";
    $result = $db->query($query);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Obtener la lista de repuestos para el select2
$queryRepuestos = "SELECT id, nombre FROM repuestos";
$resultRepuestos = $db->query($queryRepuestos);
$repuestos = $resultRepuestos;

// Obtener la lista de tipos de moneda para el select
$queryMonedas = "SELECT id, nombre FROM monedas";
$resultMonedas = $db->query($queryMonedas);
$monedas = $resultMonedas;
?>

<?php if (!empty($mensaje)) : ?>
    <!-- Mensaje de éxito o error -->
    <div class="alert alert-success" role="alert">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<div class="jumbotron py-4 bg-white border">
    <?php if (isset($_GET['editar'])) : ?>
        <?php
        $idEditar = $_GET['editar'];
        $precioEditar = obtenerPrecioPorID($idEditar);
        ?>
        <p class="lead">Edita el precio de forma rápida.</p>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
            <div class="form-group">
                <label for="repuesto_id">Repuesto:</label>
                <select name="repuesto_id" id="repuesto_id" class="form-control" required>
                    <option value="">Selecciona un repuesto</option>
                    <?php foreach ($repuestos as $repuesto) : ?>
                        <option value="<?php echo $repuesto['id']; ?>" <?php echo ($precioEditar['repuesto_id'] == $repuesto['id']) ? 'selected' : ''; ?>><?php echo $repuesto['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="precio">Costo:</label>
                <input type="text" name="precio" id="precio" class="form-control" required value="<?php echo $precioEditar['precio']; ?>">
            </div>
            <div class="form-group">
                <label for="tipo_precio">Tipo de Precio:</label>
                <select name="tipo_precio" id="tipo_precio" class="form-control" required>
                    <option value="">Selecciona un tipo de precio</option>
                    <option value="1" <?php echo ($precioEditar['tipo_precio'] == '1') ? 'selected' : ''; ?>>Precio ruta</option>
                    <option value="2" <?php echo ($precioEditar['tipo_precio'] == '2') ? 'selected' : ''; ?>>Precio tienda</option>
                    <option value="3" <?php echo ($precioEditar['tipo_precio'] == '3') ? 'selected' : ''; ?>>Precio normal</option>
                </select>
            </div>
            <div class="form-group">
                <label for="precio_minimo">Precio Mínimo:</label>
                <input type="text" name="precio_minimo" id="precio_minimo" class="form-control" required value="<?php echo $precioEditar['precio_minimo']; ?>">
            </div>
            <div class="form-group">
                <label for="precio_sugerido">Precio Sugerido:</label>
                <input type="text" name="precio_sugerido" id="precio_sugerido" class="form-control" required value="<?php echo $precioEditar['precio_sugerido']; ?>">
            </div>
            <div class="form-group">
                <label for="precio_maximo">Precio Máximo:</label>
                <input type="text" name="precio_maximo" id="precio_maximo" class="form-control" required value="<?php echo $precioEditar['precio_maximo']; ?>">
            </div>
            <div class="form-group">
                <label for="moneda_id">Moneda:</label>
                <select name="moneda_id" id="moneda_id" class="form-control" required>
                    <option value="">Selecciona una moneda</option>
                    <?php foreach ($monedas as $moneda) : ?>
                        <option value="<?php echo $moneda['id']; ?>" <?php echo ($precioEditar['moneda_id'] == $moneda['id']) ? 'selected' : ''; ?>><?php echo $moneda['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Agrega aquí los campos adicionales como Precio minimo, Precio sugerido, Precio maximo, tipo de moneda y tipo de cambio a quetzal. -->
            <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
        </form>
    <?php else : ?>
        <p class="lead">Agrega un nuevo precio.</p>
        <form action="" method="POST">
            <div class="form-group">
                <label for="repuesto_id">Repuesto:</label>
                <select name="repuesto_id" id="repuesto_id" class="form-control" required>
                    <option value="">Selecciona un repuesto</option>
                    <?php foreach ($repuestos as $repuesto) : ?>
                        <option value="<?php echo $repuesto['id']; ?>"><?php echo $repuesto['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="precio">Costo:</label>
                <input type="text" name="precio" id="precio" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="tipo_precio">Tipo de Precio:</label>
                <select name="tipo_precio" id="tipo_precio" class="form-control" required>
                    <option value="">Selecciona un tipo de precio</option>
                    <option value="1">Precio ruta</option>
                    <option value="2">Precio tienda</option>
                    <option value="3">Precio normal</option>
                </select>
            </div>
            <div class="form-group">
                <label for="precio_minimo">Precio Mínimo:</label>
                <input type="text" name="precio_minimo" id="precio_minimo" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="precio_sugerido">Precio Sugerido:</label>
                <input type="text" name="precio_sugerido" id="precio_sugerido" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="precio_maximo">Precio Máximo:</label>
                <input type="text" name="precio_maximo" id="precio_maximo" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="moneda_id">Moneda:</label>
                <select name="moneda_id" id="moneda_id" class="form-control" required>
                    <option value="">Selecciona una moneda</option>
                    <?php foreach ($monedas as $moneda) : ?>
                        <option value="<?php echo $moneda['id']; ?>"><?php echo $moneda['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Agrega aquí los campos adicionales como Precio minimo, Precio sugerido, Precio maximo, tipo de moneda y tipo de cambio a quetzal. -->
            <button type="submit" name="guardar" class="btn btn-primary">Agregar</button>
        </form>
    <?php endif; ?>
</div>

<table class="table table-striped table-bordered dt-responsive nowrap w-100" id="preciosTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Repuesto</th>
            <th>Costo</th>
            <th>Minimo</th>
            <th>Sugerido</th>
            <th>Maximo</th>
            <th>Tipo Precio</th>
            <th>Moneda</th>
            <th>Acciones</th>
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
        "responsive": true,
        "ajax": {
            "url": "ajax/get_data_table.php?method=precios", // Cambiar a la ruta correcta
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
            { "data": "repuesto_nombre" },
            { "data": "precio" },
            { "data": "minimo" },
            { "data": "sugerido" },
            { "data": "maximo" },
            { "data": "tipo_precio" },
            { "data": "moneda_nombre" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<div class="btn-group btn-group-toggle" data-toggle="buttons"><a href="?tipo=18&editar=' + row.id + '" class="btn btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>' +
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