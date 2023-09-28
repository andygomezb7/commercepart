<?php
include('../secure/class/bancos.php'); // Asegúrate de incluir el archivo correcto
$aBancos = new Bancos($db);

include('../secure/class/cuenta_contable.php');  // Asegúrate de incluir el archivo correcto
$aCuentaContable = new CuentaContable($db);

$mensaje = '';

// Agregar Cuenta de Banco
if (isset($_POST['guardar'])) {
    $numero_cuenta = $_POST['numero_cuenta'];
    $tipo_cuenta_id = $_POST['tipo_cuenta_id'];
    $moneda_id = $_POST['moneda_id'];
    $nombre_cuenta = $_POST['nombre_cuenta'];
    $descripcion = $_POST['descripcion'];
    $saldo_inicial = $_POST['saldo_inicial'];
    $fecha_inicio_saldo = $_POST['fecha_inicio_saldo'];
    $cuenta_contable_defecto_id = $_POST['cuenta_contable_defecto_id'];
    $banco_id = $_POST['banco_id'];

    // Realiza la inserción en la base de datos
    $query = "INSERT INTO Banco (numero_cuenta, tipo_cuenta_id, moneda_id, nombre_cuenta, descripcion, saldo_inicial, fecha_inicio_saldo, cuenta_contable_defecto_id, banco_id, empresa_id) 
              VALUES ('$numero_cuenta', '$tipo_cuenta_id', '$moneda_id', '$nombre_cuenta', '$descripcion', '$saldo_inicial', '$fecha_inicio_saldo', '$cuenta_contable_defecto_id', '$banco_id', '".$_SESSION['empresa_id']."')";
    
    if ($db->query($query)) {
        $mensaje = 'La cuenta de banco se ha agregado correctamente.';
    } else {
        $mensaje = 'Error al agregar la cuenta de banco: ' . $db->error;
    }
}

// Editar Cuenta de Banco
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $numero_cuenta = $_POST['numero_cuenta'];
    $tipo_cuenta_id = $_POST['tipo_cuenta_id'];
    $moneda_id = $_POST['moneda_id'];
    $nombre_cuenta = $_POST['nombre_cuenta'];
    $descripcion = $_POST['descripcion'];
    $saldo_inicial = $_POST['saldo_inicial'];
    $fecha_inicio_saldo = $_POST['fecha_inicio_saldo'];
    $cuenta_contable_defecto_id = $_POST['cuenta_contable_defecto_id'];
    $banco_id = $_POST['banco_id'];

    // Realiza la actualización en la base de datos
    $query = "UPDATE Banco SET numero_cuenta = '$numero_cuenta', tipo_cuenta_id = '$tipo_cuenta_id', moneda_id = '$moneda_id', 
              nombre_cuenta = '$nombre_cuenta', descripcion = '$descripcion', saldo_inicial = '$saldo_inicial', 
              fecha_inicio_saldo = '$fecha_inicio_saldo', cuenta_contable_defecto_id = '$cuenta_contable_defecto_id', 
              banco_id = '$banco_id' WHERE id = '$id'";
    
    if ($db->query($query)) {
        $mensaje = 'La cuenta de banco se ha actualizado correctamente.';
    } else {
        $mensaje = 'Error al actualizar la cuenta de banco: ' . $db->error;
    }
}

// Eliminar Cuenta de Banco
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    // Realiza la eliminación en la base de datos
    $query = "DELETE FROM Banco WHERE id = '$id'";
    
    if ($db->query($query)) {
        $mensaje = 'La cuenta de banco se ha eliminado correctamente.';
    } else {
        $mensaje = 'Error al eliminar la cuenta de banco: ' . $db->error;
    }
}

