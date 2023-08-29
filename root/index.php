<?php
    $tipo =  filter_input(INPUT_GET, 'tipo', FILTER_SANITIZE_EMAIL);
    if ($tipo == 2) {
        $parameters_page = array(
            'title' => 'Usuarios',
            'header' => 'Agregar/editar usuarios'
        );
        include('header.php');
        include('usuarios.php');
    } else if ($tipo == 3) {
        $parameters_page = array(
            'title' => 'Repuestos',
            'header' => 'Agregar/editar repuestos',
            'buttons' => array(
                array("name" => "<i class='fas fa-plus'></i> Agregar repuesto", "action" => "?tipo=3&agregar=1", "type" => "success")
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
            'header' => 'Creación de pedidos'
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
            'title' => 'Agregar Stock',
            'header' => 'Agregar stock'
        );
        include('header.php');
        include('stock.php');
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
    } else {
        $parameters_page = array(
            'title' => 'Tablero',
            'header' => 'Tablero'
        );
        include('header.php');
    ?>
    <!-- Contenido del dashboard -->
    <h1 class="mt-4">Dashboard</h1>
    <p>Bienvenido al panel de administración.</p>

    <div class="container">

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Repuestos Agregados Diariamente</h5>
                        <?php
                        // Consulta SQL para obtener el conteo de repuestos por fecha de creación
                        $queryRepuestosPorFecha = "SELECT DATE(fecha_creacion) AS fecha, COUNT(*) AS total_repuestos
                                                   FROM repuestos
                                                   GROUP BY DATE(fecha_creacion)";
                        $resultadoRepuestosPorFecha = $db->query($queryRepuestosPorFecha);
                        $datosRepuestosPorFecha = $resultadoRepuestosPorFecha;
                        ?>

                        <canvas id="grafico-repuestos-fecha"></canvas>

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
            var datosRepuestosMarcaModelo = <?php echo json_encode($datosRepuestosPorMarcaModelo); ?>;
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