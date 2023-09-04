<?php
// Mensaje de éxito o error
$mensaje = '';

// Obtener los datos del usuario actual (asumiendo que tienes una variable de sesión que almacena el ID del usuario)
$email = $_SESSION['email']; // Ajusta esto según cómo tengas almacenado el ID del usuario
$usuarioActual = $db->query("SELECT * FROM usuarios WHERE email='$email'")->fetch_assoc();

// Editar Perfil de Usuario
if (isset($_POST['editar'])) {
    $id = $email;
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];

    // Comprobar si se proporcionó la contraseña actual y la nueva contraseña
    if (!empty($_POST['contrasena_actual']) && !empty($_POST['nueva_contrasena'])) {
        $contrasenaActual = $_POST['contrasena_actual'];
        $nuevaContrasena = $_POST['nueva_contrasena'];

        // Comprobar si la contraseña actual coincide con la almacenada en la base de datos
        if (password_verify($contrasenaActual, $usuarioActual['password'])) {
            // Generar el hash de la nueva contraseña y actualizarla en la base de datos
            $hashNuevaContrasena = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
            $db->query("UPDATE usuarios SET nombre='$nombre', email='$email', password='$hashNuevaContrasena' WHERE id='$id'");
            $mensaje = 'El perfil se ha actualizado correctamente, incluyendo la contraseña.';
        } else {
            $mensaje = 'La contraseña actual proporcionada es incorrecta.';
        }
    } else {
        // Si no se proporcionó la contraseña actual y la nueva contraseña, actualizar solo los otros campos
        $db->query("UPDATE usuarios SET nombre='$nombre', email='$email' WHERE email='$id'");
        $mensaje = 'El perfil se ha actualizado correctamente.';
    }

    // Obtener los datos actualizados del usuario
    $usuarioActual = $db->query("SELECT * FROM usuarios WHERE email='$email'")->fetch_assoc();
}
?>

<!-- Agrega aquí tu HTML y diseño -->

<h2>Editar Perfil de Usuario</h2>
<?php if (!empty($mensaje)) : ?>
    <div class="alert alert-success" role="alert">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<form action="" method="POST">
    <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" class="form-control" required value="<?php echo $usuarioActual['nombre']; ?>">
    </div>
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" class="form-control" required value="<?php echo $usuarioActual['email']; ?>">
    </div>
    <div class="form-group">
        <label for="contrasena_actual">Contraseña Actual:</label>
        <input type="password" name="contrasena_actual" id="contrasena_actual" class="form-control">
    </div>
    <div class="form-group">
        <label for="nueva_contrasena">Nueva Contraseña:</label>
        <input type="password" name="nueva_contrasena" id="nueva_contrasena" class="form-control">
    </div>
    <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
</form>
