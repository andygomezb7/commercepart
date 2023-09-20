<?php
/*
*
* @title Inventario
* @description "Clase para control de Inventario"
* @author Andy Gomez
* @project "AlphaParts"
*
*/

class Inventario {
	private $db;

    public function __construct($db) {
        $this->db = $db;
    }

	// function insertarMovimientoInventario($repuestoId, $bodegaId, $tipo, $cantidad, $compraId = null, $pedidoId = null, $usuarioId = null, $comentario = null) {

	//     try {
	//         $query = "INSERT INTO inventario_movimientos (repuesto_id, bodega_id, tipo, cantidad, compra_id, pedido_id, usuario_id, comentario) 
	//                   VALUES ('$repuestoId', '$bodegaId', '$tipo', '$cantidad', '$compraId', '$pedidoId', '$usuarioId', '$comentario')";

	//         $stmt = $this->db->prepare($query);

	//         $stmt->execute();

	//         // Si todo fue exitoso, puedes realizar cualquier otra acción necesaria aquí

	//         return true;
	//     } catch (PDOException $e) {
	//         // Manejar cualquier error aquí, como registrar un error o devolver un mensaje de error
	//         return false;
	//     }
	// }

	// function obtenerTotalRepuestosPorBodega($bodegaId = null, $repuestoId = null) {

	//     try {
	//         $query = "SELECT b.nombre AS nombre_bodega, r.nombre AS nombre_repuesto, im.bodega_id, SUM(im.cantidad) AS total
	//                   FROM inventario_movimientos AS im
	//                   INNER JOIN bodegas AS b ON im.bodega_id = b.id
	//                   INNER JOIN repuestos AS r ON im.repuesto_id = r.id ";

	//         if ($bodegaId !== null && $repuestoId !== null) {
	//             // Si se proporcionan ambos IDs, obtenemos el total de repuestos en una bodega específica
	//             $query .= "WHERE im.bodega_id = '".$bodegaId."' AND im.repuesto_id = '".$repuestoId."' ";
	//         } elseif ($bodegaId !== null) {
	//             // Si se proporciona solo el ID de bodega, obtenemos todos los repuestos en esa bodega
	//             $query .= "WHERE im.bodega_id = '".$bodegaId."' ";
	//         } elseif ($repuestoId !== null) {
	//             // Si se proporciona solo el ID de repuesto, obtenemos el total en todas las bodegas
	//             $query .= "WHERE im.repuesto_id = '".$repuestoId."' ";
	//         }

	//         $query .= "GROUP BY b.id, r.id";
	//         $stmt = $this->db->query($query);

	//         return $stmt;
	//     } catch (PDOException $e) {
	//         // Manejar cualquier error aquí, como registrar un error o devolver un mensaje de error
	//         return false;
	//     }
	// }
    function insertarMovimientoInventario($repuestoId, $bodegaId, $tipo, $cantidad, $compraId = null, $pedidoId = null, $usuarioId = null, $comentario = null, $esReserva = false, $fechaEstimada = null) {
	    try {
	        if ($esReserva) {
	            $tabla = "inventario_reserva";
	        } else {
	            $tabla = "inventario_movimientos";
	        }

	        $query = "INSERT INTO $tabla (repuesto_id, bodega_id, tipo, cantidad, compra_id, pedido_id, usuario_id, comentario,empresa_id".($fechaEstimada&&$esReserva?",fecha_estimada":'').") 
	                  VALUES ('$repuestoId', '$bodegaId', '$tipo', '$cantidad', '$compraId', '$pedidoId', '$usuarioId', '$comentario','".$_SESSION['empresa_id']."' ".($fechaEstimada&&$esReserva?",'$fechaEstimada'":'').")";
	        $stmt = $this->db->prepare($query);
	        $stmt->execute();

	        return $query;
	    } catch (PDOException $e) {
	        // Manejar cualquier error aquí, como registrar un error o devolver un mensaje de error
	        return false;
	    }
	}

