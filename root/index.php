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
    <h1 class="mt-4">Dashboard</h1>
    <p>Bienvenido al panel de administración.</p>

    <div class="container">

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Codigos en alerta</h5>
                        <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="monedasTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Tipo de Cambio a Quetzal</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
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