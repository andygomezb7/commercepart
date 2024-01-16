<?php
// Conexión a la base de datos y otras configuraciones
session_start();
require_once '../../secure/trun.php';

// Obtener el ID de la marca seleccionada
$marcaId = $_POST['marca_id'];
$selected = @$_POST['selected'];

// Obtener los modelos correspondientes a la marca seleccionada
$modelos = $db->query("SELECT * FROM modelos WHERE marca_id = '$marcaId'");

// Generar las opciones de los modelos
$options = '<option value="" disabled selected>Seleccione un modelo</option>';
foreach ($modelos as $modelo) {
    $options .= '<option value="' . $modelo['id'] . '" '.($selected&&$selected==$modelo['id'] ? 'selected' : '').'>' . $modelo['nombre'] . '</option>';
}

// Devolver las opciones de los modelos
echo $options;
?>
