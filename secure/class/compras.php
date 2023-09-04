<?php

class ComprasManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function obtenerCompraPorId($compraId) {
        try {
            $query = "SELECT c.*, cl.nit, cl.email FROM compras AS c LEFT JOIN clientes AS cl ON c.cliente_id = cl.id WHERE c.id = ".$compraId;
            $stmt = $this->db->prepare($query);
            $stmt->execute();

           // Obtener el resultado de la consulta
            $result = $stmt->get_result();

            // Verifica si se encontró la compra
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                return null; // No se encontró la compra
            }
        } catch (PDOException $e) {
            // Manejar el error aquí, por ejemplo, registrándolo o lanzando una excepción personalizada
            return null;
        }
    }

    public function obtenerRepuestosDeCompra($compraId) {
        try {
            $sql = "SELECT a.precio AS precio, a.cantidad, r.id, r.nombre FROM compras_articulos AS a LEFT JOIN repuestos AS r ON a.repuesto_id = r.id WHERE a.compra_id = " . $compraId;
            
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

    public function agregarCompra($compraData, $repuestos) {
        include('inventario.php');
        $inventario = new Inventario($this->db);
        try {
            // Insertar los datos de la compra en la tabla 'compras'
            $query = "INSERT INTO compras (cliente_id, nombre, vendedor_id, fecha_documento, fecha_ofrecido, tipo_cambio, niveles_precio,bodega,correlativo,proveedor,tipo_precio,autorizacion,descripcion,moneda,flete,seguro,estado)
                      VALUES ('".$compraData['cliente_id']."', '".$compraData['nombre']."', '".$compraData['vendedor_id']."', '".$compraData['fecha_documento']."', '".$compraData['fecha_ofrecido']."', '".$compraData['tipo_cambio']."', '".$compraData['niveles_precio']."', '".$compraData['bodega']."','".$compraData['correlativo']."','".$compraData['proveedor']."','".$compraData['tipo_precio']."','".$compraData['autorizacion']."','".$compraData['descripcion']."','".$compraData['moneda']."','".$compraData['flete']."','".$compraData['seguro']."','".$compraData['estado']."')";
            $stmt = $this->db->prepare($query);
            if ($stmt) {
                $stmt->execute();
            } else {
                echo 'Error en la transacción: '. $this->db->error;
            }

            // Obtener el ID de la compra recién insertada
            $compraId = $this->db->insert_id;

            // Insertar los artículos de compra en la tabla 'compras_articulos'
            foreach ($repuestos as $repuesto) {
                $query = "INSERT INTO compras_articulos (repuesto_id, precio, cantidad, compra_id, fecha_modificacion)
                          VALUES ('".$repuesto['repuesto_id']."', '".$repuesto['costo']."', '".$repuesto['cantidad']."', '".$compraId."', NOW())";
                $stmt = $this->db->prepare($query);
                $stmt->execute();

                if ($compraData['estado'] == 3 || $compraData['estado'] == 2) {
                    $repuestoId = $repuesto['repuesto_id']; // ID del repuesto comprado
                    $bodegaId = $compraData['bodega']; // ID de la bodega donde se almacenará
                    $reserva = ($compraData['estado'] == 2 ? true : false);
                    $tipo = ($reserva?'entrada':'compra');
                    $cantidad = $repuesto['cantidad']; // Cantidad de repuestos comprados
                    $compraId = $compraId; // ID de la compra relacionada
                    $usuarioId = $_SESSION['usuario_id']; // ID del usuario que registra la compra
                    $comentario = 'Compra de repuestos para agregar el inventario';

                    if ($inventario->insertarMovimientoInventario($repuestoId, $bodegaId, $tipo, $cantidad, $compraId, null, $usuarioId, $comentario, $reserva, $compraData['fecha_ofrecido'])) {
                        // echo "Compra de repuestos registrada con éxito.";
                    }
                }
            }

            // Confirmar la transacción
            $this->db->commit();

            return $compraId;
        } catch (PDOException $e) {
            // Revertir la transacción en caso de error
            $this->db->rollback();
            echo "Error: " . $e->getMessage(); // Imprimir el mensaje de error
            return false;
        }
    }

    public function editarCompra($compraId, $compraData, $repuestos) {
        include('inventario.php');
        $inventario = new Inventario($this->db);
        try {
            // Iniciar una transacción
            $this->db->begin_transaction();


            $beforeInfo = $this->db->query("SELECT estado FROM compras WHERE id=".$compraId)->fetch_assoc();
            // Actualizar los datos principales de la compra (cliente, vendedor, fechas, etc.)
            $queryCompra = "UPDATE compras SET 
                            vendedor_id = '".$compraData['vendedor_id']."',
                            fecha_documento = '".$compraData['fecha_documento']."',
                            fecha_ofrecido = '".$compraData['fecha_ofrecido']."',
                            tipo_cambio = '".$compraData['tipo_cambio']."',
                            niveles_precio = '".$compraData['niveles_precio']."',
                            bodega = '".$compraData['bodega']."',

                            correlativo = '".$compraData['correlativo']."',
                            proveedor = '".$compraData['proveedor']."',
                            tipo_precio = '".$compraData['tipoprecio']."',
                            autorizacion = '".$compraData['autorizacion']."',
                            descripcion = '".$compraData['descripcion']."',
                            moneda = '".$compraData['moneda']."',
                            flete = '".$compraData['flete']."',
                            seguro = '".$compraData['seguro']."',
                            estado = '".$compraData['estado']."' WHERE id = '".$compraId."'";
            // var_dump($queryCompra);die;
            $stmtCompra = $this->db->prepare($queryCompra);
            $stmtCompra->execute();

            // Eliminar los detalles de repuestos existentes para esta compra
            $queryDeleteRepuestos = "DELETE FROM compras_articulos WHERE compra_id = " . $compraId;
            $stmtDeleteRepuestos = $this->db->prepare($queryDeleteRepuestos);
            $stmtDeleteRepuestos->execute();

            // Insertar los nuevos detalles de repuestos

            // Iterar a través de los repuestos y agregarlos a la compra
            foreach ($repuestos as $repuesto) {
                $queryInsertRepuestos = "INSERT INTO compras_articulos (compra_id, repuesto_id, precio, cantidad) VALUES ('".$compraId."', '".$repuesto['repuesto_id']."', '".$repuesto['costo']."', '".$repuesto['cantidad']."')";
                $stmtInsertRepuestos = $this->db->prepare($queryInsertRepuestos);
                $stmtInsertRepuestos->execute();

                if ($compraData['estado']==3&&$beforeInfo['estado']!=2||$compraData['estado']==2) {
                    $repuestoId = $repuesto['repuesto_id']; // ID del repuesto comprado
                    $bodegaId = $compraData['bodega']; // ID de la bodega donde se almacenará
                    $reserva = ($compraData['estado'] == 2 ? true : false);
                    $tipo = ($reserva?'entrada':'compra');
                    $cantidad = $repuesto['cantidad']; // Cantidad de repuestos comprados
                    $compraId = $compraId; // ID de la compra relacionada
                    $usuarioId = $_SESSION['usuario_id']; // ID del usuario que registra la compra
                    $comentario = 'Compra de repuestos para agregar el inventario';

                    $insertMovimiento = $inventario->insertarMovimientoInventario($repuestoId, $bodegaId, $tipo, $cantidad, $compraId, 0, $usuarioId, $comentario, $reserva, $compraData['fecha_ofrecido']);
                    if ($insertMovimiento) {
                        // return $insertMovimiento;
                    } else {
                        // return 'no sirvio1';
                    }
                } else if ($compraData['estado']==3&&$beforeInfo['estado']==2) {
                    $inventario->moverInventarioReservaAlInventarioPrincipal($repuesto['repuesto_id'], $compraData['bodega'], $repuesto['cantidad']);
                }
            }

            // Confirmar la transacción
            $this->db->commit();

            return true; // La compra se editó con éxito
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $this->db->rollback();
            return 'no sirvio';
            // Manejar el error aquí, por ejemplo, registrándolo o lanzando una excepción personalizada
            return false; // Error al editar la compra
        }
    }

    public function obtenerVendedores() {
        // Consulta SQL para obtener los vendedores (supongamos que el tipo de vendedor es 3)
        $sql = "SELECT id, nombre FROM usuarios WHERE tipo = 3";
        
        // Ejecutar la consulta
        $result = $this->db->query($sql);
        
        $vendedores = array();
        
        // Recorrer los resultados y almacenarlos en un array
        while ($row = $result->fetch_assoc()) {
            $vendedores[] = $row;
        }
        
        return $vendedores;
    }

    public function obtenerClientes() {
        // Consulta SQL para obtener los clientes
        $sql = "SELECT id, nombre FROM clientes";
        
        // Ejecutar la consulta
        $result = $this->db->query($sql);
        
        $clientes = array();
        
        // Recorrer los resultados y almacenarlos en un array
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
        
        return $clientes;
    }



}
