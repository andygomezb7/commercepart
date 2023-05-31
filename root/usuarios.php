<?php
// Función para obtener todos los usuarios de la base de datos
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

// Verificar si se ha enviado el formulario de edición o agregado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Editar usuario
    if (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $tipo = $_POST['tipo'];

        // Actualizar usuario en la base de datos
        $query = "UPDATE usuarios SET nombre = '$nombre', email = '$email', tipo = '$tipo' WHERE id = '$id'";
        $db->query($query);

        $error = array(
            'color' => 'success',
            'text' => 'Actualizado correctamente'
        );
        exit;
    }
    // Agregar usuario
    elseif (isset($_POST['agregar'])) {
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $tipo = $_POST['tipo'];

        require_once '../User.php';

        // Crear una instancia de la clase User
        $user = new User($db);

        // Procesar el formulario de registro
        if (isset($_POST['nombre']) && isset($_POST['email']) && isset($_POST['password'])) {

          if ($user->register($nombre, $email, $password)) {
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

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <title>Editar Usuario</title>
    </head>

    <body>
        <div class="container">
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
                        <option value="1" <?php echo (@$usuarioEditar['tipo'] == 1) ? 'selected' : ''; ?>>Administrador</option>
                        <option value="2" <?php echo (@$usuarioEditar['tipo'] == 2) ? 'selected' : ''; ?>>Usuario</option>
                    </select>
                </div>
                <button type="submit" name="<?php echo ($idUsuarioEditar) ? 'editar' : 'agregar'; ?>" class="btn btn-primary"><?php echo ($idUsuarioEditar) ? 'editar' : 'agregar'; ?> Usuario</button>
            </form>
        </div>
    </body>

    </html>

    <?php
} else {
    // Mostrar la tabla de usuarios
    $usuarios = obtenerUsuarios();
    ?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <title>Listado de Usuarios</title>
    </head>

    <body>
        <div class="container">
            <h1>Listado de Usuarios</h1>
            <a href="?tipo=2&agregar=1" class="btn btn-primary">Agregar Usuario</a>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>email Electrónico</th>
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
                            <td><?php echo $usuario['tipo']; ?></td>
                            <td>
                                <a href="dashboard.php?editar=<?php echo $usuario['id']; ?>" class="btn btn-primary">Editar</a>
                                <form action="" method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                    <button type="submit" name="eliminar" class="btn btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </body>

    </html>

    <?php
}
?>