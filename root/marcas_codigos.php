<?php
include('../secure/class/marcas_codigos.php');  // Asegúrate de incluir el archivo correcto
$aMarcasCodigos = new MarcasCodigos($db);

$mensaje = '';

// Agregar Marca de Código
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    // Manejo de la imagen
    $imagen = '';
    if (isset($_FILES['imagen'])) {
        $imagenTmp = $_FILES['imagen']['tmp_name'];
        $imagenNombre = $_FILES['imagen']['name'];
        $imagenExtension = pathinfo($imagenNombre, PATHINFO_EXTENSION);
        $imagenNombreGuardado = 'uploads/' . uniqid() . '.' . $imagenExtension;
        
        if (move_uploaded_file($imagenTmp, $imagenNombreGuardado)) {
            $imagen = 'root/'.$imagenNombreGuardado;
            $mensaje = 'Imagen subida exitosamente';
        } else {
            $mensaje = 'Error al guardar la nueva imagen<br>';
        }
    }

    if ($aMarcasCodigos->agregarMarcaCodigo($nombre, $imagen)) {
        $mensaje = 'La marca de código se ha agregado correctamente.';
    } else {
        $mensaje = 'Error al agregar la marca de código.';
    }
}

// Editar Marca de Código
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    // Manejo de la imagen
    $imagen = '';
    if (isset($_FILES['imagen'])) {
        $imagenTmp = $_FILES['imagen']['tmp_name'];
        $imagenNombre = $_FILES['imagen']['name'];
        $imagenExtension = pathinfo($imagenNombre, PATHINFO_EXTENSION);
        $imagenNombreGuardado = 'uploads/' . uniqid() . '.' . $imagenExtension;
        
        if (move_uploaded_file($imagenTmp, $imagenNombreGuardado)) {
            $imagen = 'root/'.$imagenNombreGuardado;
            $mensaje = 'Imagen subida exitosamente';
        } else {
            $mensaje = 'Error al guardar la nueva imagen<br>';
        }
    }

    if (!empty($imagen) && isset($id)) {
        $db->query("UPDATE marcas_codigos SET imagen = '$imagen' WHERE id = $id");
    }

    if ($aMarcasCodigos->editarMarcaCodigo($id, $nombre)) {
        $mensaje = 'La marca de código se ha actualizado correctamente.'. $imagen;
    } else {
        $mensaje = 'Error al actualizar la marca de código.';
    }
}

// Eliminar Marca de Código
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    if ($aMarcasCodigos->eliminarMarcaCodigo($id)) {
        $mensaje = 'La marca de código se ha eliminado correctamente.';
    } else {
        $mensaje = 'Error al eliminar la marca de código.';
    }
}
?>

<?php if (!empty($mensaje)) : ?>
    <div class="alert alert-success" role="alert">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<div class="jumbotron py-4 bg-white border">
    <?php if (isset($_GET['editar'])) : ?>
        <?php
        $idEditar = $_GET['editar'];
        $marcaCodigoEditar = $aMarcasCodigos->obtenerMarcaCodigoPorID($idEditar);
        ?>
        <p class="lead">Edita la marca de código de forma rápida.</p>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $marcaCodigoEditar['nombre']; ?>">
            </div>
            <div class="form-group mb-5">
                <label for="imagen">Imagen:</label>
                <input type="file" name="imagen" id="imagen" class="form-control-file" accept="image/*" onchange="previewImage(this);">
                <img id="imagen-preview" src="<?php echo isset($marcaCodigoEditar['imagen']) ? '../'.$marcaCodigoEditar['imagen'] : '../styles/images/empty.png'; ?>" alt="Vista previa" class="mt-2" style="max-width: 200px;">
            </div>
            <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
        </form>
        <script>
            function previewImage(input) {
                var preview = document.getElementById('imagen-preview');
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    <?php else : ?>
        <div class="d-flex">
            <p class="lead flex-fill">Crea una nueva marca de código.</p>
            <a class="btn btn-success float-right text-light" href="javascript:void(0)" onclick="toggleForm($(this), '.formadding')"><i class="fa fa-plus"></i> Agregar</a>
        </div>
        <form action="" class="formadding" style="display:none;" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="form-group mb-5">
                <label for="imagen">Imagen:</label>
                <input type="file" name="imagen" id="imagen" class="form-control-file" accept="image/*" onchange="previewImage(this);">
                <img id="imagen-preview" src="<?php echo isset($marcaCodigoEditar['imagen']) ? '../'.$marcaCodigoEditar['imagen'] : '../styles/images/empty.png'; ?>" alt="Vista previa" class="mt-2" style="max-width: 200px;">
            </div>
            <button type="submit" name="guardar" class="btn btn-primary">Agregar</button>
        </form>
        <script>
            function previewImage(input) {
                var preview = document.getElementById('imagen-preview');
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    <?php endif; ?>
</div>

<table class="table table-striped table-bordered dt-responsive nowrap w-100" id="marcasCodigosTable">
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
    $('#marcasCodigosTable').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url": "ajax/get_data_table.php?method=marcas_codigos", // Cambiar a la ruta correcta
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
                    return '<div class="btn-group btn-group-toggle" data-toggle="buttons"><a href="?tipo=17&editar=' + row.id + '" class="btn btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>' +
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
