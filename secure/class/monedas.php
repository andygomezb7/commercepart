<?php

class Monedas {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function agregarMoneda($nombre, $tipo_cambio) {
        // Preparar la consulta SQL para agregar una moneda
        $query = "INSERT INTO monedas (nombre, tipo_cambio, empresa_id) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sdd", $nombre, $tipo_cambio, $_SESSION['empresa_id']);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return true; // Moneda agregada con éxito
        } else {
            return false; // Error al agregar la moneda
        }
    }

    public function editarMoneda($id, $nombre, $tipo_cambio) {
        // Preparar la consulta SQL para editar una moneda
        $query = "UPDATE monedas SET nombre = ?, tipo_cambio = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sdi", $nombre, $tipo_cambio, $id);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return true; // Moneda editada con éxito
        } else {
            return false; // Error al editar la moneda
        }
    }

    public function eliminarMoneda($id) {
        // Preparar la consulta SQL para eliminar una moneda
        $query = "DELETE FROM monedas WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return true; // Moneda eliminada con éxito
        } else {
            return false; // Error al eliminar la moneda
        }
    }

    public function obtenerMonedaPorID($id) {
        // Preparar la consulta SQL para obtener una moneda por su ID
        $query = "SELECT id, nombre, tipo_cambio FROM monedas WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);

        // Ejecutar la consulta
        $stmt->execute();
        $result = $stmt->get_result();

        // Obtener los datos de la moneda
        $moneda = $result->fetch_assoc();

        return $moneda;
    }

    // Otras funciones que puedas necesitar

}
