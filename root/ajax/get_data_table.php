<?php
// Conexión a la base de datos y otras configuraciones
session_start();
require_once '../../secure/trun.php';

$method = $_REQUEST['method'];
// Obtener los parámetros de DataTables
$start = $_REQUEST['start'];
$length = $_REQUEST['length'];
$search = $_REQUEST['search'];
$order = $_REQUEST['order'];

switch ($method) {
    case 'repuestos':

        $order_position = array("r.id", "r.nombre", "r.descripcion", "r.precio", "ubicacion_bodega", "codigos");
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY codigo DESC");
        $search_ql = ($search ? " WHERE r.nombre LIKE '%$search%' OR r.descripcion LIKE '%$search%'" : "");

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $repuestos = $db->query("SELECT r.*, b.nombre as ubicacion_bodega, (SELECT GROUP_CONCAT(codigo) FROM codigos_repuesto WHERE id_repuesto = r.id) AS codigos FROM repuestos AS r LEFT JOIN bodegas AS b ON r.ubicacion_bodega = b.id" . $search_ql . $order_ql . " LIMIT $start, $length")->fetch_assoc();

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query("SELECT COUNT(id) as total FROM repuestos");
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query("SELECT COUNT(r.id) as total FROM repuestos AS r LEFT JOIN bodegas AS b ON r.ubicacion_bodega = b.id". $search_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }


        // Formatear los datos para DataTables
        $data = array();
        foreach ($repuestos as $repuesto) {
            $data[] = array(
                "id" => $repuesto['id'],
                "nombre" => $repuesto['nombre'],
                "descripcion" => $repuesto['descripcion'],
                "precio" => $repuesto['precio'],
                "ubicacion_bodega" => (string)$repuesto['ubicacion_bodega'],
                "codigos" => $repuesto['codigos'],
            );
        }

        // Crear el arreglo de respuesta
        $response = array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($totalRegistros),
            "recordsFiltered" => intval($totalFiltrados),
            "data" => $data
        );

        // Devolver los datos en formato JSON
        echo json_encode($response);
        break;
    case 'categorias':
        $order_position = array("id", "nombre");
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY id DESC");
        $search_ql = ($search ? " WHERE nombre LIKE '%$search%'" : "");

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $categorias = $db->query("SELECT * FROM categorias" . $search_ql . $order_ql . " LIMIT $start, $length")->fetch_assoc();

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query("SELECT COUNT(id) as total FROM categorias");
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query("SELECT COUNT(id) as total FROM categorias".$search_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }


        // Formatear los datos para DataTables
        $data = array();
        foreach ($categorias as $categoria) {
            $data[] = array(
                "id" => $categoria['id'],
                "nombre" => $categoria['nombre'],
            );
        }

        // Crear el arreglo de respuesta
        $response = array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($totalRegistros),
            "recordsFiltered" => intval($totalFiltrados),
            "data" => $data
        );

        // Devolver los datos en formato JSON
        echo json_encode($response);
        break;
    case 'proveedores':
        $order_position = array("id", "nombre", "correo", "nit");
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY id DESC");
        $search_ql = ($search ? " WHERE nombre LIKE '%$search%'" : "");

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $proveedores = $db->query("SELECT * FROM proveedores" . $search_ql . $order_ql . " LIMIT $start, $length")->fetch_assoc();

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query("SELECT COUNT(id) as total FROM proveedores");
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query("SELECT COUNT(id) as total FROM proveedores".$search_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }


        // Formatear los datos para DataTables
        $data = array();
        foreach ($proveedores as $proveedor) {
            $data[] = array(
                "id" => $proveedor['id'],
                "nombre" => $proveedor['nombre'],
                "correo" => $proveedor['email'],
                "nit" => $proveedor['nit'],
            );
        }

        // Crear el arreglo de respuesta
        $response = array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($totalRegistros),
            "recordsFiltered" => intval($totalFiltrados),
            "data" => $data
        );

        // Devolver los datos en formato JSON
        echo json_encode($response);
        break;
    case 'clientes':
        $order_position = array("id", "nombre", "direccion", "nit", "email");
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY id DESC");
        $search_ql = ($search ? " WHERE nombre LIKE '%$search%'" : "");

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $clientes = $db->query("SELECT * FROM clientes" . $search_ql . $order_ql . " LIMIT $start, $length")->fetch_assoc();

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query("SELECT COUNT(id) as total FROM clientes");
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query("SELECT COUNT(id) as total FROM clientes".$search_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }


        // Formatear los datos para DataTables
        $data = array();
        foreach ($clientes as $cliente) {
            $data[] = array(
                "id" => $cliente['id'],
                "nombre" => $cliente['nombre'],
                "direccion" => $cliente['direccion'],
                "nit" => $cliente['nit'],
                "email" => $cliente['email'],
            );
        }

        // Crear el arreglo de respuesta
        $response = array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($totalRegistros),
            "recordsFiltered" => intval($totalFiltrados),
            "data" => $data
        );

        // Devolver los datos en formato JSON
        echo json_encode($response);
        break;
    case 'marcas_codigos':
        $order_position = array("id", "nombre");
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY id DESC");
        $search_ql = ($search ? " WHERE nombre LIKE '%$search%'" : "");

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $marcas_codigos = $db->query("SELECT * FROM marcas_codigos" . $search_ql . $order_ql . " LIMIT $start, $length")->fetch_assoc();

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query("SELECT COUNT(id) as total FROM marcas_codigos");
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query("SELECT COUNT(id) as total FROM marcas_codigos".$search_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }


        // Formatear los datos para DataTables
        $data = array();
        foreach ($marcas_codigos as $marca_codigo) {
            $data[] = array(
                "id" => $marca_codigo['id'],
                "nombre" => $marca_codigo['nombre'],
            );
        }

        // Crear el arreglo de respuesta
        $response = array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($totalRegistros),
            "recordsFiltered" => intval($totalFiltrados),
            "data" => $data
        );

        // Devolver los datos en formato JSON
        echo json_encode($response);
        break;
    default:
        // code...
        break;
}
?>