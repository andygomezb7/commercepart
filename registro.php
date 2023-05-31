<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fitinpart - Registro</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="styles.css"> <!-- Estilos adicionales -->
  <style>
    /* Estilos adicionales para la pantalla de registro */
    .register-form {
      max-width: 400px;
      margin: 50px auto;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <?php include 'header.php'; ?>

  <!-- Contenido de la pantalla de registro -->
  <div class="container">
    <div class="register-form">
      <h2>Registro de usuario</h2>
      <form id="registrationForm" method="post" action="register.php">
        <div class="form-group">
          <label for="username">Nombre</label>
          <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
          <label for="email">Correo electrónico</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="password">Contraseña</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
          <label for="confirmPassword">Confirmar contraseña</label>
          <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrarse</button>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <?php include 'footer.php'; ?>

  <script>
    // Validación del formulario de registro utilizando JavaScript
    const registrationForm = document.getElementById('registrationForm');
    registrationForm.addEventListener('submit', function(event) {
      event.preventDefault();
      // Realizar la validación del formulario y enviar los datos al servidor
      // Puedes utilizar AJAX para enviar los datos a un archivo PHP para el procesamiento
      // Aquí se muestra un ejemplo básico de validación
      const name = document.getElementById('username').value;
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirmPassword').value;

      if (name === '' || email === '' || password === '' || confirmPassword === '') {
        alert('Por favor, complete todos los campos.');
        return;
      }

      if (password !== confirmPassword) {
        alert('Las contraseñas no coinciden.');
        return;
      }

      // Enviar los datos del formulario al servidor
      registrationForm.submit();
    });
  </script>
</body>
</html>
