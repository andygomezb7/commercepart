<?php
// Conexión a la base de datos y otras configuraciones
session_start();
require_once '../../secure/trun.php';

$method = $_REQUEST['method'];
// Obtener los parámetros de DataTables
$start = @$_REQUEST['start'];
$length = @$_REQUEST['length'];
$search = @$_REQUEST['search'];
// if(sea)
$order = @$_REQUEST['order'];

$bancos_array = array(
    array('id' => 0, "nombre" => "default"),
    array('id' => 1, 'nombre' => 'G&T continental'),
    array('id' => 2, 'nombre' => 'Banco industrial'),
    array('id' => 3, 'nombre' => 'Banco de los trabajadores'),
    array('id' => 4, 'nombre' => 'Banrural'),
    array('id' => 5, 'nombre' => 'Bac'),
    array('id' => 6, 'nombre' => 'Promerica'),
);

switch ($method) {
    case 'repuestos':

        require_once '../../secure/class/inventario.php';
        $inventario = new Inventario($db);
        //
        $order_position = array("r.nombre", "r.descripcion", "r.precio", "total_stock", "ubicacion_bodega", "codigos");
        $bodegasfiltro = @$_REQUEST['bodegas'];
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY codigo DESC");
        $search_ql = ($search ? " WHERE r.nombre LIKE '%$search%' OR r.descripcion LIKE '%$search%' OR (EXISTS (SELECT 1 FROM codigos_repuesto WHERE id_repuesto = r.id AND codigo = '$search'))" : "");
        $bodegasfiltro = (intval($bodegasfiltro) ? ($search_ql?' AND ':' WHERE ')."movimientos.bodega_id = '$bodegasfiltro' AND r.empresa_id = ". $_SESSION['empresa_id'] : ($search_ql?' AND ':' WHERE ')."r.empresa_id=" . $_SESSION['empresa_id']);

        // Ejecutar la consulta y obtener los datos
        $sql = "SELECT r.*, GROUP_CONCAT(DISTINCT movimientos.nombre_bodega ORDER BY movimientos.nombre_bodega ASC) as ubicacion_bodega, (SELECT GROUP_CONCAT(codigo) FROM codigos_repuesto WHERE id_repuesto = r.id) AS codigos, SUM(coalesce(movimientos.inventario, 0)) AS total_stock FROM repuestos AS r LEFT JOIN bodegas AS b ON r.ubicacion_bodega = b.id LEFT JOIN
            (SELECT
                    im.repuesto_id,
                    b.nombre AS nombre_bodega,
                    r.nombre AS nombre_repuesto,
                    im.bodega_id,
                    im.fecha_estimada,
                    SUM(
                        CASE WHEN(im.tipos = 'inventario') THEN im.cantidad ELSE 0
                    END
                ) - SUM(
                    CASE WHEN(im.tipos = 'salida') THEN im.cantidad ELSE 0
                END
                ) - SUM(
                    CASE WHEN(im.tipos = 'venta') THEN im.cantidad ELSE 0
                END
                ) AS inventario,
                SUM(
                    CASE WHEN(im.tipos = 'reserva') THEN im.cantidad ELSE 0
                END
                ) AS reserva
                FROM
                    (
                    SELECT
                        repuesto_id,
                        bodega_id,
                        cantidad,
                        'inventario' AS tipos,
                        fecha_estimada,
                        empresa_id
                    FROM
                        inventario_movimientos
                    WHERE
                        tipo = 'compra'
                    UNION ALL
                SELECT
                    repuesto_id,
                    bodega_id,
                    cantidad,
                    'salida' AS tipos,
                    fecha_estimada,
                    empresa_id
                FROM
                    inventario_movimientos
                WHERE
                    tipo = 'salida'
                UNION ALL
                SELECT
                    repuesto_id,
                    bodega_id,
                    cantidad,
                    'venta' AS tipos,
                    fecha_estimada,
                    empresa_id
                FROM
                    inventario_movimientos
                WHERE
                    tipo = 'venta'
                UNION ALL
                SELECT
                    repuesto_id,
                    bodega_id,
                    cantidad,
                    'reserva' AS tipos,
                    fecha_estimada,
                    empresa_id
                FROM
                    inventario_reserva
                ) AS im
                INNER JOIN bodegas AS b
                ON
                    im.bodega_id = b.id
                INNER JOIN repuestos AS r
                ON
                    im.repuesto_id = r.id
                WHERE im.empresa_id = '".$_SESSION['empresa_id']."'
                GROUP BY
                    b.id, r.id) AS movimientos ON r.id = movimientos.repuesto_id". $bodegasfiltro . $search_ql . ' GROUP BY r.id' . $order_ql . " LIMIT $start, $length";
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
        $order_ql = ($order ? " GROUP BY c.id ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " GROUP BY c.id ORDER BY id DESC");
        $search_ql = ($search ? " WHERE nombre LIKE '%$search%' AND c.empresa_id = " . $_SESSION['empresa_id'] : " WHERE c.empresa_id = " . $_SESSION['empresa_id']);

        // Ejecutar la consulta y obtener los datos
        $monedas = $db->query("SELECT c.*, pv.nombre AS proveedor, v.nombre AS vendedor, SUM(coalesce(ca.cantidad * ca.precio, 0)) AS total FROM compras AS c LEFT JOIN proveedores AS pv ON c.proveedor = pv.id LEFT JOIN usuarios AS v ON c.vendedor_id = v.id LEFT JOIN compras_articulos AS ca ON c.id = ca.compra_id" . $search_ql . $order_ql . " LIMIT $start, $length");

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
                "correlativo" => $compra['correlativo'],
                "proveedor" => $compra['proveedor'],
                "vendedor" => $compra['vendedor'],
                "fecha_documento" => $compra['fecha_documento'],
                "fecha_ofrecido" => $compra['fecha_ofrecido'],
                "estado" => $compra['estado'],
                "total" => 'Q.'. $compra['total'],
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
        $monedas = $db->query("SELECT p.id, p.cliente_nombre AS usuario_nombre, fecha, p.estado, e.nombre AS empleado FROM pedidos AS p LEFT JOIN usuarios AS e ON e.id = p.id_empleado " . $search_ql . $order_ql . " LIMIT $start, $length");

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
    case 'cuentas_banco':
        $order_position = array("id", "numero_de_cuenta", "tipo_cuenta", "moneda", "nombre_cuenta", "descripcion", "saldo_inicial", "fecha_inicio_saldo_inicial", "cuenta_contable");
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY p.id DESC");
        $search_ql = ($search ? " WHERE b.nombre_cuenta LIKE '%$search%' OR b.descripcion LIKE '%$search%' AND b.empresa_id = " . $_SESSION['empresa_id'] : " WHERE b.empresa_id = " . $_SESSION['empresa_id']);

        // Ejecutar la consulta y obtener los datos de cuentas de banco
        $query = "SELECT b.id, b.numero_cuenta, tc.tipo AS tipo_cuenta, m.nombre AS moneda, b.nombre_cuenta, b.descripcion, b.saldo_inicial, b.fecha_inicio_saldo, b.banco_id, cc.NombreCuenta AS cuenta_contable FROM Banco AS b 
                                    LEFT JOIN tipo_cuenta AS tc ON b.tipo_cuenta_id = tc.id
                                    LEFT JOIN monedas AS m ON b.moneda_id = m.id
                                    LEFT JOIN cuenta_contable AS cc ON b.cuenta_contable_defecto_id = cc.id
                                    ";
        $cuentasBanco = $db->query($query . $search_ql . $order_ql . " LIMIT $start, $length");

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query("SELECT COUNT(id) as total FROM Banco");
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query("SELECT COUNT(id) as total FROM Banco".$search_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }

        // Formatear los datos para DataTables
        $data = array();
        if($cuentasBanco) {
            foreach ($cuentasBanco as $cuenta) {
                $data[] = array(
                    "id" => $cuenta['id'],
                    "numero_de_cuenta" => $cuenta['numero_cuenta'],
                    "tipo_cuenta" => $cuenta['tipo_cuenta'],
                    "moneda" => $cuenta['moneda'],
                    "nombre_cuenta" => $cuenta['nombre_cuenta'],
                    "descripcion" => $cuenta['descripcion'],
                    "saldo_inicial" => $cuenta['saldo_inicial'],
                    "fecha_inicio_saldo" => $cuenta['fecha_inicio_saldo'],
                    "cuenta_contable" => $cuenta['cuenta_contable'],
                    "banco" => $bancos_array[$cuenta['banco_id']]['nombre']
                );
            }
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
    case 'cuentas_contables':
        $order_position = array("id", "NombreCuenta");
        $order_ql = ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY ID DESC");
        $search_ql = ($search ? " WHERE NombreCuenta LIKE '%$search%' AND empresa_id = " . $_SESSION['empresa_id'] : " WHERE empresa_id = " . $_SESSION['empresa_id']);

        include('../../secure/class/cuenta_contable.php');  // Asegúrate de incluir el archivo correcto
        $aCuentaContable = new CuentaContable($db);

        // Ejecutar la consulta y obtener los datos de cuentas de banco
        $sql_countable = "
            SELECT ID, NombreCuenta, empresa_id,
            (CASE
                WHEN (l1 IS NOT NULL AND l2 IS NULL) THEN l1
                WHEN (l1 IS NOT NULL AND l2 IS NOT NULL) THEN l2
            END) as l1,
            (CASE
                WHEN (l1_id IS NOT NULL AND l2_id IS NULL) THEN l1_id
                WHEN (l1_id IS NOT NULL AND l2_id IS NOT NULL) THEN l2_id
            END) as l1_id,
            (CASE
                WHEN (l1 IS NOT NULL AND l2 IS NOT NULL) THEN l1
                WHEN (l1 IS NOT NULL AND l2 IS NOT NULL) THEN l2
            END) as l2,
            (CASE
                WHEN (l1_id IS NOT NULL AND l2_id IS NOT NULL) THEN l1_id
                WHEN (l1_id IS NOT NULL AND l2_id IS NOT NULL) THEN l2_id
            END) as l2_id,
            (SELECT ID FROM cuenta_contable il WHERE il.CuentaContablePadreID = account_plan.ID LIMIT 1) as has_parent
            FROM (
                SELECT level_main.ID, level_main.NombreCuenta, level_main.empresa_id,
                level_main.CuentaContablePadreID as main,
                level_1.NombreCuenta as l1,
                level_1.ID as l1_id,
                level_2.NombreCuenta as l2,
                level_2.ID as l2_id
                FROM cuenta_contable level_main
                LEFT JOIN cuenta_contable level_1 ON level_main.CuentaContablePadreID = level_1.ID
                LEFT JOIN cuenta_contable level_2 ON level_1.CuentaContablePadreID = level_2.ID
            ) account_plan";
        $cuentasContables = $db->query($sql_countable . $search_ql . $order_ql . " LIMIT $start, $length");

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query(str_replace('ID, NombreCuenta, empresa_id,', 'count(ID) AS total, ', $sql_countable));
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query(str_replace('ID, NombreCuenta, empresa_id,', 'count(ID) AS total, ', $sql_countable) . $search_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }

        // Formatear los datos para DataTables
        $data = array();
        if(@$cuentasContables) {
            $db_accounting_account = $aCuentaContable->set_numeration_accounting_plan();
            foreach ($cuentasContables as $res) {
                $data[] = array(
                    "ID" => $res['ID'],
                    "NombreCuenta" => $aCuentaContable->get_accounting_plan_numaration($res['NombreCuenta'], $res['ID'], $db_accounting_account),
                    "TipoCuenta" => "",
                    "CuentaContablePrincipal" => $aCuentaContable->get_accounting_plan_numaration($aCuentaContable->_repace_if_empty($res['l1'], '-'), $res['l1_id'], $db_accounting_account),
                    "CuentaContablePadreID" => $aCuentaContable->get_accounting_plan_numaration($aCuentaContable->_repace_if_empty($res['l2'], '-'), $res['l2_id'], $db_accounting_account)
                );
            }
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
    case 'librodebancos':
        $start_date = $_GET['start'];
        $end_date = $_GET['end'];
        $order_position = array("im.id", "im.id", "im.id", "im.fecha", "Tipo_Movimiento", "Descripcion", "Cuenta_Contable_Banco", "debe", "haber");
        $order_ql = " GROUP BY im.id" . ($order ? " ORDER BY ".$order_position[$order[0]['column']] . " " . $order[0]['dir'] : " ORDER BY im.id DESC");
        $search_ql = ($search ? " WHERE (im.fecha BETWEEN '".$start_date."' AND '".$end_date."') AND (im.tipo = 'compra' OR im.tipo = 'venta') AND Descripcion LIKE '%$search%' AND im.empresa_id = " . $_SESSION['empresa_id'] : " WHERE (im.fecha BETWEEN '".$start_date."' AND '".$end_date."') AND (im.tipo = 'compra' OR im.tipo = 'venta') AND im.empresa_id = " . $_SESSION['empresa_id']) . " AND cc.CuentaContablePadreID IS NULL";

        // Ejecutar la consulta y obtener los datos de cuentas de banco
        $sql_countable = "
            SELECT {select} 
            FROM inventario_movimientos im
            LEFT JOIN cuenta_contable AS cc
            ON
                (
                    cc.TipoCuenta = 'Ingresos' AND im.tipo = 'venta'
                ) OR (
                    cc.TipoCuenta = 'Egresos' AND im.tipo = 'compra'
                )
            LEFT JOIN Banco b ON
                b.cuenta_contable_defecto_id = cc.ID
            LEFT JOIN (
                SELECT id_pedido, SUM(cantidad * precio_unitario) AS total_debe
                FROM pedido_detalles
                GROUP BY id_pedido
            ) AS ped ON im.pedido_id = ped.id_pedido AND im.tipo = 'venta'
            LEFT JOIN (
                SELECT compra_id, SUM(cantidad * precio) AS total_haber
                FROM compras_articulos
                GROUP BY compra_id
            ) AS com ON im.compra_id = com.compra_id AND im.tipo = 'compra'";
        $queryResult = str_replace('{select}', '
            im.tipo AS Tipo_Movimiento,
            im.fecha AS Fecha_Movimiento,
            COALESCE(ped.total_debe, 0) AS debe,
            COALESCE(com.total_haber, 0) AS haber,
            im.comentario AS Descripcion,
            b.id AS Banco_ID,
            b.nombre_cuenta AS Nombre_Banco,
            cc.NombreCuenta AS Cuenta_Contable_Banco,
            (
                CASE WHEN im.tipo = "venta" THEN "Ingresos" WHEN im.tipo = "compra" THEN "Egresos" END
            ) AS TipoCuenta,
            im.id AS movimientoid
        ', $sql_countable) . $search_ql . $order_ql . " LIMIT $start, $length";
        var_dump($queryResult);
        $cuentasContables = $db->query($queryResult);

        // Obtener el número total de registros sin filtro
        $resultTotal = $db->query(str_replace('{select}', 'count(im.id) AS total,
                            (CASE WHEN im.tipo = "venta" THEN "Ingresos" WHEN im.tipo = "compra" THEN "Egresos" END) AS TipoCuenta ', $sql_countable . $order_ql));
        $rowTotal = $resultTotal->fetch_assoc();
        $totalRegistros = $rowTotal['total'];

        // Obtener el número total de registros con el filtro
        $resultFilteredTotal = $db->query(str_replace('{select}', 'count(im.id) AS total ', $sql_countable) . $search_ql . $order_ql);

        if ($resultFilteredTotal) {
            $rowFilteredTotal = $resultFilteredTotal->fetch_assoc();
            $totalFiltrados = $rowFilteredTotal['total'];
        } else {
            // Manejar el error de la consulta aquí
            $totalFiltrados = 0;
        }

        // Formatear los datos para DataTables
        $data = array();
        if(@$cuentasContables) {
            foreach ($cuentasContables as $res) {
                $data[] = array(
                    "id" => $res['movimientoid'],
                    'cuenta_contable' => $res['Cuenta_Contable_Banco'],
                    'descripcion' => $res['Descripcion'],
                    'monto' => $res['debe'],
                    'fecha' => $res['Fecha_Movimiento'],
                    'tipo' => $res['Tipo_Movimiento'],
                    'debe' => $res['debe'],
                    'haber' => $res['haber'],
                );
            }
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
    case 'flujodecaja':
        $start_date = $_GET['start'];
        $end_date = $_GET['end'];
        $query = $db->query("SELECT
            DATE_FORMAT(im.fecha, '%Y-%m') AS Mes,
            cc.TipoCuenta,
            cc.NombreCuenta,
            cc.ID,
            cc.CuentaContablePadreID AS padre,
            SUM(
                CASE
                    WHEN cc.TipoCuenta = 'Ingresos' AND im.tipo = 'venta' THEN im.cantidad
                    ELSE 0
                END
            ) AS TotalIngresos,
            SUM(
                CASE
                    WHEN cc.TipoCuenta = 'Egresos' AND im.tipo = 'compra' THEN im.cantidad
                    ELSE 0
                END
            ) AS TotalEgresos
        FROM inventario_movimientos im
        LEFT JOIN cuenta_contable AS cc ON (
            (cc.TipoCuenta = 'Ingresos' AND im.tipo = 'venta')
            OR (cc.TipoCuenta = 'Egresos' AND im.tipo = 'compra')
        )
        WHERE im.fecha BETWEEN '".$start_date."' AND '".$end_date."' AND im.empresa_id = '".$_SESSION['empresa_id']."'
        GROUP BY Mes, cc.TipoCuenta, cc.NombreCuenta
        ORDER BY Mes, cc.TipoCuenta, cc.NombreCuenta");
        $response = array();

        foreach ($query AS $caja) {
            if ($caja['ID']) {
                $response[] = array( 
                    'mes' => $caja['Mes'], 
                    'cuentaPadre' => intval($caja['padre']), 
                    'idCuenta' => intval($caja['ID']), 
                    'tipoCuenta' => $caja['TipoCuenta'], 
                    'nombreCuenta' => $caja['NombreCuenta'], 
                    'totalIngresos' => intval($caja['TotalIngresos']), 
                    'totalEgresos' => intval($caja['TotalEgresos'])
                );
            }
        }

        echo json_encode($response);
        break;
    default:
        // code...
        break;
}
?>