<?php
// Conexión a la base de datos y otras configuraciones
session_start();
require_once '../../secure/trun.php';
require_once '../../secure/class/inventario.php';

$inventario = new Inventario($db);
$method = $_REQUEST['method'];

switch ($method) {
    case 'bodegas':
        $rowid = $_REQUEST['bodega'];
        $bodegas = [];
        $resultadosRepuesto = $inventario->obtenerTotalRepuestosPorBodega(null, $rowid, true);
        foreach($resultadosRepuesto AS $repuesto) {
            $bodegas[] = array(
                'bodeganame' => $repuesto['nombre_bodega'],
                'cantidad' => $repuesto['inventario'],
                'reserva' => $repuesto['reserva']
            );
        }

        echo json_encode($bodegas);
    break;
    case 'bodegashistorial':
        $rowid = $_REQUEST['bodega'];
        $repid = $_REQUEST['rep'];
        $bodegas = [];
        $resultadosRepuesto = $inventario->obtenerTotalRepuestosPorBodega(null, $rowid, true);
        foreach($resultadosRepuesto AS $repuesto) {
            $bodegas[] = array(
                'bodeganame' => $repuesto['nombre_bodega'],
                'cantidad' => $repuesto['inventario'],
                'reserva' => $repuesto['reserva']
            );
        }

        echo json_encode($bodegas);
    break;
}