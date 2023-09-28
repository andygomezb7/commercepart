<?php
include('../secure/class/cuenta_contable.php');  // Asegúrate de incluir el archivo correcto
$aCuentaContable = new CuentaContable($db);

$mensaje = '';

// Agregar Cuenta Contable
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $cuenta_contable_padre_id = ($_POST['cuenta_contable_padre_id'] ? $_POST['cuenta_contable_padre_id'] : 'NULL');

    // Realiza la inserción en la base de datos
    $query = "INSERT INTO cuenta_contable (NombreCuenta, TipoCuenta, CuentaContablePadreID,empresa_id) 
              VALUES ('$nombre', '$tipo', $cuenta_contable_padre_id,'".$_SESSION['empresa_id']."')";
    
    if ($db->query($query)) {
        $mensaje = 'La cuenta contable se ha agregado correctamente.';
    } else {
        $mensaje = 'Error al agregar la cuenta contable ('.$query.'): ' . $db->error;
    }
}

// Editar Cuenta Contable
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $cuenta_contable_padre_id = $_POST['cuenta_contable_padre_id'];

    // Realiza la actualización en la base de datos
    $query = "UPDATE cuenta_contable SET NombreCuenta = '$nombre', TipoCuenta = '$tipo', CuentaContablePadreID = '$cuenta_contable_padre_id' WHERE ID = '$id'";
    
    if ($db->query($query)) {
        $mensaje = 'La cuenta contable se ha actualizado correctamente.';
    } else {
        $mensaje = 'Error al actualizar la cuenta contable: ' . $db->error;
    }
}

// Eliminar Cuenta Contable
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    // Realiza la eliminación en la base de datos
    $query = "DELETE FROM cuenta_contable WHERE ID = '$id'";
    
    if ($db->query($query)) {
        $mensaje = 'La cuenta contable se ha eliminado correctamente.';
    } else {
        $mensaje = 'Error al eliminar la cuenta contable: ' . $db->error;
    }
}

// Obtener datos de la cuenta contable a editar
function obtenerCuentaContablePorID($id) {
    global $db;
    
    $query = "SELECT * FROM cuenta_contable WHERE ID = '$id' AND empresa_id = " . $_SESSION['empresa_id'];
    $result = $db->query($query);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Obtener la lista de cuentas contables para el select
$queryCuentasContables = "SELECT ID, NombreCuenta, CuentaContablePadreID FROM cuenta_contable WHERE empresa_id = " . $_SESSION['empresa_id'];
$resultCuentasContables = $db->query($queryCuentasContables);
$cuentasContables = $resultCuentasContables;

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
        $cuentaContableEditar = obtenerCuentaContablePorID($idEditar);
        ?>
        <p class="lead">Edita la cuenta contable de forma rápida.</p>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
            <div class="form-group">
                <label for="nombre">Nombre de la Cuenta:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $cuentaContableEditar['NombreCuenta']; ?>">
            </div>
            <div class="form-group">
                <label for="tipo">Tipo de Cuenta:</label>
                <select name="tipo" id="tipo" class="form-control" required>
                    <option value="Ingresos" <?php echo ($cuentaContableEditar['TipoCuenta'] == 'Ingresos') ? 'selected' : ''; ?>>Ingresos</option>
                    <option value="Egresos" <?php echo ($cuentaContableEditar['TipoCuenta'] == 'Egresos') ? 'selected' : ''; ?>>Egresos</option>
                    <option value="Activo" <?php echo ($cuentaContableEditar['TipoCuenta'] == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                    <option value="Pasivo" <?php echo ($cuentaContableEditar['TipoCuenta'] == 'Pasivo') ? 'selected' : ''; ?>>Pasivo</option>
                    <option value="Capital" <?php echo ($cuentaContableEditar['TipoCuenta'] == 'Capital') ? 'selected' : ''; ?>>Capital</option>
                    <option value="Otro" <?php echo ($cuentaContableEditar['TipoCuenta'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cuenta_contable_padre_id">Cuenta Contable Padre:</label>
                <select name="cuenta_contable_padre_id" id="cuenta_contable_padre_id" class="form-control">
                    <option value="">Selecciona una cuenta contable padre</option>
                    <?php 
                    foreach ($cuentasContables as $cuenta) : ?>
                        <option value="<?php echo $cuenta['ID']; ?>" <?php echo ($cuentaContableEditar['CuentaContablePadreID'] == $cuenta['ID']) ? 'selected' : ''; ?>><?php echo $cuenta['NombreCuenta']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
        </form>
    <?php else : ?>
        <p class="lead">Agrega una nueva cuenta contable.</p>
        <form action="" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre de la Cuenta:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="tipo">Tipo de Cuenta:</label>
                <select name="tipo" id="tipo" class="form-control" required>
                    <option value="Ingresos">Ingresos</option>
                    <option value="Egresos">Egresos</option>
                    <option value="Activo">Activo</option>
                    <option value="Pasivo">Pasivo</option>
                    <option value="Capital">Capital</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cuenta_contable_padre_id">Cuenta Contable Padre:</label>
                <select name="cuenta_contable_padre_id" id="cuenta_contable_padre_id" class="form-control">
                    <option value="">Selecciona una cuenta contable padre</option>
                    <?php echo $aCuentaContable->get_select_options_accounting_plan(); ?>
                </select>
            </div>
            <button type="submit" name="guardar" class="btn btn-primary">Agregar</button>
        </form>
    <?php endif; ?>
</div>

<!-- Agregar opción para asignar cuentas contables principales aquí utilizando JavaScript -->

<table class="table table-striped table-bordered dt-responsive nowrap w-100" id="cuentasContablesTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre de la Cuenta</th>
            <!-- <th>Tipo de Cuenta</th> -->
            <th>Cuenta Contable Principal</th>
            <th>Cuenta Contable Padre</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#cuentasContablesTable').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "ajax/get_data_table.php?method=cuentas_contables", // Cambiar a la ruta correcta
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
            { "data": "ID" },
            { "data": "NombreCuenta" },
            // { "data": "TipoCuenta" },
            { "data": "CuentaContablePrincipal" },
            { "data": "CuentaContablePadreID" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<div class="btn-group btn-group-toggle" data-toggle="buttons"><a href="?tipo=18&editar=' + row.ID + '" class="btn btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>' +
                           '<form action="" method="POST" style="display: inline-block;">' +
                           '<input type="hidden" name="id" value="' + row.ID + '">' +
                           '<button type="submit" name="eliminar" class="btn btn-danger rounded-0"><i class="fas fa-times"></i> Eliminar</button>' +
                           '</form></div>';
                }
            }
        ],
    });
});
</script>
