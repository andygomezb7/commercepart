<?php
// Mensaje de éxito o error
$mensaje = '';

// Obtener la lista de repuestos
$repuestos = $db->query("SELECT * FROM repuestos")->fetch_all(MYSQLI_ASSOC);

// Obtener la lista de modelos
$modelos = $db->query("SELECT * FROM modelos")->fetch_all(MYSQLI_ASSOC);

// Obtener la lista de marcas
$marcas = $db->query("SELECT * FROM marcas")->fetch_all(MYSQLI_ASSOC);

// Asignar Modelo y Marca a un Repuesto
if (isset($_POST['asignar'])) {
    $repuestoId = $_POST['repuesto_id'];
    $modeloId = $_POST['modelo_id'];
    $marcaId = $_POST['marca_id'];
    $yearInicio = $_POST['year_inicio'];
    $yearFin = $_POST['year_fin'];

    // Verificar si ya existe la asignación para el repuesto en repuesto_modelos
    $asignacionExistenteModelo = $db->query("SELECT * FROM repuesto_modelos WHERE id_repuesto='$repuestoId' AND id_modelo='$modeloId' AND marca_id='$marcaId' AND fecha_inicio='$yearInicio' AND fecha_fin='$yearFin'")->fetch_assoc();

    if ($asignacionExistenteModelo) {
        // Actualizar la asignación existente de modelos en repuesto_modelos
        $db->query("UPDATE repuesto_modelos SET id_modelo='$modeloId', marca_id='$marcaId', fecha_inicio='$yearInicio', fecha_fin='$yearFin' WHERE id_repuesto='$repuestoId'");
    } else {
        // Insertar nueva asignación de modelos en repuesto_modelos
        $db->query("INSERT INTO repuesto_modelos (id_repuesto, id_modelo, marca_id, fecha_inicio, fecha_fin) VALUES ('$repuestoId', '$modeloId', '$marcaId', '$yearInicio', '$yearFin')");
    }

    // Verificar si ya existe la asignación para el repuesto en repuesto_marcas
    $asignacionExistenteMarca = $db->query("SELECT * FROM repuesto_marcas WHERE id_repuesto='$repuestoId' AND id_marca='$marcaId' AND fecha_inicio='$yearInicio' AND fecha_fin='$yearFin'")->fetch_assoc();

    if ($asignacionExistenteMarca) {
        // Actualizar la asignación existente de marcas en repuesto_marcas
        $db->query("UPDATE repuesto_marcas SET id_marca='$marcaId', fecha_inicio='$yearInicio', fecha_fin='$yearFin' WHERE id_repuesto='$repuestoId'");
    } else {
        // Insertar nueva asignación de marcas en repuesto_marcas
        $db->query("INSERT INTO repuesto_marcas (id_repuesto, id_marca, fecha_inicio, fecha_fin) VALUES ('$repuestoId', '$marcaId', '$yearInicio', '$yearFin')");
    }

    $mensaje = 'La asignación se ha actualizado correctamente.';
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Asignación de Modelos y Marcas</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>Dashboard - Asignación de Modelos y Marcas a Repuestos</h1>

        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <h2>Asignar Modelo y Marca a un Repuesto</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="repuesto_id">Repuesto:</label>
                <select name="repuesto_id" id="repuesto_id" class="form-control" required>
                    <option value="" disabled selected>Seleccione un repuesto</option>
                    <?php foreach ($repuestos as $repuesto) : ?>
                        <option value="<?php echo $repuesto['id']; ?>"><?php echo $repuesto['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="modelo_id">Modelo:</label>
                <select name="modelo_id" id="modelo_id" class="form-control" required>
                    <option value="" disabled selected>Seleccione un modelo</option>
                    <?php foreach ($modelos as $modelo) : ?>
                        <option value="<?php echo $modelo['id']; ?>"><?php echo $modelo['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="marca_id">Marca:</label>
                <select name="marca_id" id="marca_id" class="form-control" required>
                    <option value="" disabled selected>Seleccione una marca</option>
                    <?php foreach ($marcas as $marca) : ?>
                        <option value="<?php echo $marca['id']; ?>"><?php echo $marca['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="year_inicio">Año de Inicio:</label>
                <select name="year_inicio" id="year_inicio" class="form-control" required>
                    <option value="" disabled selected>Seleccione el año de inicio</option>
                    <?php
                    $currentYear = date('Y');
                    for ($i = $currentYear - 40; $i <= $currentYear; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="year_fin">Año de Fin:</label>
                <select name="year_fin" id="year_fin" class="form-control" required>
                    <option value="" disabled selected>Seleccione el año de fin</option>
                    <?php
                    $currentYear = date('Y');
                    for ($i = $currentYear - 40; $i <= $currentYear; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="asignar" class="btn btn-primary">Asignar</button>
        </form>
    </div>
</body>

</html>