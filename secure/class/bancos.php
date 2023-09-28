<?php
class Bancos {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function obtenerTodosLosBancos() {
        $query = "SELECT * FROM bancos";
        $result = $this->db->query($query);

        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    public function obtenerBancoPorID($id) {
        $query = "SELECT * FROM bancos WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }

    public function agregarBanco($nombre, $direccion, $telefono) {
        $query = "INSERT INTO bancos (nombre, direccion, telefono) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $nombre, $direccion, $telefono);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function editarBanco($id, $nombre, $direccion, $telefono) {
        $query = "UPDATE bancos SET nombre = ?, direccion = ?, telefono = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssi", $nombre, $direccion, $telefono, $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function eliminarBanco($id) {
        $query = "DELETE FROM bancos WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>
