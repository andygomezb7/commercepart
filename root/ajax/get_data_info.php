<?php
// Conexión a la base de datos y otras configuraciones
session_start();
require_once '../../secure/trun.php';
require_once '../../secure/class/inventario.php';
$inventario = new Inventario($db);

$method = $_REQUEST['method'];

switch ($method) {
    case 'detectarbodegas':
    // Supongamos que necesitas 50 repuestos del ID 5
    $repuestoId = @$_REQUEST['id'];
    $repuestosNecesarios = @$_REQUEST['cantidad'];

    if (intval($repuestoId) && intval($repuestosNecesarios)) {
        // Obtener información sobre la disponibilidad de repuestos en bodegas para el repuesto específico
        $disponibilidadRepuesto = $inventario->obtenerTotalRepuestosPorBodega(null, $repuestoId);

        // Crear un array para rastrear cuántos repuestos tomar de cada bodega
        $repuestosDeBodegas = [];

        foreach ($disponibilidadRepuesto as $bodegaInfo) {
            // Verificar si esta bodega tiene repuestos disponibles
            if ($bodegaInfo['total'] > 0) {
                $bodegaId = $bodegaInfo['bodega_id'];
                $cantidadDisponible = $bodegaInfo['total'];

                // Determinar cuántos repuestos tomar de esta bodega
                $cantidadAExtraer = min($repuestosNecesarios, $cantidadDisponible);

                // Registrar cuántos repuestos tomarás de esta bodega
                $repuestosDeBodegas[] = array(
                    'bodegaid' => $bodegaId,
                    'cantidad' => $cantidadAExtraer,
                    'bodeganame' => $bodegaInfo['nombre_bodega']
                );

                // Actualizar la cantidad necesaria
                $repuestosNecesarios -= $cantidadAExtraer;

                // Si ya tienes suficientes repuestos, puedes salir del bucle
                if ($repuestosNecesarios <= 0) {
                    break;
                }
            }
        }

    } else {
        $repuestosDeBodegas = array('error' => 'hace falta información');
    }
    echo json_encode($repuestosDeBodegas);
    break;
}