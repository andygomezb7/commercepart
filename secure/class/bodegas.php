<?php
/*
*
* @title Bodegas
* @description "Clase para control de Bodegas"
* @author Andy Gomez
* @project "AlphaParts"
*
*/

Class Bodega {
	private $db;

    public function __construct($db) {
        $this->db = $db;
    }

	public function trasladoInventario () {
		$repuestoId = 123; // ID del repuesto transferido
		$bodegaOrigenId = 1; // ID de la bodega de origen
		$bodegaDestinoId = 2; // ID de la bodega de destino
		$tipo = 'transferencia';
		$cantidad = 20; // Cantidad de repuestos transferidos
		$usuarioId = 789; // ID del usuario que registra la transferencia
		$comentario = 'Transferencia de repuestos entre bodegas';

		if (insertarMovimientoInventario($repuestoId, $bodegaOrigenId, $tipo, -$cantidad, null, null, $usuarioId, $comentario)) {
		    if (insertarMovimientoInventario($repuestoId, $bodegaDestinoId, $tipo, $cantidad, null, null, $usuarioId, $comentario)) {
		        echo "Transferencia de repuestos registrada con éxito.";
		    } else {
		        echo "Error al registrar la transferencia de repuestos (destino).";
		    }
		} else {
		    echo "Error al registrar la transferencia de repuestos (origen).";
		}
	}
}