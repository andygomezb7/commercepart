<?php
// Mensaje de éxito o error
$mensaje = '';

// Agregar Modelo
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $marcaId = $_POST['marca_id'];

    $db->query("INSERT INTO modelos (nombre, marca_id) VALUES ('$nombre', '$marcaId')");
    $mensaje = 'El modelo se ha agregado correctamente.';
}

// Editar Modelo
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $marcaId = $_POST['marca_id'];

    $db->query("UPDATE modelos SET nombre='$nombre', marca_id='$marcaId' WHERE id='$id'");
    $mensaje = 'El modelo se ha actualizado correctamente.';
}

// Obtener el número total de modelos
$totalModelos = $db->query("SELECT COUNT(*) as total FROM modelos")->fetch_assoc()['total'];

// Configuración de la paginación
$modelosPorPagina = 10;
$totalPaginas = ceil($totalModelos / $modelosPorPagina);

// Obtener el número de página actual
$paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
$paginaActual = max(1, min($paginaActual, $totalPaginas));

// Calcular el desplazamiento para la consulta SQL
$desplazamiento = ($paginaActual - 1) * $modelosPorPagina;

// Obtener la lista actualizada de modelos con paginación
$modelos = $db->query("SELECT * FROM modelos LIMIT $desplazamiento, $modelosPorPagina")->fetch_all(MYSQLI_ASSOC);

// Obtener la lista de marcas
$marcas = $db->query("SELECT * FROM marcas")->fetch_all(MYSQLI_ASSOC);
?>

        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de agregar/editar modelo -->
        <h2>Agregar/Editar Modelo</h2>
        <?php if (isset($_GET['editar'])) : ?>
            <?php
            $idEditar = $_GET['editar'];
            $modeloEditar = $db->query("SELECT * FROM modelos WHERE id='$idEditar'")->fetch_assoc();
            ?>
            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
                <div class="form-group">
                    <label for="marca_id">Marca:</label>
                    <select name="marca_id" id="marca_id" class="form-control" required>
                        <option value="" disabled selected>Seleccione una marca</option>
                        <?php foreach ($marcas as $marca) : ?>
                            <option value="<?php echo $marca['id']; ?>" <?php if ($modeloEditar['marca_id'] == $marca['id']) echo 'selected'; ?>><?php echo $marca['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $modeloEditar['nombre']; ?>">
                </div>
                <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
            </form>
        <?php else : ?>
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
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>
                <button type="submit" name="guardar" class="btn btn-primary">Agregar</button>
            </form>
        <?php endif; ?>

        <!-- Tabla de modelos -->
        <hr>
        <h2>Listado de Modelos</h2>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($modelos as $modelo) : ?>
                    <?php
                    $marcaId = $modelo['marca_id'];
                    $marcaNombre = $db->query("SELECT nombre FROM marcas WHERE id='$marcaId'")->fetch_assoc()['nombre'];
                    ?>
                    <tr>
                        <td><?php echo $modelo['id']; ?></td>
                        <td><?php echo $modelo['nombre']; ?></td>
                        <td><?php echo $marcaNombre; ?></td>
                        <td>
                            <a href="?editar=<?php echo $modelo['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <button type="button" class="btn btn-danger btn-sm" onclick="mostrarConfirmacionEliminacion(<?php echo $modelo['id']; ?>)">Eliminar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <nav aria-label="Paginación">
            <ul class="pagination">
                <?php if ($totalPaginas > 1) : ?>
                    <?php if ($paginaActual > 1) : ?>
                        <li class="page-item">
                            <a class="page-link" href="?tipo=6&pagina=<?php echo $paginaActual - 1; ?>" aria-label="Anterior">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Anterior</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPaginas; $i++) : ?>
                        <li class="page-item <?php echo $i == $paginaActual ? 'active' : ''; ?>">
                            <a class="page-link" href="?tipo=6&pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($paginaActual < $totalPaginas) : ?>
                        <li class="page-item">
                            <a class="page-link" href="?tipo=6&pagina=<?php echo $paginaActual + 1; ?>" aria-label="Siguiente">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Siguiente</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>

        <script>
            function mostrarConfirmacionEliminacion(modeloId) {
                if (confirm("¿Estás seguro de que deseas eliminar este modelo?")) {
                    document.getElementById("form-eliminar-" + modeloId).submit();
                }
            }
        </script>

<script>
    function mostrarConfirmacionEliminacion(modeloId) {
        if (confirm("¿Estás seguro de que deseas eliminar este modelo?")) {
            // Realizar la eliminación mediante una solicitud AJAX
            eliminarModelo(modeloId);
        }
    }

    function eliminarModelo(modeloId) {
        // Realizar la solicitud AJAX para eliminar el modelo
        $.ajax({
            url: 'ajax/eliminar_modelo.php', // Ruta al archivo PHP que eliminará el modelo
            type: 'POST',
            data: { id: modeloId },
            success: function(response) {
                // Manejar la respuesta del servidor si es necesario
                // Por ejemplo, recargar la página después de eliminar el modelo
                location.reload();
            },
            error: function(xhr, status, error) {
                // Manejar los errores de la solicitud AJAX si es necesario
                console.log(error);
            }
        });
    }
</script>