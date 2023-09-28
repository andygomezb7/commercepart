<?php
// Mensaje de éxito o error
$mensaje = '';

// Agregar Categoría
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];

    $query = "INSERT INTO categorias (nombre, empresa_id) VALUES ('".$nombre."', '".$_SESSION['empresa_id']."')";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute()) {
        $mensaje = 'La categoría se ha agregado correctamente.';
    } else {
        $mensaje = 'Error al agregar la categoría.';
    }
}

// Editar Categoría
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];

    $query = "UPDATE categorias SET nombre = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('si', $nombre, $id);

    if ($stmt->execute()) {
        $mensaje = 'La categoría se ha actualizado correctamente.';
    } else {
        $mensaje = 'Error al actualizar la categoría.';
    }
}

// Eliminar Categoría
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    $query = "DELETE FROM categorias WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        $mensaje = 'La categoría se ha eliminado correctamente.';
    } else {
        $mensaje = 'Error al eliminar la categoría.';
    }
}

?>

        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de agregar/editar categoría -->
        <div class="jumbotron py-4 bg-white border">
            <?php if (isset($_GET['editar'])) : ?>
                <?php
                $idEditar = $_GET['editar'];
                $categoriaEditar = $db->query("SELECT * FROM categorias WHERE id='$idEditar'")->fetch_assoc();
                ?>
                  <p class="lead">Edita la categoría de forma rápida.</p>
                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $categoriaEditar['nombre']; ?>">
                    </div>
                    <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
                </form>
            <?php else : ?>
                  <p class="lead">Crea una nueva categoría.</p>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <button type="submit" name="guardar" class="btn btn-primary">Agregar</button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Tabla de categorías -->
        <!-- <h2>Listado de Categorías</h2> -->
        <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="categoriasTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <script>
            $(document).ready(function() {
                $('#categoriasTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "ajax": {
                        "url": "ajax/get_data_table.php?method=categorias", // Cambiar a la ruta correcta
                        "type": "POST",
                        "data": function (d) {
                            // d.length = d.length || 10;
                            d.search = d.search.value || "";
                            // Otros parámetros de búsqueda que quieras agregar
                        },
                        "dataSrc": "data"
                    },
                    "columns": [
                        { "data": "id" },
                        { "data": "nombre" },
                        {
                            "data": null,
                            "render": function(data, type, row) {
                                return '<div class="btn-group btn-group-toggle" data-toggle="buttons"><a href="?tipo=7&editar=' + row.id + '" class="btn btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>' +
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