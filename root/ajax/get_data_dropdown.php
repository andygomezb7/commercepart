<?php
// Conexión a la base de datos y otras configuraciones
session_start();
require_once '../../secure/trun.php';

$method = $_REQUEST['method'];

switch ($method) {
    case 'repuestos':
            $search = @$_REQUEST['search'];
            $offset = isset($_REQUEST['offset']) ? intval($_REQUEST['offset']) : 1;

            // Calcular la cantidad de elementos por página
            $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 10;
            $offset = ($offset - 1) * $limit;

            // Modifica la consulta SQL para incluir la búsqueda en el nombre o la descripción del repuesto
            $where = (@$search ? "WHERE nombre LIKE '%$search%' OR descripcion LIKE '%$search%' OR (r.id IN (SELECT id_repuesto FROM codigos_repuesto WHERE codigo LIKE '%$search%'))" : '');
            $sql = "SELECT r.id, r.nombre, r.descripcion, r.precio, cr.codigo, (SELECT GROUP_CONCAT(codigo) FROM codigos_repuesto WHERE id_repuesto = r.id) AS codigos FROM repuestos r JOIN codigos_repuesto cr ON r.id = cr.id_repuesto " . $where . " ORDER BY codigos DESC";
            // var_dump($sql. " LIMIT $offset, $limit");
            $result = $db->query($sql. " LIMIT $offset, $limit")->fetch_all(MYSQLI_ASSOC);;

            //
            $resultTotal = $db->query("SELECT COUNT(r.id) as total, r.nombre, r.descripcion, r.precio, cr.codigo, (SELECT GROUP_CONCAT(codigo) FROM codigos_repuesto WHERE id_repuesto = r.id) AS codigos FROM repuestos r JOIN codigos_repuesto cr ON r.id = cr.id_repuesto " . $where);
            $rowTotal = $resultTotal->fetch_assoc();
            $totalRegistros = $rowTotal['total'];

            $repuestos = array();

            if (is_array($result)) {
                foreach ($result as $row) {
                    // Crea un objeto JSON con la información necesaria
                    $repuesto = array(
                        "id" => $row["id"],
                        "nombre" => $row["nombre"],
                        "imagen" => 'https://picsum.photos/200',
                        "descripcion" => $row["descripcion"],
                        "codigos" => $row['codigos'],
                        'valor' => $row['precio']
                    );
                    
                    // Agrega el objeto JSON al arreglo de repuestos
                    $repuestos[] = $repuesto;
                }
            }

            // Envía los datos como un arreglo de objetos JSON
            echo json_encode(array(
                "offset" => $offset,
                "repuestos" => $repuestos,
                'total' => $totalRegistros
            ));
        break;
    default:
        // code...
        break;
}
?>