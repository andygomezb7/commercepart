<?php
/*
*
* @title Pedidos
* @description "Clase para control de pedidos"
* @author Andy Gomez
* @project "AlphaParts"
*
*/

require(__DIR__ . '/../trun.php');

class Pedidos {
	private $db;

	public function __construct($db) {
		$this->db = $db;
	}
	function ingresarPedido($id_usuario, $id_empleado, $cliente, $dias_credito, $id_transportista, $fecha_send, $estado, $detalles) {
		include ("inventario.php");
		$inventario = new Inventario($this->db);

	    // Insertar en la tabla pedidos
	    $fecha = date("Y-m-d"); // Obtener la fecha actual
	    $sql = "INSERT INTO pedidos (fecha, estado, id_usuario, id_empleado, cliente_nit, cliente_nombre, cliente_direccion, cliente_correo, cliente_obs, dias_credito, id_transportista, creation_date)
	            VALUES ('$fecha_send', $estado, $id_usuario, $id_empleado, '".$cliente['nit']."', '".$cliente['nombre']."', '".$cliente['direccion']."', '".$cliente['correo']."', '".$cliente['obs']."', $dias_credito, $id_transportista, '$fecha')";

	    if ($this->db->query($sql) === TRUE) {
	        $id_pedido = $this->db->insert_id;

	        // Insertar en la tabla pedido_detalles
	        foreach ($detalles as $detalle) {
	            $id_repuesto = $detalle['id_repuesto'];
	            $cantidad = $detalle['cantidad'];
	            $reserva = $detalle['reserva'];
	            $precio_unitario = $detalle['precio_unitario'];

	            $sql = "INSERT INTO pedido_detalles (id_pedido, id_repuesto, cantidad, precio_unitario, reserva)
	                    VALUES ($id_pedido, $id_repuesto, $cantidad, $precio_unitario, $reserva)";
	            $this->db->query($sql);

	            $isMyBodega = $db->query("SELECT bodega_id FROM usuarios_bodegas WHERE usuario_id = '".$_SESSION['usuario_id']."' AND empresa_id = ". $_SESSION['empresa_id'])->fetch_assoc();

	            // vender de inventario normal
	            if ($estado==3 && ($cantidad > 0 || $reserva > 0)) {

	            	$repuestoId = $id_repuesto; // ID del repuesto vendido
					$bodegaId = $isMyBodega['bodega_id']; // ID de la bodega desde donde se vende
					$tipo = 'venta';
					$cantidad = $cantidad; // Cantidad de repuestos vendidos
					$pedidoId = $id_pedido; // ID del pedido relacionado (venta)
					$usuarioId = $_SESSION['usuario_id']; // ID del usuario que registra la venta
					$comentario = 'Venta de repuestos a cliente';

					if ($cantidad>0&&$inventario->obtenerTotalRepuestosPorBodega($isMyBodega['bodega_id'], $detalle['id_repuesto'])) {
						if ($inventario->insertarMovimientoInventario($repuestoId, $bodegaId, $tipo, $cantidad, null, $pedidoId, $usuarioId, $comentario)) {
						    // echo "Venta de repuestos registrada con éxito.";
						}
					}
					if ($reserva>0&&$inventario->obtenerTotalRepuestosPorBodega($isMyBodega['bodega_id'], $detalle['id_repuesto'], true)) {
						$venderDesdeReserva = $inventario->insertarVentaDesdeReserva($repuestoId, $bodegaId, $reserva, $usuarioId, $pedidoId);
						if ($venderDesdeReserva) {
							// echo "Venta desde reserva registrada con exito, llegara el: " . $venderDesdeReserva;
						}
					}
	            	//
	            }
	            //
	        }

	        return true; // Pedido ingresado exitosamente
	    } else {
	        return false; // Error al ingresar el pedido
	    }
	}

	// Función para modificar un pedido existente
	function modificarPedido($id_pedido, $id_usuario, $id_empleado, $cliente, $dias_credito, $id_transportista, $detalles = null) {

	    // Actualizar la tabla pedidos
	    $sql = "UPDATE pedidos SET id_usuario = '$id_usuario',
			    id_empleado = '$id_empleado',
	            dias_credito = '$dias_credito', 
	            id_transportista = '$id_transportista',
	            cliente_nit = '".$cliente['nit']."',
			    cliente_nombre = '".$cliente['nombre']."',
			    cliente_direccion = '".$cliente['direccion']."',
			    cliente_correo = '".$cliente['correo']."',
			    cliente_obs = '".$cliente['obs']."'
	            WHERE id = $id_pedido";

	    if ($this->db->query($sql) === TRUE) {
	        if ($detalles !== null) {
	            // Eliminar los detalles del pedido anterior
	            $sql = "DELETE FROM pedido_detalles WHERE id_pedido = $id_pedido";
	            $this->db->query($sql);

	            // Insertar los nuevos detalles
	            foreach ($detalles as $detalle) {
	                $id_repuesto = $detalle['id_repuesto'];
	                $cantidad = $detalle['cantidad'];
	                $reserva = $detalle['reserva'];
	                $precio_unitario = $detalle['precio_unitario'];

	                $sql = "INSERT INTO pedido_detalles (id_pedido, id_repuesto, cantidad, precio_unitario, reserva)
	                        VALUES ($id_pedido, $id_repuesto, $cantidad, $precio_unitario, $reserva)";
	                $this->db->query($sql);
	            }
	        }

	        return true; // Pedido modificado exitosamente
	    } else {
	        return false; // Error al modificar el pedido
	    }
	}

	public function obtenerRepuestosDePedido($pedidoId) {
        try {
            $sql = "SELECT a.precio_unitario AS precio, a.cantidad, a.reserva, r.id, r.nombre, r.descripcion FROM pedido_detalles AS a LEFT JOIN repuestos AS r ON a.id_repuesto = r.id WHERE a.id_pedido = " . $pedidoId;
            
            // Ejecutar la consulta
            $result = $this->db->query($sql);
            
            $repuestos = array();
            
            // Recorrer los resultados y almacenarlos en un array
            while ($row = $result->fetch_assoc()) {
                $repuestos[] = $row;
            }

            return $repuestos;
        } catch (Exception $e) {
            // Manejar el error aquí, por ejemplo, registrándolo o lanzando una excepción personalizada
            return array(); // Devuelve un array vacío en caso de error
        }
    }
}