<?php 
session_start();

$pr = @$_REQUEST['pr'];
$pageview = @$_REQUEST['p'];
if (!$pr && !$pageview) {
    include 'adminlogin.php';
} else if ($pr) {
    include 'header.php'; 
    include('product.php');
    include 'footer.php';
} else if ($pageview == 'brands') {
    include 'header.php'; 

    include 'footer.php';
} else if($pageview == 'store') {
    include 'header.php'; 
    // Obtener la lista de marcas
    $queryMarcas = "SELECT id, nombre FROM marcas WHERE empresa_id = " . $getCompany['id'];
    $resultMarcas = $db->query($queryMarcas);
    $marcas = $resultMarcas;

    // Obtener la lista de modelos
    $queryModelos = "SELECT id, nombre FROM modelos WHERE empresa_id = " . $getCompany['id'];
    $resultModelos = $db->query($queryModelos);
    $modelos = $resultModelos;

    // Obtener la lista de categorias
    $queryCategorias = "SELECT id, nombre FROM categorias WHERE empresa_id = " . $getCompany['id'];
    $resultCategorias = $db->query($queryCategorias);
    $categorias = $resultCategorias;

    // Parámetros de búsqueda
    $marcaId = isset($_GET['marca']) ? $_GET['marca'] : "";
    $modeloId = isset($_GET['modelo']) ? $_GET['modelo'] : "";
    $anioInicio = isset($_GET['anio_inicio']) ? $_GET['anio_inicio'] : "";
    $anioFin = isset($_GET['anio_fin']) ? $_GET['anio_fin'] : "";
    $codigo = isset($_GET['codigo']) ? $_GET['codigo'] : "";
    $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : "";

    // Construir la consulta SQL con los parámetros de búsqueda
    $query = "SELECT r.id AS repuesto_id, r.nombre AS repuesto, r.precio, r.imagen, GROUP_CONCAT(DISTINCT c.codigo SEPARATOR ',') AS codigos, GROUP_CONCAT(DISTINCT mc.nombre, ', ', m.nombre, ': ', rm.fecha_inicio, ' - ', rm.fecha_fin SEPARATOR '/') AS detalles, ma.nombre AS marca_codigo, p.precio_minimo, p.precio_sugerido, p.precio_maximo, cg.nombre AS nombre_categoria
              FROM repuestos r
              LEFT JOIN codigos_repuesto c ON r.id = c.id_repuesto
              LEFT JOIN repuesto_modelos rm ON r.id = rm.id_repuesto
              LEFT JOIN modelos m ON rm.id_modelo = m.id
              LEFT JOIN marcas mc ON rm.marca_id = mc.id
              LEFT JOIN marcas_codigos ma ON r.id = ma.id
              LEFT JOIN categorias cg ON r.categoria_id = cg.id 
              LEFT JOIN precios p ON r.id = p.repuesto_id AND p.tipo_precio = '3'
              WHERE 1 = 1 AND r.empresa_id = " . $getCompany['id'];

    if (!empty($codigo)) {
        $query .= " AND c.codigo LIKE '%" . $codigo . "%'";
    } else {

        if (!empty($marcaId)) {
            $query .= " AND mc.id = " . $marcaId;
        }

        if (!empty($categoria)) {
            $query .= " AND r.categoria_id = " . $categoria;
        }

        if (!empty($modeloId)) {
            $query .= " AND m.id = " . $modeloId;
        }


        if (!empty($anioInicio) && !empty($anioFin)) {
            $query .= " AND ((rm.fecha_inicio <= " . $anioFin . " AND rm.fecha_fin >= " . $anioInicio . ") OR (rm.fecha_inicio >= " . $anioInicio . " AND rm.fecha_fin <= " . $anioFin . "))";
        }
    }

    $query .= " GROUP BY r.id";

    $resultTotal = $db->query($query);

    // Paginación
    $pageSize = 12;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * $pageSize;

    $query .= " LIMIT $pageSize OFFSET $offset";

    $result = $db->query($query);

    if ($result === false) {
        die("Error en la consulta: " . $db->error);
    }

    $repuestos = $result;
    ?>

    <!--  intro  -->
    <section class="pt-0">
      <div class="bg-white d-flex py-3">
        <div class="container">
            <a class="btn btn-dark text-white rounded-0" href="javascript:void(0)" onclick="$('.personalizeSearch').toggle()">Busqueda personalizada <i class="fas fa-tools"></i></a>
        </div>
      </div>
      <div class="container">
        <main class="card personalizeSearch pt-0 shadow-2-strong mb-0 border rounded-0 shadow-none" style="display:none;">
          <div class="row">
    <!--         <div class="col-lg-3">
              <nav class="nav flex-column nav-pills mb-md-2">
                <a class="nav-link py-2 ps-3 my-0 bg-white" href="#">Categories</a>
              </nav>
            </div> -->
            <div class="col-lg-12">
              <div class="card-banner h-auto p-5 rounded-5" style="height: 350px;">
                <h1 class="display-4 text-center text-dark" style="font-size: 25px;">Buscador de códigos</h1>
                <form action="" method="get">
                    <input type="hidden" name="p" value="store" />
                    <div class="form-group">
                        <label class="text-dark" for="codigo">Código:</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" value="<?php echo $codigo; ?>">
                        <small class="form-text text-muted">Al llenar este campo anulas todos los demás.</small>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="text-dark" for="marca">Marca:</label>
                            <select class="form-control" id="marca" name="marca">
                                <option value="">Todas las marcas</option>
                                <?php foreach ($marcas as $marca) : ?>
                                    <option value="<?php echo $marca['id']; ?>" <?php echo ($marcaId == $marca['id']) ? "selected" : ""; ?>><?php echo $marca['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="text-dark" for="modelo">Modelo:</label>
                            <select class="form-control" id="modelo" name="modelo">
                                <option value="">Todos los modelos</option>
                                <?php foreach ($modelos as $modelo) : ?>
                                    <option value="<?php echo $modelo['id']; ?>" <?php echo ($modeloId == $modelo['id']) ? "selected" : ""; ?>><?php echo $modelo['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="text-dark" for="categoria">Categorias:</label>
                        <select class="form-control" id="categoria" name="categoria">
                            <option value="">Todas las categorias</option>
                            <?php foreach ($categorias as $cat) : ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($categoria == $cat['id']) ? "selected" : ""; ?>><?php echo $cat['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="text-dark" for="rango-anios">Rango de Años:</label>
                        <div class="row">
                            <div class="col">
                                <input type="number" class="form-control" id="anio-inicio" name="anio_inicio" placeholder="Año inicio" value="<?php echo $anioInicio; ?>">
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" id="anio-fin" name="anio_fin" placeholder="Año fin" value="<?php echo $anioFin; ?>">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </form>
              </div>
            </div>
          </div>
        </main>
      </div>
      <!-- container end.// -->
    </section>
    <!-- intro -->

    <!-- Products -->
    <section>
      <div class="container my-3">
        <header class="mb-4">
            <h3 class="pb-3 mb-4 font-italic border-bottom">
                <?php
                    echo (!empty($marcaId) || !empty($modeloId) || !empty($anioInicio) || !empty($anioFin) || !empty($codigo) || !empty($categoria) ? "Resultados ($resultTotal->num_rows)" : 'Códigos disponibles')
                ?>
            </h3>
        </header>

        <div class="row">
            <?php foreach ($repuestos as $repuesto) :
                $imagen = ($repuesto['imagen'] ? $repuesto['imagen'] : 'https://wiki.freecad.org/images/thumb/c/c5/PartDesign_Example.png/500px-PartDesign_Example.png');
            ?>
            <!-- col-lg-3 -->
            <div class="col-md-6 col-sm-6">
                <div class="card flex-md-row mb-4 shadow-sm h-md-250 border p-2 flex-column-reverse flex-md-row">
                    <div class="ribbon base"><span><?php echo $repuesto['nombre_categoria']; ?></span></div>

                    <div class="card-body d-flex flex-column align-items-start">
                        <strong class="d-inline-block mb-2 text-primary"><?php echo $repuesto['marca_codigo']; ?></strong>
                        <h3 class="mb-0">
                            <a class="text-dark" href="?pr=<?php echo $repuesto['repuesto_id']; ?>"><?php echo $repuesto['repuesto']; ?></a>
                        </h3>
                        <div class="mb-1 text-muted">Q <?php echo $repuesto['precio_sugerido']; ?></div>
                        <p class="card-text mb-auto">
                            <?php if (!empty($repuesto['codigos'])) : ?>
                                <h6 class="card-subtitle my-2 text-muted">Códigos:</h6>
                                <div class="mb-2">
                                    <?php $codigos = explode(',', $repuesto['codigos']); ?>
                                    <?php foreach ($codigos as $codigo) : ?>
                                        <div class="badge badge-secondary"><?php echo $codigo; ?></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif;

                                $detalles = array_filter(explode('/', $repuesto['detalles']));
                                if (count($detalles)>0) echo '<h6 class="card-subtitle mb-2 text-muted">Marcas, modelos y años:</h6>';
                                foreach ($detalles as $detalle) : 
                                    echo "<div class=\"badge badge-danger\">$detalle</div>";
                                endforeach;

                            ?>
                        </p>
                        <a class="btn btn-outline-info" href="?pr=<?php echo $repuesto['repuesto_id']; ?>">Agregar al carrito &nbsp;<i class="fas fa-cart-plus"></i></a>
                    </div>
                    <img class="card-img-right flex-auto d-lg-block mx-auto" alt="Thumbnail [200x250]" style="width: 200px; height: 250px;" src="<?php echo $imagen; ?>" data-holder-rendered="true">
                </div>

    <!--             <div class="card my-2 shadow-0">
                  <a href="#" class="img-wrap">
                    <img src="<?php echo $imagen; ?>" class="card-img-top" style="aspect-ratio: 1 / 1"> 
                  </a>
                  <div class="card-body pt-3">
                    <p class="card-text mb-0"><a href="?pr=<?php echo $repuesto['repuesto_id']; ?>"><?php echo $repuesto['repuesto']; ?></a></p>
                     <?php if (!empty($repuesto['codigos'])) : ?>
                        <h6 class="card-subtitle my-2 text-muted">Códigos:</h6>
                        <div class="mb-2">
                            <?php $codigos = explode(',', $repuesto['codigos']); ?>
                            <?php foreach ($codigos as $codigo) : ?>
                                <div class="badge badge-success"><?php echo $codigo; ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <h6 class="card-subtitle mb-2 text-muted">Marcas, modelos y años:</h6>
                     <?php $detalles = explode('/', $repuesto['detalles']);
                            foreach ($detalles as $detalle) : ?>
                        <div class="badge badge-success"><?php echo $detalle; ?></div>
                    <?php endforeach; ?>
                  </div>
                </div> -->
            </div>
            <?php endforeach; ?>
        </div>
        <!-- Paginación -->
        <nav aria-label="Page navigation example">
            <ul class="pagination pagination-lg justify-content-center">
                <?php
                $totalPages = ceil($resultTotal->num_rows / $pageSize);
                
                if ($totalPages <= 1) {
                    // No se muestra paginación si solo hay una página
                    echo '<li class="page-item active"><span class="page-link">1</span></li>';
                } else {
                    $prevPage = $page - 1;
                    $nextPage = $page + 1;
                    
                    // Enlace a página primera
                    if ($page > 3) {
                        echo '<li class="page-item"><a class="page-link" href="?codigo=' . @$_GET['codigo'] . '&marca=' . @$_GET['marca'] . '&modelo=' . @$_GET['modelo'] . '&categoria=' . $categoria . '&anio_inicio=' . @$_GET['anio_inicio'] . '&anio_fin=' . @$_GET['anio_fin'] . '&page=1">&lt;&lt;</a></li>';
                    }
                    
                    // Enlace a página anterior
                    if ($page > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?codigo=' . @$_GET['codigo'] . '&marca=' . @$_GET['marca'] . '&modelo=' . @$_GET['modelo'] . '&categoria=' . $categoria . '&anio_inicio=' . @$_GET['anio_inicio'] . '&anio_fin=' . @$_GET['anio_fin'] . '&page=' . $prevPage . '">&lt;</a></li>';
                    }
                    
                    // Enlaces a páginas intermedias
                    for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++) {
                        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '"><a class="page-link" href="?codigo=' . @$_GET['codigo'] . '&marca=' . @$_GET['marca'] . '&modelo=' . @$_GET['modelo'] . '&categoria=' . $categoria . '&anio_inicio=' . @$_GET['anio_inicio'] . '&anio_fin=' . @$_GET['anio_fin'] . '&page=' . $i . '">' . $i . '</a></li>';
                    }
                    
                    // Enlace de puntos suspensivos y enlace para ir a la última página
                    if ($totalPages > 5) {
                        if ($page < $totalPages - 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?codigo=' . @$_GET['codigo'] . '&marca=' . @$_GET['marca'] . '&modelo=' . @$_GET['modelo'] . '&categoria=' . $categoria . '&anio_inicio=' . @$_GET['anio_inicio'] . '&anio_fin=' . @$_GET['anio_fin'] . '&page=' . $totalPages . '">' . $totalPages . '</a></li>';
                    }
                    
                    // Enlace a página siguiente
                    if ($page < $totalPages) {
                        echo '<li class="page-item"><a class="page-link" href="?codigo=' . @$_GET['codigo'] . '&marca=' . @$_GET['marca'] . '&modelo=' . @$_GET['modelo'] . '&categoria=' . $categoria . '&anio_inicio=' . @$_GET['anio_inicio'] . '&anio_fin=' . @$_GET['anio_fin'] . '&page=' . $nextPage . '">&gt;</a></li>';
                    }
                    
                    // Enlace a página última
                    if ($page < $totalPages - 2) {
                        echo '<li class="page-item"><a class="page-link" href="?codigo=' . @$_GET['codigo'] . '&marca=' . @$_GET['marca'] . '&modelo=' . @$_GET['modelo'] . '&categoria=' . $categoria . '&anio_inicio=' . @$_GET['anio_inicio'] . '&anio_fin=' . @$_GET['anio_fin'] . '&page=' . $totalPages . '">&gt;&gt;</a></li>';
                    }
                }
                ?>
            </ul>
        </nav>

      </div>
    </section>
    <!-- Products -->

    <!-- Our -->
    <section>
      <div class="container">
        <div class="px-4 pt-3 border">
          <div class="row pt-1">
            <div class="col-lg-3 col-md-6 mb-3 d-flex">
              <div class="d-flex align-items-center">
                <div class="badge badge-warning p-2 rounded-4 me-3">
                  <i class="fas fa-thumbs-up fa-2x fa-fw"></i>
                </div>
                <span class="info ml-2">
                  <h6 class="mb-0">Precios razonables</h6>
                  <p class="mb-0">Lo que finalmente buscas</p>
                </span>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 d-flex">
              <div class="d-flex align-items-center">
                <div class="badge badge-warning p-2 rounded-4 me-3">
                  <i class="fas fa-plane fa-2x fa-fw"></i>
                </div>
                <span class="info ml-2">
                  <h6 class="mb-0">Envios nacionales</h6>
                  <p class="mb-0">La coordinación y entrega correcta</p>
                </span>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 d-flex">
              <div class="d-flex align-items-center">
                <div class="badge badge-warning p-2 rounded-4 me-3">
                  <i class="fas fa-star fa-2x fa-fw"></i>
                </div>
                <span class="info ml-2">
                  <h6 class="mb-0">La mejor calidad</h6>
                  <p class="mb-0">La mejor calidad para usted</p>
                </span>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 d-flex">
              <div class="d-flex align-items-center">
                <div class="badge badge-warning p-2 rounded-4 me-3">
                  <i class="fas fa-phone-alt fa-2x fa-fw"></i>
                </div>
                <span class="info ml-2">
                  <h6 class="mb-0">Centro de ayuda</h6>
                  <p class="mb-0">Solucionamos tus dudas</p>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Our -->

    <style type="text/css">
        /*Personalize select2*/
        .select2-container {
            width: 100%!important;
        }
    </style>

<?php
        include 'footer.php';
    }
?>