	function insertarVentaDesdeReserva($repuestoId, $bodegaId, $cantidad, $usuarioId = null, $pedidoId) {
	    try {
	        // Verificar si hay suficiente stock en la reserva
	        $queryStockReserva = "SELECT SUM(cantidad) AS cantidad, fecha_estimada FROM inventario_reserva WHERE repuesto_id = '$repuestoId' AND bodega_id = '$bodegaId' AND empresa_id = " . $_SESSION['empresa_id'];
	        $stmtStockReserva = $this->db->query($queryStockReserva);
	        $row = $stmtStockReserva->fetch_assoc();

	        if ($row['cantidad'] >= $cantidad) {
	            // Si hay suficiente stock en la reserva, realizar la venta desde reserva

	            $queryVentaReserva = "INSERT INTO inventario_movimientos (repuesto_id, bodega_id, tipo, cantidad, usuario_id, comentario, pedido_id, empresa_id) 
	                                  VALUES ('$repuestoId', '$bodegaId', 'venta', '$cantidad', '$usuarioId', 'Venta desde reserva', '$pedidoId', '".$_SESSION['empresa_id']."')";
	            $stmtVentaReserva = $this->db->query($queryVentaReserva);

	            // Obtener la fecha estimada para la reposición del stock
	            $fechaEstimada = $row['fecha_estimada']; // Ejemplo: fecha estimada en 1 mes

	            // Restar la cantidad vendida de la reserva
	            $queryActualizarReserva = "UPDATE inventario_reserva SET cantidad = cantidad - '$cantidad' WHERE repuesto_id = '$repuestoId' AND bodega_id = '$bodegaId' AND empresa_id = " . $_SESSION['empresa_id'];
	            $stmtActualizarReserva = $this->db->query($queryActualizarReserva);

	            // Si todo fue exitoso, puedes realizar cualquier otra acción necesaria aquí
	            return $fechaEstimada;
	        } else {
	            echo "No hay suficiente stock en la reserva";
	            return false;
	        }
	    } catch (PDOException $e) {
	        // Manejar cualquier error aquí, como registrar un error o devolver un mensaje de error
	        // Revertir la transacción si ocurrió un error
	        $this->db->rollback();
	        return false;
	    }
	}

	function trasladarInventario($repuestoId, $bodegaOrigen, $bodegaDestino, $cantidad, $usuarioId, $comentario) {
	    try {
	        // Verificar si hay suficiente stock en la bodega de origen
	        $queryStockBodegaOrigen = "SELECT SUM(cantidad) AS cantidad FROM inventario_movimientos WHERE repuesto_id = '$repuestoId' AND bodega_id = '$bodegaOrigen'";
	        $stmtStockBodegaOrigen = $this->db->query($queryStockBodegaOrigen);
	        $row = $stmtStockBodegaOrigen->fetch_assoc();

	        if ($row['cantidad'] >= $cantidad) {
	            // Si hay suficiente stock en la bodega de origen, realizar el traslado

	            // Registrar el movimiento de entrada en la bodega de destino
	            $queryEntradaBodegaDestino = "INSERT INTO inventario_movimientos (repuesto_id, bodega_id, tipo, cantidad, usuario_id, comentario, empresa_id) 
	                                          VALUES ('$repuestoId', '$bodegaDestino', 'compra', '$cantidad', '$usuarioId', '$comentario', '".$_SESSION['empresa_id']."')";
	            $stmtEntradaBodegaDestino = $this->db->query($queryEntradaBodegaDestino);
				$salidaId = $this->db->insert_id;

	            // Registrar el movimiento de salida en la bodega de origen
	            $querySalidaBodegaOrigen = "INSERT INTO inventario_movimientos (repuesto_id, bodega_id, tipo, cantidad, usuario_id, comentario, pedido_id, empresa_id) 
	                                        VALUES ('$repuestoId', '$bodegaOrigen', 'salida', '$cantidad', '$usuarioId', '$comentario', '".$salidaId."', '".$_SESSION['empresa_id']."')";
	            $stmtSalidaBodegaOrigen = $this->db->query($querySalidaBodegaOrigen);

	            // Si todo fue exitoso, puedes realizar cualquier otra acción necesaria aquí
	            return true;
	        } else {
	            // No hay suficiente stock en la bodega de origen
	            return false;
	        }
	    } catch (PDOException $e) {
	        // Manejar cualquier error aquí, como registrar un error o devolver un mensaje de error
	        // Revertir la transacción si ocurrió un error
	        $this->db->rollback();
	        return false;
	    }
	}

	// function insertarVentaDesdeReserva($repuestoId, $bodegaId, $cantidad, $usuarioId = null, $pedidoId) {
	//     try {
	//         // Verificar si hay suficiente stock en la reserva
	//         $queryStockReserva = "SELECT SUM(cantidad) AS cantidad, fecha_estimada FROM inventario_reserva WHERE repuesto_id = '$repuestoId' AND bodega_id = '$bodegaId' AND empresa_id = " . $_SESSION['empresa_id'];
	//         $stmtStockReserva = $this->db->query($queryStockReserva);
	//         $row = $stmtStockReserva->fetch_assoc();

