<?php
    $tipo =  filter_input(INPUT_GET, 'tipo', FILTER_SANITIZE_EMAIL);
    if ($tipo == 102) {
        $parameters_page = array(
            'title' => 'Usuarios',
            'header' => 'Agregar/editar usuarios',
            'buttons' => array(
                array("name" => "<i class='fas fa-plus'></i> Agregar usuario", "action" => "?tipo=102&agregar=1", "type" => "success")
            )
        );
        include('header.php');
        include('usuarios.php');
    } else if ($tipo == 3) {
        $parameters_page = array(
            'title' => 'Códigos',
            'header' => 'Agregar/editar códigos',
            'buttons' => array(
                array("name" => "<i class='fas fa-plus'></i> Agregar código", "action" => "?tipo=3&agregar=1", "type" => "success")
            )
        );
        include('header.php');
        include('repuestos.php');
    } else if ($tipo == 4) {
        $parameters_page = array(
            'title' => 'Bodegas',
            'header' => 'Agregar/editar bodegas'
        );
        include('header.php');
        include('bodegas.php');
    } else if ($tipo == 5) {
        $parameters_page = array(
            'title' => 'Marcas',
            'header' => 'Agregar/editar marcas'
        );
        include('header.php');
        include('marcas.php');
    } else if ($tipo == 6) {
        $parameters_page = array(
            'title' => 'Modelos',
            'header' => 'Agregar/editar modelos'
        );
        include('header.php');
        include('modelos.php');
    } else if ($tipo == 7) {
        $parameters_page = array(
            'title' => 'Categorias',
            'header' => 'Agregar/editar categorias'
        );
        include('header.php');
        include('categorias.php');
    } else if ($tipo == 8) {
        $parameters_page = array(
            'title' => 'Modelos/Marcas',
            'header' => 'Asignacion de modelos y marcas'
        );
        include('header.php');
        include('modelosmarcas.php');
    } else if ($tipo == 9) {
        $parameters_page = array(
            'title' => 'Códigos de repuestos',
            'header' => 'Creación y asignación de códigos'
        );
        include('header.php');
        include('asignacioncodigo.php');
    } else if ($tipo == 10) {
        $parameters_page = array(
            'title' => 'Motores',
            'header' => 'Asignación de motores'
        );
        include('header.php');
        include('asignarmotores.php');
    } else if ($tipo == 11) {
        $parameters_page = array(
            'title' => 'Mis Motores',
            'header' => 'Agregar/editar motores'
        );
        include('header.php');
        include('motores.php');
    } else if ($tipo == 12) {
        $parameters_page = array(
            'title' => 'Pedidos',
            'header' => 'Creación de pedidos',
            'buttons' => array(
                array("name" => "<i class='fas fa-plus'></i> Crear Pedido", "action" => "?tipo=12&agregar=1", "type" => "success")
            )
        );
        include('header.php');
        include('pedidos.php');
    } else if ($tipo == 13) {
        $parameters_page = array(
            'title' => 'Importación de información',
            'header' => 'Importar masiva de información'
        );
        include('header.php');
        include('import.php');
    } else if ($tipo == 14) {
        $parameters_page = array(
            'title' => 'Compras',
            'header' => 'Compras',
            'buttons' => array(
                array("name" => "<i class='fas fa-plus'></i> Crear compra", "action" => "?tipo=14&agregar=1", "type" => "success")
            )
        );
        include('header.php');
        include('compras.php');
    } else if ($tipo == 15) {
        $parameters_page = array(
            'title' => 'Agregar Proveedor',
            'header' => 'Agregar Proveedor'
        );
        include('header.php');
        include('proveedores.php');
    } else if ($tipo == 16) {
        $parameters_page = array(
            'title' => 'Agregar Cliente',
            'header' => 'Agregar Cliente'
        );
        include('header.php');
        include('clientes.php');
    } else if ($tipo == 17) {
        $parameters_page = array(
            'title' => 'Agregar Marcas de código',
            'header' => 'Agregar Marcas de código'
        );
        include('header.php');
        include('marcas_codigos.php');
    } else if ($tipo == 18) {
        $parameters_page = array(
            'title' => 'Agregar Precios',
            'header' => 'Agregar Precios'
        );
        include('header.php');
        include('precios.php');
    } else if ($tipo == 19) {
        $parameters_page = array(
            'title' => 'Agregar Monedas',
            'header' => 'Agregar Monedas'
        );
        include('header.php');
        include('monedas.php');
    } else if ($tipo == 20) {
        $parameters_page = array(
            'title' => 'Editar mi perfil',
            'header' => 'Editar mi perfil'
        );
        include('header.php');
        include('usuario.php');
    } else if ($tipo == 21) {
        $parameters_page = array(
            'title' => 'Asignar bodegas',
            'header' => 'Asignar bodegas'
        );
        include('header.php');
        include('asignarbodega.php');
    } else if ($tipo == 22) {
        $parameters_page = array(
            'title' => 'Traslado de inventario',
            'header' => 'Traslado de inventario'
        );
        include('header.php');
        include('trasladodeinventario.php');
        //
    } else if ($tipo == 103) {
        $parameters_page = array(
            'title' => 'Empresas',
            'header' => 'Empresas'
        );
        include('header.php');
        include('empresas.php');
    } else {
        $parameters_page = array(
            'title' => 'Tablero',
            'header' => 'Tablero'
        );
        include('header.php');
    ?>
    <!-- Contenido del dashboard -->

    <div class="container">

        <!-- Rentabilidad -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h3>Resumen de ventas</h3>
                </div>
                <div class="card shadow-none">
                    <div class="card-body">
                        <?php
                            $primera_fecha = '2023-08-01';
                            $ultima_fecha = date('Y-m-d');
                            $entregas = $db->query("SELECT
                                                        iv.bodega_id,
                                                        r.nombre AS repuesto_nombre,
                                                        SUM(iv.cantidad) AS cantidad_vendida,
                                                        MIN(iv.fecha) AS primera_venta,
                                                        MAX(iv.fecha) AS ultima_venta,
                                                        SUM(iv.cantidad * (pd.precio_unitario - p.precio)) AS rentabilidad
                                                    FROM
                                                        inventario_movimientos iv
                                                    INNER JOIN
                                                        repuestos r ON iv.repuesto_id = r.id
                                                    LEFT JOIN
                                                        precios p ON r.id = p.repuesto_id
                                                    LEFT JOIN 
                                                        pedido_detalles pd ON iv.pedido_id = pd.id_pedido
                                                    WHERE
                                                        iv.tipo = 'venta'
                                                        AND iv.fecha BETWEEN '".$primera_fecha."' AND '".$ultima_fecha."'
                                                        AND iv.empresa_id = '".$_SESSION['empresa_id']."'
                                                    GROUP BY
                                                        iv.bodega_id, iv.repuesto_id
                                                    ORDER BY
                                                        cantidad_vendida DESC");
                            if ($entregas) {
                        ?>
                        <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="monedasTable">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th>Primera Venta</th>
                                    <th>Ultima Venta</th>
                                    <th>Rentabilidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach ($entregas as $row) {
                                        echo '<tr>
                                                <td>'.$row['repuesto_nombre'].'</td>
                                                <td>'.$row['cantidad_vendida'].'</td>
                                                <td>'.$row['primera_venta'].'</td>
                                                <td>'.$row['ultima_venta'].'</td>
                                                <td>'.$row['rentabilidad'].'</td>
                                              </tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                        <?php
                            } else {
                                echo '<div class="alert alert-info">No hay movimientos aun</div>';
                            }
                        ?>
                        <!-- Final de repuestos sin stock -->
                    </div>
                </div>
            </div>
            <!-- Codigos mas vendidos -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h3>Codigos menos vendidos</h3>
                </div>
                <div class="card shadow-none">
                    <div class="card-body">
                        <?php
                            $primera_fecha = '2023-08-01';
                            $ultima_fecha = date('Y-m-d');
                            $ventascompras = $db->query("SELECT repuesto_id, nombre_repuesto, SUM(total_pedidos) AS total_pedidos, SUM(total_compras) AS total_compras
                                                    FROM (
                                                        SELECT r.id AS repuesto_id, r.nombre AS nombre_repuesto, 0 AS total_pedidos, COUNT(DISTINCT c.id) AS total_compras
                                                        FROM repuestos r
                                                        LEFT JOIN compras_articulos ca ON r.id = ca.repuesto_id
                                                        LEFT JOIN compras c ON ca.compra_id = c.id
                                                        WHERE c.fecha_documento BETWEEN '".$primera_fecha."' AND '".$ultima_fecha."' AND r.empresa_id = '".$_SESSION['empresa_id']."'
                                                        GROUP BY r.id, r.nombre
                                                        UNION ALL
                                                        SELECT r.id AS repuesto_id, r.nombre AS nombre_repuesto, COUNT(DISTINCT p.id) AS total_pedidos, 0 AS total_compras
                                                        FROM repuestos r
                                                            LEFT JOIN pedido_detalles pd ON r.id = pd.id_repuesto
                                                            LEFT JOIN pedidos p ON pd.id_pedido = p.id
                                                        WHERE p.fecha BETWEEN '".$primera_fecha."' AND '".$ultima_fecha."' AND r.empresa_id = '".$_SESSION['empresa_id']."'
                                                        GROUP BY r.id, r.nombre
                                                    ) AS combined
                                                    GROUP BY repuesto_id, nombre_repuesto
                                                    ORDER BY total_pedidos + total_compras ASC LIMIT 0,10");
                            if ($ventascompras) {
                        ?>
                        <style type="text/css">
                            /* Aplica colores por tercios a las filas */
                            .gradient-table tbody tr {
                                background-color: transparent;
                            }

                            /* Primer tercio de las filas (rojo) */
                            .gradient-table tbody tr:nth-child(n + 1):nth-child(-n + 3) {
                                background-color: #ff00002e;
                            }

                            /* Segundo tercio de las filas (naranja) */
                            .gradient-table tbody tr:nth-child(n + 4):nth-child(-n + 6) {
                                background-color: #ffa5006e;
                            }

                            /* Último tercio de las filas (amarillo) */
                            .gradient-table tbody tr:nth-child(n + 7):nth-child(-n + 9) {
                                background-color: yellow;
                            }
                        </style>
                        <table class="table table-striped table-bordered dt-responsive nowrap w-100 gradient-table" id="monedasTable">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Ventas</th>
                                    <th>Compras</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach ($ventascompras as $row) {
                                        echo '<tr>
                                                <td>'.$row['nombre_repuesto'].'</td>
                                                <td>'.$row['total_pedidos'].'</td>
                                                <td>'.$row['total_compras'].'</td>
                                              </tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                        <?php
                            } else {
                                echo '<div class="alert alert-info">No hay movimientos aun</div>';
                            }
                        ?>
                        <!-- Final de repuestos sin stock -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Proximas entregas -->
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Proximas entregas</h2>
                </div>
                <div class="card shadow-none">
                    <div class="card-body">
                        <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="monedasTable">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th>Bodega</th>
                                    <th>Fecha estimada</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $entregas = $db->query("SELECT
                                                                r.nombre AS nombre_repuesto,
                                                                ir.cantidad AS cantidad,
                                                                ir.fecha_estimada AS fecha_estimada,
                                                                b.nombre AS bodega_nombre
                                                            FROM
                                                                inventario_reserva AS ir
                                                            JOIN
                                                                repuestos AS r ON ir.repuesto_id = r.id
                                                            JOIN
                                                                bodegas AS b ON ir.bodega_id = b.id
                                                            WHERE
                                                                ir.fecha_estimada > CURDATE() AND cantidad > 0 AND ir.bodega_id IN (SELECT bodega_id FROM usuarios_bodegas WHERE usuario_id = '".$_SESSION['usuario_id']."')");
                                    foreach ($entregas as $row) {
                                        echo '<tr>
                                                <td>'.$row['nombre_repuesto'].'</td>
                                                <td>'.$row['cantidad'].'</td>
                                                <td>'.$row['bodega_nombre'].'</td>
                                                <td>'.$row['fecha_estimada'].'</td>
                                              </tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                        <!-- Final de repuestos sin stock -->
                    </div>
                </div>
            </div>
        </div>
        <!--  -->
    </div>

    <!-- Scripts de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js"></script>
    <script>
        $(document).ready(function() {
            $('table.table').DataTable({
                "responsive": true
            });
            var datosRepuestosMarcaModelo = <?php echo (isset(@$datosRepuestosPorMarcaModelo) ? json_encode(@$datosRepuestosPorMarcaModelo) : ''); ?>;
            var etiquetas = [];
            var datos = [];

            for (var i = 0; i < datosRepuestosMarcaModelo.length; i++) {
                etiquetas.push(datosRepuestosMarcaModelo[i].marca + " - " + datosRepuestosMarcaModelo[i].modelo);
                datos.push(datosRepuestosMarcaModelo[i].total_repuestos);
            }

            var ctx = document.getElementById("grafico-repuestos-marca-modelo").getContext("2d");
            var chart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: etiquetas,
                    datasets: [{
                        label: "Total de Repuestos",
                        data: datos,
                        backgroundColor: "rgba(75, 192, 192, 0.2)",
                        borderColor: "rgba(75, 192, 192, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }
                }
            });
        
            var datosRepuestosFecha = <?php echo json_encode($datosRepuestosPorFecha); ?>;
            var etiquetas = [];
            var datos = [];

            for (var i = 0; i < datosRepuestosFecha.length; i++) {
                etiquetas.push(datosRepuestosFecha[i].fecha);
                datos.push(datosRepuestosFecha[i].total_repuestos);
            }

            var ctx = document.getElementById("grafico-repuestos-fecha").getContext("2d");
            var chart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: etiquetas,
                    datasets: [{
                        label: "Total de Repuestos",
                        data: datos,
                        fill: false,
                        borderColor: "rgba(75, 192, 192, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }
                }
            });
        });
    </script>

    <!-- End; Contenido del dashboard -->
    <?php
    }

    include('footer.php');
?>