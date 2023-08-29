<?php
// Mensaje de éxito o error
$mensaje = '';

// Obtener la lista de repuestos
$repuestos = $db->query("SELECT * FROM repuestos");

// Obtener la lista de modelos
$modelos = $db->query("SELECT * FROM modelos");

// Obtener la lista de marcas
$marcas = $db->query("SELECT * FROM marcas");

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

// Obtener las asignaciones de repuestos con nombres de marcas, modelos y años
$asignaciones = $db->query("SELECT r.nombre AS repuesto, m.nombre AS modelo, mc.nombre AS marca, rm.fecha_inicio, rm.fecha_fin
                            FROM repuestos r
                            INNER JOIN repuesto_modelos rm ON r.id = rm.id_repuesto
                            INNER JOIN modelos m ON rm.id_modelo = m.id
                            INNER JOIN marcas mc ON rm.marca_id = mc.id");
?>
        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <h2>Asignar Modelo y Marca a un Repuesto</h2>
        <form action="" method="POST">
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
                <label for="modelo_id">Modelo:</label>
                <select name="modelo_id" id="modelo_id" class="form-control" required disabled>
                    <option value="" disabled selected>Seleccione un modelo</option>
                </select>
            </div>
            <div class="form-group">
                <label for="repuesto_id">Repuesto:</label>
                <select name="repuesto_id" id="repuesto_id" class="form-control" required disabled>
                    <option value="" disabled selected>Seleccione un repuesto</option>
                </select>
            </div>
            <div class="form-group">
                <label for="year_inicio">Año de Inicio:</label>
                <select name="year_inicio" id="year_inicio" class="form-control" required disabled>
                    <option value="" disabled selected>Seleccione el año de inicio</option>
                </select>
            </div>
            <div class="form-group">
                <label for="year_fin">Año de Fin:</label>
                <select name="year_fin" id="year_fin" class="form-control" required disabled>
                    <option value="" disabled selected>Seleccione el año de fin</option>
                </select>
            </div>
            <button type="submit" name="asignar" class="btn btn-primary" disabled>Asignar</button>
        </form>

        <h2>Asignaciones de Repuestos</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Repuesto</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Año de Inicio</th>
                    <th>Año de Fin</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($asignaciones as $asignacion) : ?>
                    <tr>
                        <td><?php echo $asignacion['repuesto']; ?></td>
                        <td><?php echo $asignacion['marca']; ?></td>
                        <td><?php echo $asignacion['modelo']; ?></td>
                        <td><?php echo $asignacion['fecha_inicio']; ?></td>
                        <td><?php echo $asignacion['fecha_fin']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script>
        $(document).ready(function() {
            // Obtener los modelos según la marca seleccionada
            $("#marca_id").change(function() {
                var marcaId = $(this).val();
                if (marcaId !== "") {
                    $.ajax({
                        url: "ajax/get_modelos.php",
                        method: "POST",
                        data: {
                            marca_id: marcaId
                        },
                        success: function(data) {
                            $("#modelo_id").html(data);
                            $("#modelo_id").prop("disabled", false);
                            $("#repuesto_id").html('<option value="" disabled selected>Seleccione un repuesto</option>');
                            $("#repuesto_id").prop("disabled", true);
                            $("#year_inicio").html('<option value="" disabled selected>Seleccione el año de inicio</option>');
                            $("#year_inicio").prop("disabled", true);
                            $("#year_fin").html('<option value="" disabled selected>Seleccione el año de fin</option>');
                            $("#year_fin").prop("disabled", true);
                            $("button[name='asignar']").prop("disabled", true);
                        }
                    });
                } else {
                    $("#modelo_id").html('<option value="" disabled selected>Seleccione un modelo</option>');
                    $("#modelo_id").prop("disabled", true);
                    $("#repuesto_id").html('<option value="" disabled selected>Seleccione un repuesto</option>');
                    $("#repuesto_id").prop("disabled", true);
                    $("#year_inicio").html('<option value="" disabled selected>Seleccione el año de inicio</option>');
                    $("#year_inicio").prop("disabled", true);
                    $("#year_fin").html('<option value="" disabled selected>Seleccione el año de fin</option>');
                    $("#year_fin").prop("disabled", true);
                    $("button[name='asignar']").prop("disabled", true);
                }
            });

            // Obtener los repuestos según el modelo seleccionado
            $("#modelo_id").change(function() {
                var modeloId = $(this).val();
                if (modeloId !== "") {
                    $.ajax({
                        url: "ajax/get_repuestos.php",
                        method: "POST",
                        data: {
                            modelo_id: modeloId
                        },
                        success: function(data) {
                            $("#repuesto_id").html(data);
                            $("#repuesto_id").prop("disabled", false);
                            $("#year_inicio").html('<option value="" disabled selected>Seleccione el año de inicio</option>');
                            $("#year_inicio").prop("disabled", true);
                            $("#year_fin").html('<option value="" disabled selected>Seleccione el año de fin</option>');
                            $("#year_fin").prop("disabled", true);
                            $("button[name='asignar']").prop("disabled", true);
                        }
                    });
                } else {
                    $("#repuesto_id").html('<option value="" disabled selected>Seleccione un repuesto</option>');
                    $("#repuesto_id").prop("disabled", true);
                    $("#year_inicio").html('<option value="" disabled selected>Seleccione el año de inicio</option>');
                    $("#year_inicio").prop("disabled", true);
                    $("#year_fin").html('<option value="" disabled selected>Seleccione el año de fin</option>');
                    $("#year_fin").prop("disabled", true);
                    $("button[name='asignar']").prop("disabled", true);
                }
            });

            // Obtener los años de inicio y fin según el repuesto seleccionado
            $("#repuesto_id").change(function() {
                var repuestoId = $(this).val();
                if (repuestoId !== "") {
                    $.ajax({
                        url: "ajax/get_years.php",
                        method: "POST",
                        data: {
                            repuesto_id: repuestoId
                        },
                        success: function(data) {
                            var years = JSON.parse(data);
                            var yearOptions = "";
                            for (var i = 0; i < years.length; i++) {
                                yearOptions += "<option value='" + years[i] + "'>" + years[i] + "</option>";
                            }
                            $("#year_inicio").html(yearOptions);
                            $("#year_inicio").prop("disabled", false);
                            // $("#year_fin").html('<option value="" disabled selected>Seleccione el año de fin</option>');
                            $("#year_fin").html(yearOptions);
                            $("#year_fin").prop("disabled", false);
                            $("button[name='asignar']").prop("disabled", true);
                        }
                    });
                } else {
                    $("#year_inicio").html('<option value="" disabled selected>Seleccione el año de inicio</option>');
                    $("#year_inicio").prop("disabled", true);
                    $("#year_fin").html('<option value="" disabled selected>Seleccione el año de fin</option>');
                    $("#year_fin").prop("disabled", true);
                    $("button[name='asignar']").prop("disabled", true);
                }
            });

            // Habilitar el botón de asignar cuando se selecciona el año de inicio y fin
            $("#year_inicio, #year_fin").change(function() {
                var yearInicio = $("#year_inicio").val();
                var yearFin = $("#year_fin").val();
                if (yearInicio !== "" && yearFin !== "") {
                    $("button[name='asignar']").prop("disabled", false);
                } else {
                    $("button[name='asignar']").prop("disabled", true);
                }
            });
        });
    </script>
