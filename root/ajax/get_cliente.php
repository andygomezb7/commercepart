<?php
// Conexión a la base de datos y otras configuraciones
session_start();
require_once '../../secure/trun.php';

// Obtener el ID del cliente seleccionado desde la solicitud GET
$clienteId = $_GET['cliente_id'];

// Consulta SQL para obtener los datos del cliente
$sql = "SELECT nit, nombre, email FROM clientes WHERE id = $clienteId AND empresa_id = " . $_SESSION['empresa_id'];

// Ejecutar la consulta
$result = $db->query($sql);

if ($result->num_rows > 0) {
    // Obtener los datos del cliente como un array asociativo
    $clienteData = $result->fetch_assoc();

    // Devolver los datos del cliente en formato JSON
    header("Content-Type: application/json");
    echo json_encode($clienteData);
} else {
    // Si no se encuentra el cliente, devolver un objeto JSON vacío
    echo json_encode(array());
}

?>