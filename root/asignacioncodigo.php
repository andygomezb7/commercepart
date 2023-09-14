<?php
// Mensaje de éxito o error
$mensaje = '';

// Obtener la lista de repuestos
$repuestos = $db->query("SELECT * FROM repuestos WHERE empresa_id = " . $_SESSION['empresa_id']);

// Obtener la lista de códigos de repuesto
// $codigosRepuesto = $db->query("SELECT * FROM codigos_repuesto");

// Asignar Códigos a un Repuesto
if (isset($_POST['asignar'])) {
    $repuestoId = $_POST['id_repuesto'];
    $codigos = $_POST['codigos'];

    // Insertar las nuevas asignaciones de códigos en la tabla de códigos
    foreach ($codigos as $codigo) {
        // Verificar si ya existe la asignación para el repuesto y código específico
        $asignacionExistente = $db->query("SELECT * FROM codigos_repuesto WHERE id_repuesto='$repuestoId' AND codigo='$codigo'")->fetch_assoc();

        if (!$asignacionExistente) {
            $db->query("INSERT INTO codigos_repuesto (id_repuesto, codigo) VALUES ('$repuestoId', '$codigo')");
        }
    }

    $mensaje = 'La asignación de códigos se ha realizado correctamente.';
}

// Desasignar Código de un Repuesto
if (isset($_POST['desasignar'])) {
    $repuestoId = $_POST['id_repuesto'];
    $codigo = $_POST['codigo'];

    // Eliminar la asignación de código para el repuesto específico
    $db->query("DELETE FROM codigos_repuesto WHERE id_repuesto='$repuestoId' AND codigo='$codigo'");

    $mensaje = 'La desasignación de código se ha realizado correctamente.';
}

?>
        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <h2>Asignar Códigos a un Repuesto</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="id_repuesto">Repuesto:</label>
                <select name="id_repuesto" id="id_repuesto" class="form-control" required>
                    <option value="" disabled selected>Seleccione un repuesto</option>
                    <?php foreach ($repuestos as $repuesto) : ?>
                        <option value="<?php echo $repuesto['id']; ?>"><?php echo $repuesto['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="codigos">Códigos:</label>
                <input type="text" name="codigos[]" id="codigos" class="form-control" required multiple>
                <small class="form-text text-muted">Ingrese los códigos separados por comas.</small>
            </div>
            <button type="submit" name="asignar" class="btn btn-primary">Asignar</button>
        </form>

        <h2>Asignaciones de Códigos</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Repuesto</th>
                    <th>Códigos</th>
                    <th width="40%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($repuestos as $repuesto) : ?>
                    <tr>
                        <td><?php echo $repuesto['nombre']; ?></td>
                        <td>
                            <?php
                            $repuestoId = $repuesto['id'];
                            $result = $db->query("SELECT codigo FROM codigos_repuesto WHERE id_repuesto='$repuestoId'");
                            $codigosAsignados = [];
                            foreach ($result as $row) {
                                $codigosAsignados[] = $row['codigo'];
                            }

                            // $codigos = array_column($codigosAsignados, 'codigo');
                            echo implode(', ', $codigosAsignados);
                            ?>
                        </td>
                        <td>
                            <form action="" method="POST">
                                <input type="hidden" name="id_repuesto" value="<?php echo $repuestoId; ?>">
                                <div class="input-group">
                                    <select name="codigo" class="form-control mr-2 col-8" required>
                                        <option value="" disabled selected>Seleccione un código</option>
                                        <?php foreach ($codigos as $codigo) : ?>
                                            <option value="<?php echo $codigo; ?>"><?php echo $codigo; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="desasignar" class="btn btn-danger">Desasignar</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>