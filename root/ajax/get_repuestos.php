<?php
// Conexión a la base de datos y otras configuraciones
session_start();
require_once '../../secure/trun.php';

// Obtener el ID del modelo seleccionado
$modeloId = $_POST['modelo_id'];

// Obtener los repuestos correspondientes al modelo seleccionado
$repuestos = $db->query("SELECT * FROM repuestos")->fetch_assoc();

// Generar las opciones de los repuestos
$options = '<option value="" disabled selected>Seleccione un repuesto</option>';
foreach ($repuestos as $repuesto) {
    $options .= '<option value="' . $repuesto['id'] . '">' . $repuesto['nombre'] . '</option>';
}

// Devolver las opciones de los repuestos
echo $options;
?>