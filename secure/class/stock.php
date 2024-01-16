<?php
/*
*
* @title StockManager
* @description "Clase para control de stock en repuestos"
* @author Andy Gomez
* @project "AlphaParts"
*
*/

class StockManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function crearFacturaStock($serie, $invoice, $autorizacion, $certificate, $invName, $invAddress, $invNit, $repuestos) {
        // Crear la factura en la tabla de facturas
        $invType = 3; // Tipo de inventario para stock
        $conciled = 2; // No conciliada
        $sql = "INSERT INTO facturas (inv_user, inv_obj, inv_type, serie_number, invoice_number, autorizacion_number, certificate_date, inv_name, inv_address, inv_nit, conciled, date_creation) VALUES (:invUser, :invObj, :invType, :serie, :invoice, :autorizacion, :certificate, :invName, :invAddress, :invNit, :conciled, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':invUser', $_SESSION['user_id']); // Supongo que tienes una sesión de usuario
        $stmt->bindParam(':invObj', $repuestos[0]['id_repuesto']); // Id de stock_repuestos
        $stmt->bindParam(':invType', $invType);
        $stmt->bindParam(':serie', $serie);
        $stmt->bindParam(':invoice', $invoice);
        $stmt->bindParam(':autorizacion', $autorizacion);
        $stmt->bindParam(':certificate', $certificate);
        $stmt->bindParam(':invName', $invName);
        $stmt->bindParam(':invAddress', $invAddress);
        $stmt->bindParam(':invNit', $invNit);
        $stmt->bindParam(':conciled', $conciled);
        $stmt->execute();

        $facturaId = $this->db->lastInsertId();

        // Insertar repuestos en la tabla stock_repuestos
        foreach ($repuestos as $repuesto) {
            $this->agregarStockRepuesto($facturaId, $repuesto['id_repuesto'], $repuesto['cantidad']);
        }

        return $facturaId;
    }

    public function agregarStockRepuesto($facturaId, $idRepuesto, $cantidad) {
        $idEmpresa = $_SESSION['user_empresa_id']; // Supongo que tienes una sesión de usuario con empresa
        $idProveedor = null; // Si tienes un proveedor asociado, agrega aquí su id

        $estado = 2; // Estado por defecto al crear el stock

        // Insertar en la tabla stock_repuestos
        $sql = "INSERT INTO stock_repuestos (id_repuesto, id_empresa, id_proveedor, cantidad, estado) VALUES (:idRepuesto, :idEmpresa, :idProveedor, :cantidad, :estado)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idRepuesto', $idRepuesto);
        $stmt->bindParam(':idEmpresa', $idEmpresa);
        $stmt->bindParam(':idProveedor', $idProveedor);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();

        // Actualizar el stock en la tabla repuestos
        $sqlUpdateStock = "UPDATE repuestos SET stock = COALESCE(stock, 0) + :cantidad WHERE id = :idRepuesto";
        $stmtUpdateStock = $this->db->prepare($sqlUpdateStock);
        $stmtUpdateStock->bindParam(':idRepuesto', $idRepuesto);
        $stmtUpdateStock->bindParam(':cantidad', $cantidad);
        $stmtUpdateStock->execute();
    }

    public function cambiarEstadoStockRepuesto($idStockRepuesto, $nuevoEstado) {
        // Cambiar el estado del stock_repuestos
        $sql = "UPDATE stock_repuestos SET estado = :nuevoEstado WHERE id = :idStockRepuesto";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idStockRepuesto', $idStockRepuesto);
        $stmt->bindParam(':nuevoEstado', $nuevoEstado);
        $stmt->execute();
    }

    public function regenerarStock($idRepuesto) {
        // Obtener la cantidad total de stock_repuestos con estado=1
        $sqlTotalStock = "SELECT SUM(cantidad) AS total_stock FROM stock_repuestos WHERE id_repuesto = :idRepuesto AND estado = 1";
        $stmtTotalStock = $this->db->prepare($sqlTotalStock);
        $stmtTotalStock->bindParam(':idRepuesto', $idRepuesto);
        $stmtTotalStock->execute();
        $totalStock = $stmtTotalStock->fetch(PDO::FETCH_ASSOC)['total_stock'];

        // Obtener la suma total de cantidad de pedido_detalles con estado=1
        $sqlTotalPedido = "SELECT SUM(pd.cantidad) AS total_pedido FROM pedido_detalles pd JOIN pedidos p ON pd.id_pedido = p.id WHERE pd.id_repuesto = :idRepuesto AND p.estado = 1";
        $stmtTotalPedido = $this->db->prepare($sqlTotalPedido);
        $stmtTotalPedido->bindParam(':idRepuesto', $idRepuesto);
        $stmtTotalPedido->execute();
        $totalPedido = $stmtTotalPedido->fetch(PDO::FETCH_ASSOC)['total_pedido'];

        // Calcular el stock regenerado
        $stockRegenerado = $totalStock - $totalPedido;

        // Actualizar el stock en la tabla repuestos
        $sqlUpdateStock = "UPDATE repuestos SET stock = :stockRegenerado WHERE id = :idRepuesto";
        $stmtUpdateStock = $this->db->prepare($sqlUpdateStock);
        $stmtUpdateStock->bindParam(':idRepuesto', $idRepuesto);
        $stmtUpdateStock->bindParam(':stockRegenerado', $stockRegenerado);
        $stmtUpdateStock->execute();

        return $stockRegenerado;
    }
    // Otras funciones...

}
