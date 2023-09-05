<?php
// Mensaje de éxito o error
$mensaje = '';

// Agregar Motor
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];

    $db->query("INSERT INTO motores (nombre_de_motor, empresa_id) VALUES ('$nombre', '".$_SESSION['empresa_id']."')");
    $mensaje = 'El motor se ha agregado correctamente.';
} else if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];

    $db->query("UPDATE motores SET nombre_de_motor='$nombre' WHERE id='$id' AND empresa_id = " . $_SESSION['empresa_id']);
    $mensaje = 'El motor se ha actualizado correctamente.';
} else if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    $db->query("DELETE FROM motores WHERE id='$id' AND empresa_id = " . $_SESSION['empresa_id']);
    $mensaje = 'El motor se ha eliminado correctamente.';
}

// Obtener el número total de motores
$totalMotores = $db->query("SELECT COUNT(*) as total FROM motores WHERE empresa_id = " . $_SESSION['empresa_id'])->fetch_assoc()['total'];

// Configuración de la paginación
$motoresPorPagina = 10;
$totalPaginas = ceil($totalMotores / $motoresPorPagina);

// Obtener el número de página actual
$paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
$paginaActual = max(1, min($paginaActual, $totalPaginas));

// Calcular el desplazamiento para la consulta SQL
$desplazamiento = ($paginaActual - 1) * $motoresPorPagina;

// Obtener la lista actualizada de motores con paginación
$motores = $db->query("SELECT * FROM motores WHERE empresa_id = '".$_SESSION['empresa_id']."' LIMIT $desplazamiento, $motoresPorPagina");
?>
        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de agregar/editar motor -->
        <h2>Agregar/Editar Motor</h2>
        <?php if (isset($_GET['editar'])) : ?>
            <?php
            $idEditar = $_GET['editar'];
            $motorEditar = $db->query("SELECT * FROM motores WHERE id='$idEditar' AND empresa_id = " . $_SESSION['empresa_id'])->fetch_assoc();
            ?>
            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $motorEditar['nombre_de_motor']; ?>">
                </div>
                <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
            </form>
        <?php else : ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>
                <button type="submit" name="guardar" class="btn btn-primary">Agregar</button>
            </form>
        <?php endif; ?>

        <!-- Tabla de motores -->
        <hr>
        <h2>Listado de Motores</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($motores as $motor) : ?>
                    <tr>
                        <td><?php echo $motor['id']; ?></td>
                        <td><?php echo $motor['nombre_de_motor']; ?></td>
                        <td>
                            <a href="?tipo=11&editar=<?php echo $motor['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <button type="button" class="btn btn-danger btn-sm" onclick="mostrarConfirmacionEliminacion(<?php echo $motor['id']; ?>)">Eliminar</button>
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
                            <a class="page-link" href="?pagina=<?php echo $paginaActual - 1; ?>" aria-label="Anterior">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Anterior</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPaginas; $i++) : ?>
                        <li class="page-item <?php echo $i == $paginaActual ? 'active' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($paginaActual < $totalPaginas) : ?>
                        <li class="page-item">
                            <a class="page-link" href="?pagina=<?php echo $paginaActual + 1; ?>" aria-label="Siguiente">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Siguiente</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>

        <script>
            function mostrarConfirmacionEliminacion(motorId) {
                if (confirm("¿Estás seguro de que deseas eliminar este motor?")) {
                    document.getElementById("form-eliminar-" + motorId).submit();
                }
            }
        </script>
