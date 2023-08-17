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
	function ingresarPedido($id_usuario, $id_empleado, $cliente, $dias_credito, $id_transportista, $fecha_send, $detalles) {

	    // Insertar en la tabla pedidos
	    $fecha = date("Y-m-d"); // Obtener la fecha actual
	    $estado = 1; // Estado inicial del pedido
	    $sql = "INSERT INTO pedidos (fecha, estado, id_usuario, id_empleado, cliente_nit, cliente_nombre, cliente_direccion, cliente_correo, cliente_obs, dias_credito, id_transportista, creation_date)
	            VALUES ('$fecha_send', $estado, $id_usuario, $id_empleado, '".$cliente['nit']."', '".$cliente['nombre']."', '".$cliente['direccion']."', '".$cliente['correo']."', '".$cliente['obs']."', $dias_credito, $id_transportista, '$fecha')";

	    if ($this->db->query($sql) === TRUE) {
	        $id_pedido = $this->db->insert_id;

	        // Insertar en la tabla pedido_detalles
	        foreach ($detalles as $detalle) {
	            $id_repuesto = $detalle['id_repuesto'];
	            $cantidad = $detalle['cantidad'];
	            $precio_unitario = $detalle['precio_unitario'];

	            $sql = "INSERT INTO pedido_detalles (id_pedido, id_repuesto, cantidad, precio_unitario)
	                    VALUES ($id_pedido, $id_repuesto, $cantidad, $precio_unitario)";
	            $this->db->query($sql);
	        }

	        return true; // Pedido ingresado exitosamente
	    } else {
	        return false; // Error al ingresar el pedido
	    }
	}

	// Función para modificar un pedido existente
	function modificarPedido($id_pedido, $id_usuario, $id_empleado, $cliente, $dias_credito, $id_transportista, $detalles = null) {

	    // Actualizar la tabla pedidos
	    $sql = "UPDATE pedidos SET id_usuario = $id_usuario
	    id_empleado = $id_empleado,
	            id_cliente = $id_cliente, 
	            dias_credito = $dias_credito, 
	            id_transportista = $id_transportista,
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
	                $precio_unitario = $detalle['precio_unitario'];

	                $sql = "INSERT INTO pedido_detalles (id_pedido, id_repuesto, cantidad, precio_unitario)
	                        VALUES ($id_pedido, $id_repuesto, $cantidad, $precio_unitario)";
	                $this->db->query($sql);
	            }
	        }

	        return true; // Pedido modificado exitosamente
	    } else {
	        return false; // Error al modificar el pedido
	    }
	}
}