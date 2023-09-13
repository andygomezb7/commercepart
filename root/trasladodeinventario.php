<?php
// Asegúrate de incluir y configurar la conexión a la base de datos aquí.

$mensaje = '';

// Realizar el traslado de inventario
if (isset($_POST['trasladar'])) {
    $repuestoId = $_POST['repuesto_id'];
    $bodegaOrigen = $_POST['bodega_origen'];
    $bodegaDestino = $_POST['bodega_destino'];
    $cantidad = $_POST['cantidad'];
    $usuarioId = $_POST['usuario_id'];
    $comentario = $_POST['comentario'];

    // Llama a la función para realizar el traslado de inventario
    if (trasladarInventario($repuestoId, $bodegaOrigen, $bodegaDestino, $cantidad, $usuarioId, $comentario)) {
        $mensaje = 'El traslado de inventario se ha realizado correctamente.';
    } else {
        $mensaje = 'Error al realizar el traslado de inventario.';
    }
}

// Obtener la lista de usuarios para el select
$queryUsuarios = "SELECT id, nombre FROM usuarios WHERE empresa_id = " . $_SESSION['empresa_id'];
$usuarios = $db->query($queryUsuarios);

// Obtener la lista de bodegas para el select
$bodegas = $db->query("SELECT id, nombre FROM bodegas WHERE empresa_id = " . $_SESSION['empresa_id']);
?>

<?php if (!empty($mensaje)) : ?>
    <!-- Mensaje de éxito o error -->
    <div class="alert alert-success" role="alert">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<div class="jumbotron py-4 bg-white border">
    <p class="lead">Realiza un traslado de inventario de una bodega a otra.</p>
    <form action="" method="POST">
        <div class="form-group">
            <label for="repuesto_id">Repuesto:</label>
            <select name="repuesto_id" id="repuesto_id" class="form-control" required>
                <option value="">Selecciona un repuesto</option>
                <!-- Aquí debes cargar dinámicamente los repuestos desde la base de datos -->
            </select>
        </div>
        <div class="form-group">
            <label for="bodega_origen">Bodega de Origen:</label>
            <select name="bodega_origen" id="bodega_origen" class="form-control" required>
                <option value="">Selecciona la bodega de origen</option>
                <!-- Aquí debes cargar dinámicamente las bodegas desde la base de datos -->
            </select>
        </div>
        <div class="form-group">
            <label for="bodega_destino">Bodega de Destino:</label>
            <select name="bodega_destino" id="bodega_destino" class="form-control" required>
                <option value="">Selecciona la bodega de destino</option>
                <!-- Aquí debes cargar dinámicamente las bodegas desde la base de datos -->
            </select>
        </div>
        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" class="form-control" name="cantidad" id="cantidad" required>
        </div>
        <div class="form-group">
            <label for="comentario">Comentario:</label>
            <textarea class="form-control" name="comentario" id="comentario"></textarea>
        </div>
        <button type="submit" name="trasladar" class="btn btn-primary">Realizar Traslado</button>
    </form>
</div>

<div class="container mt-4">
    <h3>Traslados de Inventario</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Repuesto</th>
                <th>Bodega de Origen</th>
                <th>Bodega de Destino</th>
                <th>Cantidad</th>
                <th>Usuario</th>
                <th>Comentario</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <!-- Aquí debes cargar dinámicamente los traslados de inventario desde la base de datos -->
        </tbody>
    </table>
</div>

<!-- ... Tu código HTML anterior ... -->

<script>
$(document).ready(function() {
    // Función para verificar que las bodegas de origen y destino no sean iguales
    function validarBodegas() {
        var bodegaOrigen = $("#bodega_origen").val();
        var bodegaDestino = $("#bodega_destino").val();
        
        if (bodegaOrigen === bodegaDestino) {
            toastr.error("La bodega de origen y la bodega de destino no pueden ser iguales.");
            return false;
        }
        
        return true;
    }

    // Manejar el envío del formulario
    $("form").submit(function() {
        return validarBodegas();
    });
});
</script>
