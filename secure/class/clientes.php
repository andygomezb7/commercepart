<?php
class Clientes {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function agregarCliente($nombre, $direccion, $nit, $email) {
        $query = "INSERT INTO clientes (nombre, direccion, nit, email) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssss', $nombre, $direccion, $nit, $email);
        return $stmt->execute();
    }

    public function editarCliente($id, $nombre, $direccion, $nit, $email) {
        $query = "UPDATE clientes SET nombre = ?, direccion = ?, nit = ?, email = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssssi', $nombre, $direccion, $nit, $email, $id);
        return $stmt->execute();
    }

    public function eliminarCliente($id) {
        $query = "DELETE FROM clientes WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function obtenerClientePorID($id) {
        $query = "SELECT * FROM clientes WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    // Aquí podrían agregar más métodos para obtener información de los clientes, realizar búsquedas, etc.
}
?>
