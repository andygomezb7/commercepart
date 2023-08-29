<?php
session_start();
require_once 'secure/class/user.php';
require_once 'secure/trun.php';

// Crear una instancia de la clase User
$user = new User($db);

// Verificar si ya se ha iniciado sesión
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Obtener los parámetros enviados desde index.php y sanitizarlos
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

// Verificar los datos ingresados
if (!empty($email) && !empty($password)) {


    // Validar los datos de inicio de sesión en la base de datos
    if ($user->login($email, $password)) {
        // Inicio de sesión exitoso
        $_SESSION['loggedin'] = true;
        $_SESSION['email'] = $email;
        // Verificar si el usuario es administrador (tipo 1)
        if ($user->getUserType($email) == 1) {
            $_SESSION['admin'] = true;
        }

        // Crear una cookie para el seguimiento del usuario
        $cookie_name = 'user_email';
        $cookie_value = $email;
        $cookie_expiration = time() + (86400 * 30); // 30 días
        setcookie($cookie_name, $cookie_value, $cookie_expiration, '/');
        
        header('Location: index.php');
        exit;
    } else {
        // Credenciales inválidas
        header('Location: index.php?error=1');
        exit;
    }
} else {
    // Datos de inicio de sesión incompletos
    header('Location: index.php?error=2');
    exit;
}
?>