// Obtener datos de la cuenta de banco a editar
function obtenerCuentaBancoPorID($id) {
    global $db;
    
    $query = "SELECT * FROM Banco WHERE id = '$id'";
    $result = $db->query($query);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Obtener la lista de tipos de cuenta de banco para el select
$queryTiposCuenta = "SELECT id, tipo as nombre FROM tipo_cuenta";
$resultTiposCuenta = $db->query($queryTiposCuenta);
$tiposCuenta = $resultTiposCuenta;

// Obtener la lista de tipos de moneda para el select
$queryMonedas = "SELECT id, nombre FROM monedas WHERE empresa_id = " .  $_SESSION['empresa_id'];
$resultMonedas = $db->query($queryMonedas);
$monedas = $resultMonedas;

// Obtener la lista de cuentas contables por defecto para el select
$queryCuentasContables = "SELECT id, nombre FROM cuentas_contables WHERE empresa_id = " .  $_SESSION['empresa_id'];
$resultCuentasContables = $db->query($queryCuentasContables);
$cuentasContables = $resultCuentasContables;

$tiposDeBanco = array(
    array('id' => 1, 'nombre' => 'G&T continental'),
    array('id' => 2, 'nombre' => 'Banco industrial'),
    array('id' => 3, 'nombre' => 'Banco de los trabajadores'),
    array('id' => 4, 'nombre' => 'Banrural'),
    array('id' => 5, 'nombre' => 'Bac'),
    array('id' => 6, 'nombre' => 'Promerica'),
);
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
        $cuentaBancoEditar = obtenerCuentaBancoPorID($idEditar);
        ?>
        <p class="lead">Edita la cuenta de banco de forma rápida.</p>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="numero_cuenta">Número de Cuenta:</label>
                    <input type="text" name="numero_cuenta" id="numero_cuenta" class="form-control" required value="<?php echo $cuentaBancoEditar['numero_cuenta']; ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="tipo_cuenta_id">Tipo de Cuenta:</label>
                    <select name="tipo_cuenta_id" id="tipo_cuenta_id" class="form-control" required>
                        <option value="">Selecciona un tipo de cuenta</option>
                        <?php foreach ($tiposCuenta as $tipoCuenta) : ?>
                            <option value="<?php echo $tipoCuenta['id']; ?>" <?php echo ($cuentaBancoEditar['tipo_cuenta_id'] == $tipoCuenta['id']) ? 'selected' : ''; ?>><?php echo $tipoCuenta['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nombre_cuenta">Nombre de la Cuenta:</label>
                    <input type="text" name="nombre_cuenta" id="nombre_cuenta" class="form-control" required value="<?php echo $cuentaBancoEditar['nombre_cuenta']; ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="moneda_id">Moneda:</label>
                    <select name="moneda_id" id="moneda_id" class="form-control" required>
                        <option value="">Selecciona una moneda</option>
                        <?php foreach ($monedas as $moneda) : ?>
                            <option value="<?php echo $moneda['id']; ?>" <?php echo ($cuentaBancoEditar['moneda_id'] == $moneda['id']) ? 'selected' : ''; ?>><?php echo $moneda['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" id="descripcion" class="form-control">
                    <?php echo $cuentaBancoEditar['descripcion']; ?>
                </textarea>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="saldo_inicial">Saldo Inicial:</label>
                    <input type="text" name="saldo_inicial" id="saldo_inicial" class="form-control" required value="<?php echo $cuentaBancoEditar['saldo_inicial']; ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="fecha_inicio_saldo">Fecha de Inicio del Saldo Inicial:</label>
                    <input type="date" name="fecha_inicio_saldo" id="fecha_inicio_saldo" class="form-control" required value="<?php echo $cuentaBancoEditar['fecha_inicio_saldo']; ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="cuenta_contable_defecto_id">Cuenta Contable por Defecto:</label>
                    <select name="cuenta_contable_defecto_id" id="cuenta_contable_defecto_id" class="form-control" required>
                        <option value="">Selecciona una cuenta contable por defecto</option>
                        <?php echo $aCuentaContable->get_select_options_accounting_plan($cuentaBancoEditar['cuenta_contable_defecto_id']); ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="banco_id">Banco:</label>
                    <select name="banco_id" id="banco_id" class="form-control" required>
                        <option>Selecciona una cuenta de banco</option>
                        <?php foreach ($tiposDeBanco as $tipoBanco) : ?>
                            <option value="<?php echo $tipoBanco['id']; ?>" <?php echo ($cuentaBancoEditar['banco_id'] == $tipoBanco['id']) ? 'selected' : ''; ?>><?php echo $tipoBanco['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
        </form>
    <?php else : ?>
        <p class="lead">Agrega una nueva cuenta de banco.</p>
        <form action="" method="POST">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="numero_cuenta">Número de Cuenta:</label>
                    <input type="text" name="numero_cuenta" id="numero_cuenta" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="tipo_cuenta_id">Tipo de Cuenta:</label>
                    <select name="tipo_cuenta_id" id="tipo_cuenta_id" class="form-control" required>
                        <option value="">Selecciona un tipo de cuenta</option>
                        <?php foreach ($tiposCuenta as $tipoCuenta) : ?>
                            <option value="<?php echo $tipoCuenta['id']; ?>"><?php echo $tipoCuenta['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nombre_cuenta">Nombre de la Cuenta:</label>
                    <input type="text" name="nombre_cuenta" id="nombre_cuenta" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="moneda_id">Moneda:</label>
                    <select name="moneda_id" id="moneda_id" class="form-control" required>
                        <option value="">Selecciona una moneda</option>
                        <?php foreach ($monedas as $moneda) : ?>
                            <option value="<?php echo $moneda['id']; ?>"><?php echo $moneda['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" id="descripcion" class="form-control"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="saldo_inicial">Saldo Inicial:</label>
                    <input type="text" name="saldo_inicial" id="saldo_inicial" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="fecha_inicio_saldo">Fecha de Inicio del Saldo Inicial:</label>
                    <input type="date" name="fecha_inicio_saldo" id="fecha_inicio_saldo" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="cuenta_contable_defecto_id">Cuenta Contable por Defecto:</label>
                    <select name="cuenta_contable_defecto_id" id="cuenta_contable_defecto_id" class="form-control" required>
                        <option value="">Selecciona una cuenta contable por defecto</option>
                        <?php echo $aCuentaContable->get_select_options_accounting_plan(); ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="banco_id">Banco:</label>
                    <select name="banco_id" id="banco_id" class="form-control" required>
                        <option>Selecciona una cuenta de banco</option>
                        <?php foreach ($tiposDeBanco as $tipoBanco) : ?>
                            <option value="<?php echo $tipoBanco['id']; ?>"><?php echo $tipoBanco['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" name="guardar" class="btn btn-primary">Agregar</button>
        </form>
    <?php endif; ?>
</div>

<table class="table table-striped table-bordered dt-responsive nowrap w-100" id="cuentasBancoTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Número de Cuenta</th>
            <th>Tipo de Cuenta</th>
            <th>Moneda</th>
            <th>Nombre de la Cuenta</th>
            <th>Descripción</th>
            <th>Saldo Inicial</th>
            <th>Fecha de Inicio del Saldo Inicial</th>
            <th>Cuenta Contable por Defecto</th>
            <th>Banco</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#cuentasBancoTable').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "ajax/get_data_table.php?method=cuentas_banco", // Cambiar a la ruta correcta
            "type": "POST",
            "data": function (d) {
                // d.length = d.length || 10;
                // d.draw++; // Incrementa el valor del draw en cada solicitud
                d.search = d.search.value || "";
                // Otros parámetros de búsqueda que quieras agregar
            },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id" },
            { "data": "numero_de_cuenta" },
            { "data": "tipo_cuenta" },
            { "data": "moneda" },
            { "data": "nombre_cuenta" },
            { "data": "descripcion" },
            { "data": "saldo_inicial" },
            { "data": "fecha_inicio_saldo" },
            { "data": "cuenta_contable" },
            { "data": "banco" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<div class="btn-group btn-group-toggle" data-toggle="buttons"><a href="?tipo=23&editar=' + row.id + '" class="btn btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>' +
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
