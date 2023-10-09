<?php
// Mensaje de éxito o error
$mensaje = '';

// Obtener la lista de repuestos
$repuestos = $db->query("SELECT * FROM repuestos WHERE empresa_id = " . $_SESSION['empresa_id']);

// Obtener la lista de modelos
$modelos = $db->query("SELECT * FROM modelos WHERE empresa_id = " . $_SESSION['empresa_id']);

// Obtener la lista de marcas
$marcas = $db->query("SELECT * FROM marcas WHERE empresa_id = " . $_SESSION['empresa_id']);
$yearInicio = '';
$yearFin = '';
// Asignar Modelo y Marca a un Repuesto
if (isset($_POST['asignar'])) {
    $repuestoId = $_POST['repuesto_id'];
    $modeloId = $_POST['modelo_id'];
    $marcaId = $_POST['marca_id'];
    $yearInicio = $_POST['year_inicio'];
    $yearFin = $_POST['year_fin'];

    // Verificar si ya existe la asignación para el repuesto en repuesto_modelos
    $asignacionExistenteModelo = $db->query("SELECT * FROM repuesto_modelos WHERE id_repuesto='$repuestoId' AND id_modelo='$modeloId' AND marca_id='$marcaId' AND fecha_inicio='$yearInicio' AND fecha_fin='$yearFin' AND empresa_id = " . $_SESSION['empresa_id'])->fetch_assoc();

    if ($asignacionExistenteModelo) {
        // Actualizar la asignación existente de modelos en repuesto_modelos
        $db->query("UPDATE repuesto_modelos SET id_modelo='$modeloId', marca_id='$marcaId', fecha_inicio='$yearInicio', fecha_fin='$yearFin' WHERE id_repuesto='$repuestoId'");
    } else {
        // Insertar nueva asignación de modelos en repuesto_modelos
        $db->query("INSERT INTO repuesto_modelos (id_repuesto, id_modelo, marca_id, fecha_inicio, fecha_fin, empresa_id) VALUES ('$repuestoId', '$modeloId', '$marcaId', '$yearInicio', '$yearFin', '".$_SESSION['empresa_id']."')");
    }

    // Verificar si ya existe la asignación para el repuesto en repuesto_marcas
    $asignacionExistenteMarca = $db->query("SELECT * FROM repuesto_marcas WHERE id_repuesto='$repuestoId' AND id_marca='$marcaId' AND fecha_inicio='$yearInicio' AND fecha_fin='$yearFin' AND empresa_id = " . $_SESSION['empresa_id'])->fetch_assoc();

    if ($asignacionExistenteMarca) {
        // Actualizar la asignación existente de marcas en repuesto_marcas
        $db->query("UPDATE repuesto_marcas SET id_marca='$marcaId', fecha_inicio='$yearInicio', fecha_fin='$yearFin' WHERE id_repuesto='$repuestoId'");
    } else {
        // Insertar nueva asignación de marcas en repuesto_marcas
        $db->query("INSERT INTO repuesto_marcas (id_repuesto, id_marca, fecha_inicio, fecha_fin, empresa_id) VALUES ('$repuestoId', '$marcaId', '$yearInicio', '$yearFin', '".$_SESSION['empresa_id']."')");
    }

    $mensaje = 'La asignación se ha actualizado correctamente.';
}

