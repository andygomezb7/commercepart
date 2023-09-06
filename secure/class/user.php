<?php
/*
*
* @title User
* @description "Clase para control de usuarios"
* @author Andy Gomez
* @project "AlphaParts"
*
*/

require(__DIR__ . '/../trun.php');

class User {
  private $db;

  public function __construct($db) {
    $this->db = $db;
  }

  // Registro de usuario
  public function register($username, $email, $password, $tipo, $empresa) {
    // Encriptar la contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Preparar y ejecutar la consulta SQL
    $stmt =  $this->db->prepare("INSERT INTO usuarios (nombre, email, password, tipo, empresa_id) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die('Error al preparar la consulta.');
    }

    // Asignar los valores de los parámetros
    $stmt->bind_param("sss", $username, $email, $hashedPassword, $tipo, $empresa);

    // Ejecutar la consulta
    $result = $stmt->execute();
    if ($result === false) {
        die('Error al ejecutar la consulta.');
    }

    // Verificar si se insertaron registros correctamente
    if ($stmt->affected_rows > 0) {
      return true;
    } else {
      return false;
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conn->close();

  }

  // Inicio de sesión de usuario
  public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT id, email, password FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $dbEmail, $dbPassword);
            $stmt->fetch();

            // Verificar la contraseña ingresada
            if (password_verify($password, $dbPassword)) {
                // Contraseña válida, iniciar sesión
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = $email;
                return true;
            } else {
                // Contraseña incorrecta
                return false;
            }
        } else {
            // El correo electrónico no está registrado
            return false;
        }
    }

    public function getUserByEmail($email) {
        try {
            $stmt = $this->db->query("SELECT * FROM usuarios WHERE email = '$email'");
            $user = $stmt->fetch_assoc();

            return $user;
        } catch (PDOException $e) {
            // Manejo de errores
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    public function getUserType($email) {
        $stmt = $this->db->prepare("SELECT tipo FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($tipo);
        $stmt->fetch();
        $stmt->close();

        return $tipo;
    }
    public function validateSession() {
        // Verificar si la sesión está activa
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Verificar si el usuario ha iniciado sesión en $_SESSION
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['email'])) {
                $email = $_SESSION['email'];

                // Verificar si el usuario ha iniciado sesión en $_COOKIE
                if (isset($_COOKIE['user_email'])) {
                    $cookieData = $_COOKIE['user_email'];
                    // Realizar cualquier validación adicional de la cookie

                    // Actualizar la cookie si es necesario
                    // setcookie('login', $cookieData, time() + 3600, '/');

                    return true;
                } else {
                    // La sesión existe, pero no se encontró la cookie de inicio de sesión
                    // Realizar cualquier acción adicional requerida

                    return false;
                }
            }
        }

        return false;
    }
}

?>