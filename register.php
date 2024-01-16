<?php
require_once 'User.php';
require_once 'secure/trun.php';

// Crear una instancia de la clase User
$user = new User($db);

// Procesar el formulario de registro
if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  if ($user->register($username, $email, $password)) {
    // Registro exitoso, redirigir al usuario a una página de éxito o realizar alguna otra acción
    header('location: index.php');
  } else {
    // Error en el registro, mostrar mensaje de error o realizar alguna otra acción
    header('location: index.php?error=Error en el registro');
  }
}

$db->close();
?>