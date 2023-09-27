<?php
// Función para obtener todos los usuarios de la base de datos
if ($_SESSION['usuario_id']!=1) {
    header('location: index.php');
}

function obtenerUsuarios()
{
    global $db;
    $query = "SELECT * FROM usuarios";
    $result = $db->query($query);
    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
    return $usuarios;
}

$tiposUser = array(1 => "Administrador", 2 => "Usuario", 3 => "Empleado");

// Verificar si se ha enviado el formulario de edición o agregado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Editar usuario
    if (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $tipo = $_POST['tipo'];
        $empresa = $_POST['empresa'];

        // Actualizar usuario en la base de datos
        $query = "UPDATE usuarios SET nombre = '$nombre', email = '$email', tipo = '$tipo', empresa_id = '$empresa' WHERE id = '$id'";
        $db->query($query);

        $error = array(
            'color' => 'success',
            'text' => 'Actualizado correctamente'
        );
    }
    // Agregar usuario
    elseif (isset($_POST['agregar'])) {
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $tipo = $_POST['tipo'];
        $empresa = $_POST['empresa'];

        require_once '../secure/class/user.php';

        // Crear una instancia de la clase User
        $user = new User($db);

        // Procesar el formulario de registro
        if (isset($_POST['nombre']) && isset($_POST['email']) && isset($_POST['password'])) {

          if ($user->register($nombre, $email, $password, $tipo, $empresa)) {
            $error = array(
                'color' => 'success',
                'text' => 'Usuario agregado correctamente'
            );
          } else {
            $error = array(
                'color' => 'danger',
                'text' => 'Error al crear el usuario'
            );
          }

      } else {
        $error = array(
            'color' => 'danger',
            'text' => 'Hacen falta campos al enviar el usuario'
        );
      }
    }
}

// Verificar si se ha recibido el parámetro de edición en el método GET
if (isset($_GET['editar']) || isset($_GET['agregar'])) {
    $idUsuarioEditar = @$_GET['editar'];

    // Obtener los datos del usuario a editar
    if ($idUsuarioEditar) {
        $query = "SELECT * FROM usuarios WHERE id = '$idUsuarioEditar'";
        $result = $db->query($query);
        $usuarioEditar = $result->fetch_assoc();
    } 

    // Mostrar el formulario de edición del usuario
    ?>
            <?php
                if (is_array(@$error)) {
                    echo "<div class='alert alert-$error[color]'>$error[text]</div>";
                }
            ?>
            <h1><?php echo ($idUsuarioEditar) ? 'Editar' : 'Agregar'; ?> Usuario</h1>
            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo @$idUsuarioEditar; ?>">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo @$usuarioEditar['nombre']; ?>">
                </div>
                <div class="form-group">
                    <label for="email">email Electrónico:</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo @$usuarioEditar['email']; ?>">
                </div>
                <?php if (!$idUsuarioEditar) { ?>
                    <div class="form-group">
                      <label for="password">Contraseña</label>
                      <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <label for="tipo">Tipo:</label>
                    <select name="tipo" id="tipo" class="form-control">
                        <option value="">Selecciona una opción</option>
                        <option value="1" <?php echo (@$usuarioEditar['tipo'] == 1) ? 'selected' : ''; ?>>Administrador</option>
                        <option value="2" <?php echo (@$usuarioEditar['tipo'] == 2) ? 'selected' : ''; ?>>Usuario</option>
                        <option value="3" <?php echo (@$usuarioEditar['tipo'] == 3) ? 'selected' : ''; ?>>Empleado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="empresa">Empresa:</label>
                    <select name="empresa" id="empresa" class="form-control">
                        <option value="">Selecciona una opción</option>
                        <?php
                            $empresas = $db->query("SELECT * FROM empresas");
                            foreach($empresas as $empresa) {
                        ?>
                            <option value="<?php echo $empresa['id']; ?>" <?php echo (@$usuarioEditar['empresa_id']==$empresa['id'] ? 'selected' :''); ?>><?php echo $empresa['nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" name="<?php echo ($idUsuarioEditar) ? 'editar' : 'agregar'; ?>" class="btn btn-primary"><?php echo ($idUsuarioEditar) ? 'editar' : 'agregar'; ?> Usuario</button>
            </form>
<?php
} else {
    // Mostrar la tabla de usuarios
    $usuarios = obtenerUsuarios();
    ?>
            <table class="table table-striped table-bordered dt-responsive nowrap w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Tipo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario) : ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td><?php echo $usuario['nombre']; ?></td>
                            <td><?php echo $usuario['email']; ?></td>
                            <td><?php echo $tiposUser[$usuario['tipo']]; ?></td>
                            <td>
                                <a href="?tipo=102&editar=<?php echo $usuario['id']; ?>" class="btn btn-primary">Editar</a>
                                <form action="" method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                    <button type="submit" name="eliminar" class="btn btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
<?php
    }
?>
<script type="text/javascript">
    $(document).ready(function() {
            $('table.table').DataTable({
                "responsive": true
            });
    });
</script>