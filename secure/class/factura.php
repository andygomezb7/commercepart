<?php
/*
*
* @title FacturaManager
* @description "Clase para control de facturas"
* @author Andy Gomez
* @project "AlphaParts"
*
*/

class FacturaManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function certificarFactura($facturaId) {
        // Lógica para conectarse al certificador y obtener los datos
        // ...

        // Actualizar la factura con los datos obtenidos
        $sql = "UPDATE facturas SET serie_number = :serie, invoice_number = :invoice, autorizacion_number = :autorizacion, certificate_date = :fecha WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':serie', $serie);
        $stmt->bindParam(':invoice', $invoice);
        $stmt->bindParam(':autorizacion', $autorizacion);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':id', $facturaId);
        $stmt->execute();
    }

    public function crearFactura($invUser, $invObj, $invType, $conciled, $invName, $invAddress, $invNit, $certificar = true) {
        // Lógica para crear la factura en la base de datos
        $sql = "INSERT INTO facturas (inv_user, inv_obj, inv_type, date_creation, conciled, inv_name, inv_address, inv_nit) VALUES (:invUser, :invObj, :invType, NOW(), :conciled, :invName, :invAddress, :invNit)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':invUser', $invUser);
        $stmt->bindParam(':invObj', $invObj);
        $stmt->bindParam(':invType', $invType);
        $stmt->bindParam(':conciled', $conciled);
        $stmt->bindParam(':invName', $invName);
        $stmt->bindParam(':invAddress', $invAddress);
        $stmt->bindParam(':invNit', $invNit);
        $stmt->execute();

        $facturaId = $this->db->lastInsertId();

        if ($certificar) {
            $this->certificarFactura($facturaId);
        }

        return $facturaId;
    }

    public function anularFactura($facturaId, $descripcion) {
        $sql = "UPDATE facturas SET anulada = 1, anulada_desc = :descripcion WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':id', $facturaId);
        $stmt->execute();
    }

    public function conciliarFactura($facturaId) {
        $sql = "UPDATE facturas SET conciled = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $facturaId);
        $stmt->execute();
    }

    public function noConciliarFactura($facturaId) {
        $sql = "UPDATE facturas SET conciled = 2 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $facturaId);
        $stmt->execute();
    }

    // Otras funciones de búsqueda, listado, etc.
}