// Obtener las asignaciones de repuestos con nombres de marcas, modelos y años
$asignaciones = $db->query("SELECT r.nombre AS repuesto, m.nombre AS modelo, mc.nombre AS marca, rm.fecha_inicio, rm.fecha_fin
                            FROM repuestos r
                            INNER JOIN repuesto_modelos rm ON r.id = rm.id_repuesto
                            INNER JOIN modelos m ON rm.id_modelo = m.id
                            INNER JOIN marcas mc ON rm.marca_id = mc.id WHERE rm.empresa_id = " . $_SESSION['empresa_id']);
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
                        <option value="<?php echo $marca['id']; ?>" <?php echo (@$marcaId&&@$marcaId==$marca['id'] ? 'selected' : ''); ?> ><?php echo $marca['nombre']; ?></option>
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
            <div class="form-group">
                <label for="repuesto_id">Repuesto:</label>
                <select name="repuesto_id" id="repuesto_id" class="form-control" required disabled>
                    <option value="" disabled selected>Seleccione un repuesto</option>
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
            var extServices = {
                modelo: function (selected = false) {
                    var marcaId = $("#marca_id").val();
                    if (marcaId !== "") {
                        $.ajax({
                            url: "ajax/get_modelos.php",
                            method: "POST",
                            data: {
                                marca_id: marcaId,
                                selected: selected
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
                                if (selected) {
                                    <?php echo ($yearInicio && $yearFin) ? "extServices.yearInicio($yearInicio,$yearFin)" : ""; ?>;
                                }
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
                },
                yearInicio: function (selected = false, selectedtwo = false) {
                    var modeloId = $("#modelo_id").val();
                    if (modeloId !== "") {
                        $.ajax({
                            url: "ajax/get_years.php",
                            method: "POST",
                            data: {
                                repuesto_id: $("#repuesto_id").val(),
                            },
                            success: function(data) {
                                var years = JSON.parse(data);
                                var yearOptions = "";
                                for (var i = 0; i < years.length; i++) {
                                    yearOptions += "<option value='" + years[i] + "' " + (selected&&selected==years[i] ? 'selected' : '') + ">" + years[i] + "</option>";
                                }
                                $("#year_inicio").html(yearOptions);
                                $("#year_inicio").prop("disabled", false);
                                // $("#year_fin").html('<option value="" disabled selected>Seleccione el año de fin</option>');
                                var yearOptions = "";
                                for (var i = 0; i < years.length; i++) {
                                    yearOptions += "<option value='" + years[i] + "' " + (selectedtwo&&selectedtwo==years[i] ? 'selected' : '') + ">" + years[i] + "</option>";
                                }
                                $("#year_fin").html(yearOptions);
                                $("#year_fin").prop("disabled", false);
                                $("button[name='asignar']").prop("disabled", true);
                                if (selected && selectedtwo) {
                                    extServices.yearFin(selected, selectedtwo);
                                }
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
                },
                yearFin: function(selected = false, selectedtwo = false) {
                    var yearInicio = (selected ? selected : $("#year_inicio").val());
                    var yearFin = (selectedtwo ? selectedtwo : $("#year_fin").val());
                    if (yearInicio !== "" && yearFin !== "") {
                        $.ajax({
                            url: "ajax/get_repuestos.php",
                            method: "POST",
                            data: {
                                modelo_id: $("#modelo_id").val()
                            },
                            success: function(data) {
                                $("#repuesto_id").html(data);
                                $("#repuesto_id").prop("disabled", false);
                            }
                        });
                    } else {
                        $("#repuesto_id").html('<option value="" disabled selected>Seleccione un repuesto</option>');
                        $("#repuesto_id").prop("disabled", true);
                    }
                }
            }

            <?php
             if (@$modeloId) {
                echo "extServices.modelo($modeloId);";
             }
            ?>

            // Obtener los modelos según la marca seleccionada
            $("#marca_id").change(function() {
                extServices.modelo();
            });

            // Obtener los repuestos según el modelo seleccionado
            $("#modelo_id").change(function() {
                extServices.yearInicio();
            });

            // Habilitar el botón de asignar cuando se selecciona el año de inicio y fin
            $("#year_inicio, #year_fin").change(function() {
                extServices.yearFin();
            });

            // Obtener los años de inicio y fin según el repuesto seleccionado
            $("#repuesto_id").change(function() {
                var repuestoId = $(this).val();
                if (repuestoId !== "") {
                    $("button[name='asignar']").prop("disabled", false);
                } else {
                    $("button[name='asignar']").prop("disabled", true);
                    $("#year_inicio").html('<option value="" disabled selected>Seleccione el año de inicio</option>');
                    $("#year_inicio").prop("disabled", true);
                    $("#year_fin").html('<option value="" disabled selected>Seleccione el año de fin</option>');
                    $("#year_fin").prop("disabled", true);
                    $("button[name='asignar']").prop("disabled", true);
                }
            });

        });
    </script>
