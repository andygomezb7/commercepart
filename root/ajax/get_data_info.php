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
        $disponibilidadRepuesto = $inventario->obtenerTotalRepuestosPorBodega(null, $repuestoId, true);

        // Crear un array para rastrear cuántos repuestos tomar de cada bodega
        $repuestosDeBodegas = [];

        foreach ($disponibilidadRepuesto as $bodegaInfo) {
            $isMyBodega = $db->query("SELECT bodega_id FROM usuarios_bodegas WHERE usuario_id = '".$_SESSION['usuario_id']."' AND bodega_id = '".$bodegaInfo['bodega_id']."' AND empresa_id = ". $_SESSION['empresa_id'])->fetch_assoc();
            // Verificar si esta bodega tiene repuestos disponibles
            if ($bodegaInfo['total'] > 0 && @$isMyBodega['bodega_id']) {
                $bodegaId = $bodegaInfo['bodega_id'];
                $cantidadDisponible = $bodegaInfo['total'];

                // Determinar cuántos repuestos tomar de esta bodega
                $cantidadAExtraer = min($repuestosNecesarios, $cantidadDisponible);

                // Registrar cuántos repuestos tomarás de esta bodega
                $repuestosDeBodegas[] = array(
                    'bodegaid' => $bodegaId,
                    'cantidad' => ($cantidadAExtraer>0?$cantidadAExtraer-intval($bodegaInfo['reserva']):0),
                    'bodeganame' => $bodegaInfo['nombre_bodega'],
                    'reserva' => intval($bodegaInfo['reserva']),
                    'nombre_repuesto' => $bodegaInfo['nombre_repuesto'],
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
        $repuestosDeBodegas = 'hace falta información';
    }
    echo json_encode($repuestosDeBodegas);
    break;
}