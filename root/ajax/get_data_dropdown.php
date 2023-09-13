<?php
// Conexión a la base de datos y otras configuraciones
session_start();
require_once '../../secure/trun.php';
require_once '../../secure/class/inventario.php';
$inventario = new Inventario($db);

$method = $_REQUEST['method'];

switch ($method) {
    case 'repuestos':
            $search = @$_REQUEST['search'];
            $offset = isset($_REQUEST['offset']) ? intval($_REQUEST['offset']) : 1;

            // Calcular la cantidad de elementos por página
            $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 10;
            $offset = ($offset - 1) * $limit;

            // Modifica la consulta SQL para incluir la búsqueda en el nombre o la descripción del repuesto
            $where = (@$search ? "WHERE (nombre LIKE '%$search%' OR descripcion LIKE '%$search%' OR (r.id IN (SELECT id_repuesto FROM codigos_repuesto WHERE codigo LIKE '%$search%'))) AND r.empresa_id = " . $_SESSION['empresa_id'] : ' WHERE r.empresa_id = ' . $_SESSION['empresa_id']);

            $sql = "SELECT r.id, r.nombre, r.descripcion, pr.precio_sugerido AS precio, r.stock, cr.codigo, (SELECT GROUP_CONCAT(codigo) FROM codigos_repuesto WHERE id_repuesto = r.id) AS codigos FROM repuestos r JOIN codigos_repuesto cr ON r.id = cr.id_repuesto LEFT JOIN precios as pr ON r.id = pr.repuesto_id " . $where . " GROUP BY r.id ORDER BY codigos DESC";
            var_dump($sql. " LIMIT $offset, $limit");
            $result = $db->query($sql. " LIMIT $offset, $limit");
            //
            $resultTotal = $db->query("SELECT COUNT(r.id) as total, r.nombre, r.descripcion, r.precio, cr.codigo, (SELECT GROUP_CONCAT(codigo) FROM codigos_repuesto WHERE id_repuesto = r.id) AS codigos FROM repuestos r JOIN codigos_repuesto cr ON r.id = cr.id_repuesto " . $where . " GROUP BY r.id");
            $rowTotal = $resultTotal->fetch_assoc();
            $totalRegistros = $rowTotal['total'];

            $isMyBodega = $db->query("SELECT bodega_id FROM usuarios_bodegas WHERE usuario_id = '".$_SESSION['usuario_id']."' AND empresa_id = ". $_SESSION['empresa_id'])->fetch_assoc();

            $repuestos = array();

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    // Crea un objeto JSON con la información necesaria
                    $repuestosPor = [];
                    $resultadosRepuesto = $inventario->obtenerTotalRepuestosPorBodega(null, $row['id'], true);
                    if ($resultadosRepuesto) {
                        while($repuesto = $resultadosRepuesto->fetch_assoc()) {
                            $repuestosPor[] = array(
                                'bodegaid' => $repuesto['bodega_id'],
                                'bodeganame' => $repuesto['nombre_bodega'],
                                'cantidad' => $repuesto['inventario'],
                                'reserva' => $repuesto['reserva'],
                                'fecha_estimada' => $repuesto['fecha_estimada']
                            );
                        }
                    }

                    $repuesto = array(
                        "id" => $row["id"],
                        "nombre" => $row["nombre"],
                        "imagen" => 'https://picsum.photos/200',
                        "descripcion" => $row["descripcion"],
                        "codigos" => $row['codigos'],
                        'valor' => ($row['precio'] ? $row['precio'] : '0'),
                        'diponibilidad' => 0,
                        'bodegas' => $repuestosPor,
                        'myBodega' => $isMyBodega['bodega_id']
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