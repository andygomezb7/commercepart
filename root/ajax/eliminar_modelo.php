<?php
// Conexión a la base de datos y otras configuraciones
session_start();
require_once '../../secure/trun.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $modeloId = $_POST['id'];
    
    // Realizar las operaciones necesarias para eliminar el modelo con el ID proporcionado
    
    // Ejemplo: Eliminar el modelo de la base de datos
    $db->query("DELETE FROM modelos WHERE id='$modeloId'");
    
    // Respuesta de éxito
    echo "El modelo se ha eliminado correctamente.";
} else {
    // Respuesta de error
    echo "Error al eliminar el modelo.";
}
?>