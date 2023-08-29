<?php

// Obtener la lista de asignaciones de repuestos y motores
$asignaciones = $db->query("SELECT rm.id, m.nombre AS marca, mo.nombre AS modelo, CONCAT(rm.fecha_inicio, ' - ', rm.fecha_fin) AS anio FROM repuesto_modelos rm JOIN marcas m ON rm.marca_id = m.id JOIN modelos mo ON rm.id_modelo = mo.id");
$motores = $db->query("SELECT * FROM motores");

// Asignar motor a una asignación de repuesto y modelo
if (isset($_POST['asignar'])) {
    $asignacionId = $_POST['id_asignacion'];
    $motorId = $_POST['id_motor'];

    // Verificar si ya existe una asignación de motor para la asignación de repuesto y modelo específica
    $asignacionExistente = $db->query("SELECT * FROM motor_asignacion WHERE id_modelo_asignacion='$asignacionId' AND id_motor = '$motorId'")->fetch_assoc();

    if (!$asignacionExistente->fetch_assoc()) {
        $db->query("INSERT INTO motor_asignacion (id_modelo_asignacion, id_motor) VALUES ('$asignacionId', '$motorId')");
        $mensaje = 'La asignación de motor se ha realizado correctamente.';
    } else {
        $mensaje = 'Ya existe una asignación de motor para la asignación de repuesto y modelo seleccionada.';
    }
}

// Borrar asignación de motor
if (isset($_POST['borrar'])) {
    $asignacionId = $_POST['id_asignacion'];

    // Eliminar la asignación de motor para la asignación de repuesto y modelo específica
    $db->query("DELETE FROM motor_asignacion WHERE id_asignacion='$asignacionId'");

    $mensaje = 'La asignación de motor ha sido eliminada correctamente.';
}

?>
        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <h2>Asignar Motor a una Asignación de Repuesto y Modelo</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="id_asignacion">Asignación de Repuesto y Modelo:</label>
                <select name="id_asignacion" id="id_asignacion" class="form-control" required>
                    <option value="" disabled selected>Seleccione una asignación</option>
                    <?php foreach ($asignaciones as $asignacion) : ?>
                        <option value="<?php echo $asignacion['id']; ?>"><?php echo $asignacion['marca'] . ', ' . $asignacion['modelo'] . ': ' . $asignacion['anio']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_motor">Motor:</label>
                <select name="id_motor" id="id_motor" class="form-control" required>
                    <option value="" disabled selected>Seleccione un motor</option>
                    <?php foreach ($motores as $motor) : ?>
                        <option value="<?php echo $motor['id']; ?>"><?php echo $motor['nombre_de_motor']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="asignar" class="btn btn-primary">Asignar Motor</button>
        </form>

        <hr />

        <h2>Asignaciones de Motores</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Asignación</th>
                    <th>Motor</th>
                    <th width="40%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($asignaciones as $asignacion) : ?>
                    <tr>
                        <td><?php echo $asignacion['marca'] . ', ' . $asignacion['modelo'] . ': ' . $asignacion['anio']; ?></td>
                        <td>
                            <?php
                            $asignacionId = $asignacion['id'];
                            $motorAsignado = $db->query("SELECT m.nombre_de_motor as nombre FROM motor_asignacion ma JOIN motores m ON ma.id_motor = m.id WHERE ma.id_modelo_asignacion='$asignacionId'");

                            if ($motorAsignado) {
                                $motorAsignadoL = [];
                                foreach ($motorAsignado AS $rowMotor) {
                                    $motorAsignadoL[] = $rowMotor['nombre'];
                                }
                                echo implode($motorAsignadoL, ', ');
                            } else {
                                echo 'No asignado';
                            }
                            ?>
                        </td>
                        <td>
                            <form action="" method="POST" onsubmit="return confirm('¿Estás seguro de borrar esta asignación de motor?');">
                                <input type="hidden" name="id_asignacion" value="<?php echo $asignacionId; ?>">
                                <button type="submit" name="borrar" class="btn btn-danger">Borrar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>