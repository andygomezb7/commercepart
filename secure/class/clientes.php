<?php
class Clientes {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function agregarCliente($nombre, $direccion, $nit, $email) {
        $query = "INSERT INTO clientes (nombre, direccion, nit, email,empresa_id) VALUES ('$nombre', '$direccion', '$nit', '$email', '".$_SESSION['empresa_id']."')";
        $stmt = $this->db->query($query);
        return $stmt;
    }

    public function editarCliente($id, $nombre, $direccion, $nit, $email) {
        $query = "UPDATE clientes SET nombre = '$nombre', direccion = '$direccion', nit = '$nit', email = '$email' WHERE id = '$id' AND empresa_id = " . $_SESSION['empresa_id'];
        $stmt = $this->db->query($query);
        return $stmt;
    }

    public function eliminarCliente($id) {
        $query = "DELETE FROM clientes WHERE id = $id";
        $stmt = $this->db->query($query);
        return $stmt;
    }

    public function obtenerClientePorID($id) {
        $query = "SELECT * FROM clientes WHERE id = $id";
        $stmt = $this->db->query($query);
        $result = $stmt->fetch_assoc();
        return $result;
    }

    // Aquí podrían agregar más métodos para obtener información de los clientes, realizar búsquedas, etc.
}
?>