	//         if ($row['cantidad'] >= $cantidad) {
	//             // Si hay suficiente stock en la reserva, realizar la venta desde reserva
	//             $queryVentaReserva = "INSERT INTO inventario_movimientos (repuesto_id, bodega_id, tipo, cantidad, usuario_id, comentario, pedido_id) 
	//                                   VALUES ('$repuestoId', '$bodegaId', 'salida', '$cantidad', '$usuarioId', 'Venta desde reserva', '$pedidoId')";
	//             $stmtVentaReserva = $this->db->prepare($queryVentaReserva);
	//             $stmtVentaReserva->execute();

	//             // Obtener la fecha estimada para la reposición del stock
	//             $fechaEstimada = $row['fecha_estimada']; // Ejemplo: fecha estimada en 1 mes

	//             // Si todo fue exitoso, puedes realizar cualquier otra acción necesaria aquí
	//             return $fechaEstimada;
	//         } else {
	//             // No hay suficiente stock en la reserva
	//             return false;
	//         }
	//     } catch (PDOException $e) {
	//         // Manejar cualquier error aquí, como registrar un error o devolver un mensaje de error
	//         return false;
	//     }
	// }

	function obtenerTotalRepuestosPorBodega($bodegaId = null, $repuestoId = null, $incluirReserva = false) {
    try {
        $query = "SELECT b.nombre AS nombre_bodega, r.nombre AS nombre_repuesto, ";
        $query .= "im.bodega_id, im.fecha_estimada, ";
        
        if ($incluirReserva) {
            $query .= "SUM(CASE WHEN (im.tipos = 'inventario') THEN im.cantidad ELSE 0 END) - SUM(CASE WHEN (im.tipos = 'salida') THEN im.cantidad ELSE 0 END) AS inventario, ";
            // $query .= "SUM(CASE WHEN (im.tipos = 'salida') THEN -im.cantidad ELSE 0 END) AS salida, ";
            $query .= "SUM(CASE WHEN (im.tipos = 'reserva') THEN im.cantidad ELSE 0 END) AS reserva ";
        } else {
            $query .= "SUM(CASE WHEN (im.tipos = 'inventario') THEN im.cantidad ELSE 0 END) - SUM(CASE WHEN (im.tipos = 'salida') THEN im.cantidad ELSE 0 END) AS inventario, ";
            // $query .= "SUM(CASE WHEN (im.tipos = 'salida') THEN -im.cantidad ELSE 0 END) AS salida, ";
            $query .= "0 AS reserva ";
        }
        
        $query .= "FROM ";
        
        if ($incluirReserva) {
            $query .= "(SELECT repuesto_id, bodega_id, cantidad, 'inventario' AS tipos, fecha_estimada, empresa_id FROM inventario_movimientos ";
            $query .= "UNION ALL ";
            $query .= "SELECT repuesto_id, bodega_id, cantidad, 'salida' AS tipos, fecha_estimada, empresa_id FROM inventario_movimientos WHERE tipo = 'salida' ";
            $query .= "UNION ALL ";
            $query .= "SELECT repuesto_id, bodega_id, cantidad, 'reserva' AS tipos, fecha_estimada, empresa_id FROM inventario_reserva) AS im ";
        } else {
            $query .= "inventario_movimientos AS im ";
        }

        $query .= "INNER JOIN bodegas AS b ON im.bodega_id = b.id ";
        $query .= "INNER JOIN repuestos AS r ON im.repuesto_id = r.id ";
        
        if ($bodegaId !== null && $repuestoId !== null) {
            $query .= "WHERE im.bodega_id = '$bodegaId' AND im.repuesto_id = '$repuestoId' ";
        } elseif ($bodegaId !== null) {
            $query .= "WHERE im.bodega_id = '$bodegaId' ";
        } elseif ($repuestoId !== null) {
            $query .= "WHERE im.repuesto_id = '$repuestoId' ";
        }

        $query .= "AND im.empresa_id = '".$_SESSION['empresa_id']."' ";
        
        $query .= "GROUP BY b.id, r.id";
        
        $stmt = $this->db->query($query);
        
        return $stmt;
    } catch (PDOException $e) {
        return false;
    }
}



	// function obtenerTotalRepuestosPorBodega($bodegaId = null, $repuestoId = null, $incluirReserva = false) {
	//     try {
	//         $query = "SELECT b.nombre AS nombre_bodega, r.nombre AS nombre_repuesto, ";
	//         $query .= "im.bodega_id, im.fecha_estimada, SUM(im.cantidad) AS total,";
	        
