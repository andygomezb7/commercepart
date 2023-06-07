<?php
// Obtener la lista de bodegas
$bodegas = $db->query("SELECT * FROM bodegas")->fetch_all(MYSQLI_ASSOC);

// Mensaje de éxito o error
$mensaje = '';

// Editar Repuesto
if (isset($_GET['editar'])) {
    $idRepuestoEditar = $_GET['editar'];
    $repuestoEditar = $db->query("SELECT * FROM repuestos WHERE id = $idRepuestoEditar")->fetch_assoc();
}

// Agregar o Actualizar Repuesto
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $bodega = $_POST['bodega'];

    // Si se proporciona un ID, actualizar el repuesto existente
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $db->query("UPDATE repuestos SET nombre = '$nombre', descripcion = '$descripcion', precio = $precio, ubicacion_bodega = '$bodega' WHERE id = $id");
        $mensaje = 'El repuesto se ha actualizado correctamente.';
    } else { // Si no se proporciona un ID, agregar un nuevo repuesto
        $result = $db->query("INSERT INTO repuestos (nombre, descripcion, precio, ubicacion_bodega) VALUES ('$nombre', '$descripcion', $precio, '$bodega')");
        if (!$result) {
            // Ha ocurrido un error
            $error = mysqli_error($db);
            $mensaje = "Error en la consulta: " . $error;
        } else {
            $mensaje = 'El repuesto se ha agregado correctamente.';
        }
    }
}

// Eliminar Repuesto
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    $db->query("DELETE FROM repuestos WHERE id = $id");
    $mensaje = 'El repuesto se ha eliminado correctamente.';
}

// Obtener la lista actualizada de repuestos
$repuestos = $db->query("SELECT r.*, b.nombre as ubicacion_bodega FROM repuestos AS r LEFT JOIN bodegas AS b ON r.ubicacion_bodega = b.id")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Repuestos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>Dashboard - Listado de Repuestos</h1>

        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['editar']) || isset($_GET['agregar'])) : ?>
            <!-- Formulario de agregar/editar -->
            <h2><?php echo isset($_GET['editar']) ? 'Editar Repuesto' : 'Agregar Repuesto'; ?></h2>
            <form action="" method="POST">
                <?php if (isset($_GET['editar'])) : ?>
                    <input type="hidden" name="id" value="<?php echo $idRepuestoEditar; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo isset($repuestoEditar['nombre']) ? $repuestoEditar['nombre'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea name="descripcion" id="descripcion" class="form-control" required><?php echo isset($repuestoEditar['descripcion']) ? $repuestoEditar['descripcion'] : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="precio">Precio:</label>
                    <input type="number" name="precio" id="precio" class="form-control" value="<?php echo isset($repuestoEditar['precio']) ? $repuestoEditar['precio'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="bodega">Bodega:</label>
                    <select name="bodega" id="bodega" class="form-control" required>
                        <?php foreach ($bodegas as $bodega) : ?>
                            <option value="<?php echo $bodega['id']; ?>" <?php echo (isset($repuestoEditar['bodega']) && $repuestoEditar['bodega'] == $bodega['nombre']) ? 'selected' : ''; ?>><?php echo $bodega['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="guardar" class="btn btn-primary"><?php echo isset($_GET['editar']) ? 'Actualizar' : 'Agregar'; ?></button>
            </form>
        <?php else : ?>
            <!-- Tabla de repuestos -->
            <a href="?tipo=3&agregar=1" class="btn btn-success mb-3">Agregar Repuesto</a>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Bodega</th>
                        <th>Códigos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($repuestos as $repuesto) : ?>
                        <?php
                        $idRepuesto = $repuesto['id'];
                        $codigosRepuesto = $db->query("SELECT codigo FROM codigos_repuesto WHERE id_repuesto = $idRepuesto")->fetch_all(MYSQLI_ASSOC);
                        $codigos = array_column($codigosRepuesto, 'codigo');
                        ?>
                        <tr>
                            <td><?php echo $repuesto['id']; ?></td>
                            <td><?php echo $repuesto['nombre']; ?></td>
                            <td><?php echo $repuesto['descripcion']; ?></td>
                            <td><?php echo $repuesto['precio']; ?></td>
                            <td><?php echo $repuesto['ubicacion_bodega']; ?></td>
                            <td><?php echo implode(', ', $codigos); ?></td>
                            <td>
                                <a href="?tipo=3&editar=<?php echo $repuesto['id']; ?>" class="btn btn-primary">Editar</a>
                                <form action="" method="POST" style="display: inline-block;">
                                    <input type="hidden" name="id" value="<?php echo $repuesto['id']; ?>">
                                    <button type="submit" name="eliminar" class="btn btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>
