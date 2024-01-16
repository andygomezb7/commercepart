<?php
class MarcasCodigos
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function agregarMarcaCodigo($nombre)
    {
        $query = "INSERT INTO marcas_codigos (nombre, empresa_id) VALUES ('".$nombre."', '".$_SESSION['empresa_id']."')";
        $stmt = $this->db->prepare($query);

        return $stmt->execute();
    }

    public function editarMarcaCodigo($id, $nombre)
    {
        $query = "UPDATE marcas_codigos SET nombre = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $nombre, $id);

        return $stmt->execute();
    }

    public function eliminarMarcaCodigo($id)
    {
        $query = "DELETE FROM marcas_codigos WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);

        return $stmt->execute();
    }

    public function obtenerMarcaCodigoPorID($id)
    {
        $query = "SELECT * FROM marcas_codigos WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return $result;
    }
}
?>