	//         if ($incluirReserva) {
	//             // OR im.tipo = 'reserva'
	//             $query .= "SUM(CASE WHEN (im.tipo = 'inventario') THEN (im.cantidad - COALESCE(v.cantidad, 0)) ELSE 0 END) AS inventario, ";
	//             $query .= "SUM(CASE WHEN im.tipo = 'reserva' THEN im.cantidad ELSE 0 END) AS reserva ";
	//         } else {
	//             $query .= "SUM(CASE WHEN im.tipo = 'inventario' THEN (im.cantidad - COALESCE(v.cantidad, 0)) ELSE 0 END) AS inventario ";
	//         }
	        
	//         $query .= "FROM ";
	        
	//         if ($incluirReserva) {
	//             $query .= "(SELECT repuesto_id, bodega_id, cantidad, 'inventario' AS tipo, fecha_estimada, empresa_id FROM inventario_movimientos ";
	//             $query .= "UNION ALL ";
	//             $query .= "SELECT repuesto_id, bodega_id, cantidad, 'reserva' AS tipo, fecha_estimada, empresa_id FROM inventario_reserva) AS im ";
	//         } else {
	//             $query .= "inventario_movimientos AS im ";
	//         }

	//         $query .= "INNER JOIN bodegas AS b ON im.bodega_id = b.id ";
	//         $query .= "INNER JOIN repuestos AS r ON im.repuesto_id = r.id ";
	        
	//         // Subconsulta para obtener las ventas
	//         $query .= "LEFT JOIN (SELECT repuesto_id, bodega_id, SUM(cantidad) AS cantidad FROM inventario_movimientos WHERE tipo = 'venta' GROUP BY repuesto_id, bodega_id) AS v ";
	//         $query .= "ON im.repuesto_id = v.repuesto_id AND im.bodega_id = v.bodega_id ";
	        
	//         if ($bodegaId !== null && $repuestoId !== null) {
	//             // Si se proporcionan ambos IDs, obtenemos el total de repuestos en una bodega específica
	//             $query .= "WHERE im.bodega_id = '$bodegaId' AND im.repuesto_id = '$repuestoId' AND im.empresa_id = '".$_SESSION['empresa_id']."' ";
	//         } elseif ($bodegaId !== null) {
	//             // Si se proporciona solo el ID de bodega, obtenemos todos los repuestos en esa bodega
	//             $query .= "WHERE im.bodega_id = '$bodegaId' AND im.empresa_id = '".$_SESSION['empresa_id']."' ";
	//         } elseif ($repuestoId !== null) {
	//             // Si se proporciona solo el ID de repuesto, obtenemos el total en todas las bodegas
	//             $query .= "WHERE im.repuesto_id = '$repuestoId' AND im.empresa_id = '".$_SESSION['empresa_id']."' ";
	//         }
	        
	//         $query .= "GROUP BY b.id, r.id";
	        
	//         $stmt = $this->db->query($query);
	        
	//         return $stmt;
	//     } catch (PDOException $e) {
	//         // Manejar cualquier error aquí, como registrar un error o devolver un mensaje de error
	//         return false;
	//     }
	// }

	function moverInventarioReservaAlInventarioPrincipal($repuestoId, $bodegaId, $cantidad) {
	    try {
	        // 1. Resta la cantidad de inventario en reserva
	        $queryRestarReserva = "UPDATE inventario_reserva SET cantidad = cantidad - $cantidad 
	                               WHERE repuesto_id = '$repuestoId' AND bodega_id = '$bodegaId'";
	        $stmtRestarReserva = $this->db->prepare($queryRestarReserva);
	        $stmtRestarReserva->execute();

	        // 2. Agrega la cantidad al inventario principal
	        $querySumarInventario = "INSERT INTO inventario_movimientos (repuesto_id, bodega_id, tipo, cantidad) 
	                                VALUES ('$repuestoId', '$bodegaId', 'entrada', '$cantidad')";
	        $stmtSumarInventario = $this->db->prepare($querySumarInventario);
	        $stmtSumarInventario->execute();

	        // Si todo fue exitoso, puedes realizar cualquier otra acción necesaria aquí
	        return true;
	    } catch (PDOException $e) {
	        // Manejar cualquier error aquí, como registrar un error o devolver un mensaje de error
	        return false;
	    }
	}

}