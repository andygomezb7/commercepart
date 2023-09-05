<?php
class Empresas {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Agregar una nueva empresa
    public function agregarEmpresa($nombre, $direccion, $nit, $telefono, $email) {
        // Utiliza consultas preparadas para evitar la inyección de SQL
        $stmt = $this->db->prepare("INSERT INTO empresas (nombre, direccion, nit, telefono, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $direccion, $nit, $telefono, $email);

        if ($stmt->execute()) {
            return true; // La empresa se agrega correctamente
        } else {
            return false; // Error al agregar la empresa
        }
    }

    // Editar una empresa existente
    public function editarEmpresa($id, $nombre, $direccion, $nit, $telefono, $email) {
        // Utiliza consultas preparadas para evitar la inyección de SQL
        $stmt = $this->db->prepare("UPDATE empresas SET nombre = ?, direccion = ?, nit = ?, telefono = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $nombre, $direccion, $nit, $telefono, $email, $id);

        if ($stmt->execute()) {
            return true; // La empresa se edita correctamente
        } else {
            return false; // Error al editar la empresa
        }
    }

    // Eliminar una empresa existente por su ID
    public function eliminarEmpresa($id) {
        // Utiliza consultas preparadas para evitar la inyección de SQL
        $stmt = $this->db->prepare("DELETE FROM empresas WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return true; // La empresa se elimina correctamente
        } else {
            return false; // Error al eliminar la empresa
        }
    }

    // Obtener información de una empresa por su ID
    public function obtenerEmpresaPorID($id) {
        // Utiliza consultas preparadas para evitar la inyección de SQL
        $stmt = $this->db->prepare("SELECT * FROM empresas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            return $result->fetch_assoc(); // Retorna un arreglo con los datos de la empresa
        } else {
            return null; // No se encontró la empresa
        }
    }
}
?>
