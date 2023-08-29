<?php
class Proveedores {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function agregarProveedor($nombre, $direccion, $nit, $telefono, $email) {
        $query = "INSERT INTO proveedores (nombre, direccion, nit, telefono, email) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssss', $nombre, $direccion, $nit, $telefono, $email);
        
        return $stmt->execute();
    }

    public function editarProveedor($id, $nombre, $direccion, $nit, $telefono, $email) {
        $query = "UPDATE proveedores SET nombre = ?, direccion = ?, nit = ?, telefono = ?, email = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssssi', $nombre, $direccion, $nit, $telefono, $email, $id);

        return $stmt->execute();
    }

    public function eliminarProveedor($id) {
        $query = "DELETE FROM proveedores WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);

        return $stmt->execute();
    }

    public function obtenerProveedorPorID($id) {
        $query = "SELECT * FROM proveedores WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // Otros métodos necesarios según la lógica de tu aplicación
}
?>
