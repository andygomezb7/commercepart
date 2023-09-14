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

        $order_position = array("r.nombre", "r.descripcion", "r.precio", "total_stock", "ubicacion_bodega", "codigos");
        $bodegasfiltro = @$_REQUEST['bodegas'];
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY codigo DESC");
        $search_ql = ($search ? " WHERE r.nombre LIKE '%$search%' OR r.descripcion LIKE '%$search%' OR (EXISTS (SELECT 1 FROM codigos_repuesto WHERE id_repuesto = r.id AND codigo = '$search'))" : "");
        $bodegasfiltro = (intval($bodegasfiltro) ? ($search_ql?' AND ':' WHERE ')."movimientos.bodega_id = '$bodegasfiltro' AND r.empresa_id = ". $_SESSION['empresa_id'] : ($search_ql?' AND ':' WHERE ')."r.empresa_id=" . $_SESSION['empresa_id']);

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $sql = "SELECT r.*, b.nombre as ubicacion_bodega, (SELECT GROUP_CONCAT(codigo) FROM codigos_repuesto WHERE id_repuesto = r.id) AS codigos, SUM(coalesce(movimientos.cantidad, 0)) AS total_stock FROM repuestos AS r LEFT JOIN bodegas AS b ON r.ubicacion_bodega = b.id LEFT JOIN
            (
                SELECT
                    bodega_id,
                    repuesto_id,
                    SUM(cantidad) AS cantidad
                FROM
                    inventario_movimientos
                GROUP BY
                    repuesto_id
            ) AS movimientos ON r.id = movimientos.repuesto_id". $bodegasfiltro . $search_ql . ' GROUP BY r.id' . $order_ql . " LIMIT $start, $length";
        // var_dump($sql);
        $repuestos = $db->query($sql);

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query("SELECT COUNT(id) as total FROM repuestos");
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query("SELECT COUNT(r.id) as total FROM repuestos AS r LEFT JOIN bodegas AS b ON r.ubicacion_bodega = b.id LEFT JOIN
            (
                SELECT
                    bodega_id,
                    repuesto_id,
                    SUM(cantidad) AS cantidad
                FROM
                    inventario_movimientos
                GROUP BY
                    repuesto_id
            ) AS movimientos ON r.id = movimientos.repuesto_id". $bodegasfiltro .  $search_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }


        // Formatear los datos para DataTables
        $data = array();
        // if (is_array($repuestos)) {
            while ($repuesto = $repuestos->fetch_assoc()) {
                $data[] = array(
                    "id" => $repuesto['id'],
                    "nombre" => $repuesto['nombre'],
                    "descripcion" => $repuesto['descripcion'],
                    "precio" => $repuesto['precio'],
                    "ubicacion_bodega" => (string)$repuesto['ubicacion_bodega'],
                    "codigos" => $repuesto['codigos'],
                    "estado" => ($repuesto['estado'] == 1 ? "Activo" : ($repuesto['estado'] == 2 ? "inactivo" : "indefinido")),
                    "cantidad" => intval($repuesto['total_stock'])
                );
            }
        // }

        // Crear el arreglo de respuesta
        $response = array(
            "draw" => intval(@$_POST['draw']),
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
        $search_ql = ($search ? " WHERE nombre LIKE '%$search%' AND empresa_id = ". $_SESSION['empresa_id'] : " WHERE empresa_id = ".$_SESSION['empresa_id'] );

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $categorias = $db->query("SELECT * FROM categorias" . $search_ql . $order_ql . " LIMIT $start, $length");

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
        $search_ql = ($search ? " WHERE nombre LIKE '%$search%' AND empresa_id = " . $_SESSION['empresa_id'] : " WHERE empresa_id = " . $_SESSION['empresa_id']);

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $proveedores = $db->query("SELECT * FROM proveedores" . $search_ql . $order_ql . " LIMIT $start, $length");

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
        $search_ql = ($search ? " WHERE nombre LIKE '%$search%' AND empresa_id = " .$_SESSION['empresa_id'] : " WHERE empresa_id = ". $_SESSION['empresa_id']);

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $clientes = $db->query("SELECT * FROM clientes" . $search_ql . $order_ql . " LIMIT $start, $length");

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
        $search_ql = ($search ? " WHERE nombre LIKE '%$search%' AND empresa_id = " . $_SESSION['empresa_id'] : " WHERE empresa_id = ".$_SESSION['empresa_id']);

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $marcas_codigos = $db->query("SELECT * FROM marcas_codigos" . $search_ql . $order_ql . " LIMIT $start, $length");

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
    case 'precios':
        $order_position = array("id", "nombre_repuesto", "precio", "precio_minimo", "precio_sugerido", "precio_maximo", "tipo_precio", "moneda");
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY id DESC");
        $search_ql = ($search ? " WHERE r.nombre LIKE '%$search%' AND p.empresa_id = " . $_SESSION['empresa_id'] : " WHERE p.empresa_id = " . $_SESSION['empresa_id']);

        // Consulta SQL para obtener los datos requeridos
        $start -= 1;
        $query = "SELECT p.id, r.nombre AS nombre_repuesto, p.precio, p.precio_minimo, p.precio_sugerido, p.precio_maximo, CASE
                        WHEN p.tipo_precio = 1 THEN 'Precio ruta'
                        WHEN p.tipo_precio = 2 THEN 'Precio taller'
                        WHEN p.tipo_precio = 3 THEN 'Precio consumidor final'
                        -- Agrega más condiciones según sea necesario
                        ELSE 'No definido'
                    END AS tipo_precio, m.nombre AS moneda
                  FROM precios AS p
                  JOIN repuestos AS r ON p.repuesto_id = r.id
                  JOIN monedas AS m ON p.moneda_id = m.id
                  " . $search_ql . $order_ql . " LIMIT $start, $length";

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $result = $db->query($query);

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query("SELECT COUNT(id) as total FROM precios");
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query("SELECT COUNT(p.id) as total
                                           FROM precios AS p
                                           JOIN repuestos AS r ON p.repuesto_id = r.id
                                           JOIN monedas AS m ON p.moneda_id = m.id
                                           " . $search_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }

        // Formatear los datos para DataTables
        $data = array();
        foreach ($result as $row) {
            $data[] = array(
                "id" => $row['id'],
                "repuesto_nombre" => $row['nombre_repuesto'],
                "precio" => $row['precio'],
                "minimo" => $row['precio_minimo'],
                "sugerido" => $row['precio_sugerido'],
                "maximo" => $row['precio_maximo'],
                "tipo_precio" => $row['tipo_precio'],
                "moneda_nombre" => $row['moneda'],
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
    case 'monedas':
        $order_position = array("id", "nombre");
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY id DESC");
        $search_ql = ($search ? " WHERE nombre LIKE '%$search%' AND empresa_id = " . $_SESSION['empresa_id'] : " WHERE empresa_id = " . $_SESSION['empresa_id']);

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $monedas = $db->query("SELECT * FROM monedas" . $search_ql . $order_ql . " LIMIT $start, $length");

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query("SELECT COUNT(id) as total FROM monedas");
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query("SELECT COUNT(id) as total FROM monedas".$search_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }


        // Formatear los datos para DataTables
        $data = array();
        foreach ($monedas as $moneda) {
            $data[] = array(
                "id" => $moneda['id'],
                "nombre" => $moneda['nombre'],
                "tipo_cambio_quetzal" => $moneda['tipo_cambio'],
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
    case 'compras':
        $order_position = array("id", "nombre");
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY id DESC");
        $search_ql = ($search ? " WHERE nombre LIKE '%$search%' AND c.empresa_id = " . $_SESSION['empresa_id'] : " WHERE c.empresa_id = " . $_SESSION['empresa_id']);

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $monedas = $db->query("SELECT c.*, cl.nombre AS cliente, v.nombre AS vendedor FROM compras AS c LEFT JOIN clientes AS cl ON c.cliente_id = cl.id LEFT JOIN usuarios AS v ON c.vendedor_id = v.id" . $search_ql . $order_ql . " LIMIT $start, $length");

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query("SELECT COUNT(id) as total FROM compras");
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query("SELECT COUNT(id) as total FROM compras".$search_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }


        // Formatear los datos para DataTables
        $data = array();
        foreach ($monedas as $compra) {
            $data[] = array(
                "id" => $compra['id'],
                "cliente" => $compra['cliente'],
                "vendedor" => $compra['vendedor'],
                "fecha_documento" => $compra['fecha_documento'],
                "fecha_ofrecido" => $compra['fecha_ofrecido'],
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
    case 'asignaciones':
        $order_position = array("id", "nombre");
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY id DESC");
        $search_ql = ($search ? " WHERE nombre LIKE '%$search%' AND ub.empresa_id = " . $_SESSION['empresa_id'] : " WHERE ub.empresa_id = ". $_SESSION['empresa_id']);

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $monedas = $db->query("SELECT ub.*, u.nombre as usuario_nombre, b.nombre as bodega_name FROM usuarios_bodegas AS ub LEFT JOIN usuarios AS u ON ub.usuario_id = u.id LEFT JOIN bodegas as b ON ub.bodega_id = b.id" . $search_ql . $order_ql . " LIMIT $start, $length");

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query("SELECT COUNT(id) as total FROM usuarios_bodegas");
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query("SELECT COUNT(id) as total FROM usuarios_bodegas".$search_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }


        // Formatear los datos para DataTables
        $data = array();
        foreach ($monedas as $compra) {
            $data[] = array(
                "id" => $compra['id'],
                "usuario_nombre" => $compra['usuario_nombre'],
                "bodega_name" => $compra['bodega_name'],
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
    case 'empresas':
        $order_position = array("id", "nombre");
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY id DESC");
        $search_ql = ($search ? " WHERE nombre LIKE '%$search%'" : "");

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $monedas = $db->query("SELECT * FROM empresas" . $search_ql . $order_ql . " LIMIT $start, $length");

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query("SELECT COUNT(id) as total FROM empresas");
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query("SELECT COUNT(id) as total FROM empresas".$search_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }


        // Formatear los datos para DataTables
        $data = array();
        foreach ($monedas as $compra) {
            $data[] = array(
                "id" => $compra['id'],
                "nombre" => $compra['nombre'],
                "direccion" => $compra['direccion'],
                "nit" => $compra['nit'],
                "telefono" => $compra['telefono'],
                "email" => $compra['email'],
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
    case 'pedidos':
        $order_position = array("id", "fecha", "estado", "usuario_nombre", "empleado");
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY p.id DESC");
        $search_ql = ($search ? " WHERE usuario_nombre LIKE '%$search%' OR empleado LIKE '%$search%' AND p.empresa_id = " . $_SESSION['empresa_id'] : " WHERE p.empresa_id = " . $_SESSION['empresa_id']);

        // Ejecutar la consulta y obtener los datos
        $start -= 1;
        $monedas = $db->query("SELECT p.id, p.cliente_nombre AS usuario_nombre, fecha, CASE
            WHEN estado = 1 THEN 'Pendiente'
            WHEN estado = 2 THEN 'En proceso'
            WHEN estado = 3 THEN 'Completado'
            ELSE 'No detectado'
            END AS estado, e.nombre AS empleado FROM pedidos AS p LEFT JOIN usuarios AS e ON e.id = p.id_empleado " . $search_ql . $order_ql . " LIMIT $start, $length");

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query("SELECT COUNT(id) as total FROM pedidos");
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query("SELECT COUNT(id) as total FROM pedidos".$search_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }


        // Formatear los datos para DataTables
        $data = array();
        foreach ($monedas as $compra) {
            $data[] = array(
                "id" => $compra['id'],
                "fecha" => $compra['fecha'],
                "estado" => $compra['estado'],
                "usuario_nombre" => $compra['usuario_nombre'],
                "empleado" => $compra['empleado'],
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
        case 'traslados':
            // Mantén solo las consultas relacionadas con 'inventario_movimientos' para 'compra' y 'salida'.
            
            $order_position = array("id", "repuesto_nombre", "bodega_origen", "bodega_destino", "cantidad", "usuario", "comentario", "fecha");
            $order_ql = ($order ? " ORDER BY " . $order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY im.fecha DESC");
            $search_ql = ($search ? "AND repuesto_nombre LIKE '%$search%' OR usuario LIKE '%$search%' AND im.empresa_id = " . $_SESSION['empresa_id'] : "AND im.empresa_id = " . $_SESSION['empresa_id']);

            // Ejecutar la consulta y obtener los datos
            $start -= 1;
            $traslados = $db->query("SELECT 
                                    im.id, 
                                    r.nombre AS repuesto_nombre,
                                    IF(im.tipo = 'compra', b1.nombre, 
                                       IF(im.tipo = 'salida', 
                                          (SELECT b2.nombre FROM inventario_movimientos AS im2 
                                           INNER JOIN bodegas AS b2 ON im2.bodega_id = b2.id 
                                           WHERE im2.id = im.pedido_id), 
                                          NULL)
                                    ) AS bodega_destino,
                                    IF(im.tipo = 'salida' AND im.pedido_id IS NOT NULL, b2.nombre, NULL) AS bodega_origen,
                                    im.cantidad,
                                    u.nombre AS usuario,
                                    im.comentario,
                                    im.fecha,
                                    im.tipo
                                FROM inventario_movimientos AS im
                                INNER JOIN repuestos AS r ON im.repuesto_id = r.id
                                LEFT JOIN bodegas AS b1 ON im.bodega_id = b1.id AND im.tipo = 'compra'
                                LEFT JOIN bodegas AS b2 ON im.bodega_id = b2.id AND im.tipo = 'salida' AND im.pedido_id IS NOT NULL
                                INNER JOIN usuarios AS u ON im.usuario_id = u.id
                                WHERE im.tipo IN ('compra', 'salida') AND (
                                                                im.tipo = 'salida' 
                                                                OR (
                                                                    im.tipo = 'compra' 
                                                                    AND im.pedido_id IS NOT NULL 
                                                                    AND NOT EXISTS (
                                                                        SELECT 1 FROM inventario_movimientos AS im3 
                                                                        WHERE im3.tipo = 'salida' 
                                                                        AND im3.pedido_id = im.id
                                                                    )
                                                                )
                                                            ) " . $search_ql . $order_ql . " LIMIT $start, $length");

            // Obtener el número total de registros sin filtro
            $resultTotal = $db->query("SELECT COUNT(id) as total FROM inventario_movimientos WHERE tipo IN ('compra', 'salida')");
            $rowTotal = $resultTotal->fetch_assoc();
            $totalRegistros = $rowTotal['total'];

            // Obtener el número total de registros con el filtro
            $resultFilteredTotal = $db->query("SELECT COUNT(im.id) as total FROM inventario_movimientos as im WHERE im.tipo IN ('compra', 'salida') " . $search_ql);

            if ($resultFilteredTotal) {
                $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
                $totalFiltrados = $rowFilteredTotal['total'];
            } else {
                // Manejar el error de la consulta aquí
                $totalFiltrados = 0;
            }

            // Formatear los datos para DataTables
            $data = array();
            foreach ($traslados as $traslado) {
                $data[] = array(
                    "id" => $traslado['id'],
                    "repuesto_nombre" => $traslado['repuesto_nombre'],
                    "bodega_origen" => $traslado['bodega_origen'],
                    "bodega_destino" => $traslado['bodega_destino'],
                    "cantidad" => $traslado['cantidad'],
                    "usuario" => $traslado['usuario'],
                    "comentario" => $traslado['comentario'],
                    "fecha" => $traslado['fecha'],
                    'tipo' => $traslado['tipo']
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