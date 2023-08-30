<?php 
include 'header.php'; 

// Obtener la lista de marcas
$queryMarcas = "SELECT id, nombre FROM marcas";
$resultMarcas = $db->query($queryMarcas);
$marcas = $resultMarcas;

// Obtener la lista de modelos
$queryModelos = "SELECT id, nombre FROM modelos";
$resultModelos = $db->query($queryModelos);
$modelos = $resultModelos;

// Parámetros de búsqueda
$marcaId = isset($_GET['marca']) ? $_GET['marca'] : "";
$modeloId = isset($_GET['modelo']) ? $_GET['modelo'] : "";
$anioInicio = isset($_GET['anio_inicio']) ? $_GET['anio_inicio'] : "";
$anioFin = isset($_GET['anio_fin']) ? $_GET['anio_fin'] : "";
$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : "";

// Construir la consulta SQL con los parámetros de búsqueda
$query = "SELECT r.nombre AS repuesto, r.precio, r.imagen, GROUP_CONCAT(DISTINCT c.codigo SEPARATOR ',') AS codigos, GROUP_CONCAT(DISTINCT mc.nombre, ', ', m.nombre, ': ', rm.fecha_inicio, ' - ', rm.fecha_fin SEPARATOR '/') AS detalles
          FROM repuestos r
          LEFT JOIN codigos_repuesto c ON r.id = c.id_repuesto
          LEFT JOIN repuesto_modelos rm ON r.id = rm.id_repuesto
          LEFT JOIN modelos m ON rm.id_modelo = m.id
          LEFT JOIN marcas mc ON rm.marca_id = mc.id
          WHERE 1 = 1";

if (!empty($codigo)) {
    $query .= " AND c.codigo LIKE '%" . $codigo . "%'";
} else {

    if (!empty($marcaId)) {
        $query .= " AND mc.id = " . $marcaId;
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
<section class="mt-3">
  <div class="container">
    <main class="card p-3 shadow-2-strong">
      <div class="row">
        <div class="col-lg-3">
          <nav class="nav flex-column nav-pills mb-md-2">
            <a class="nav-link py-2 ps-3 my-0 bg-white" href="#">Categories</a>
          </nav>
        </div>
        <div class="col-lg-9">
          <div class="card-banner h-auto p-5 bg-dark rounded-5" style="height: 350px;">
            <h1 class="display-4 text-center text-white" style="font-size: 25px;">Buscador personalizado</h1>
            <form action="" method="get">
                <div class="form-group">
                    <label class="text-white" for="codigo">Código:</label>
                    <input type="text" class="form-control" id="codigo" name="codigo" value="<?php echo $codigo; ?>">
                    <small class="form-text text-muted">Al llenar este campo anulas todos los demás.</small>
                </div>
                <div class="form-group">
                    <label class="text-white" for="marca">Marca:</label>
                    <select class="form-control" id="marca" name="marca">
                        <option value="">Todas las marcas</option>
                        <?php foreach ($marcas as $marca) : ?>
                            <option value="<?php echo $marca['id']; ?>" <?php echo ($marcaId == $marca['id']) ? "selected" : ""; ?>><?php echo $marca['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="text-white" for="modelo">Modelo:</label>
                    <select class="form-control" id="modelo" name="modelo">
                        <option value="">Todos los modelos</option>
                        <?php foreach ($modelos as $modelo) : ?>
                            <option value="<?php echo $modelo['id']; ?>" <?php echo ($modeloId == $modelo['id']) ? "selected" : ""; ?>><?php echo $modelo['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="text-white" for="rango-anios">Rango de Años:</label>
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
  <div class="container my-5">
    <header class="mb-4">
      <h3>Nuestros productos</h3>
    </header>

    <div class="row">
        <?php foreach ($repuestos as $repuesto) :
            $imagen = ($repuesto['imagen'] ? $repuesto['imagen'] : 'https://wiki.freecad.org/images/thumb/c/c5/PartDesign_Example.png/500px-PartDesign_Example.png');
        ?>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card my-2 shadow-0">
              <a href="#" class="img-wrap">
                <img src="<?php echo $imagen; ?>" class="card-img-top" style="aspect-ratio: 1 / 1"> 
              </a>
              <div class="card-body pt-3">
                <!-- <a href="#!" class="btn btn-light border px-2 pt-2 float-end icon-hover"><i class="fas fa-heart fa-lg px-1 text-secondary"></i></a> -->
                <h5 class="card-title">Q. <?php echo $repuesto['precio']; ?></h5>
                <p class="card-text mb-0"><?php echo $repuesto['repuesto']; ?></p>
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
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <!-- Paginación -->
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
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
                    echo '<li class="page-item"><a class="page-link" href="?codigo=' . @$_GET['codigo'] . '&marca=' . @$_GET['marca'] . '&modelo=' . @$_GET['modelo'] . '&anio_inicio=' . @$_GET['anio_inicio'] . '&anio_fin=' . @$_GET['anio_fin'] . '&page=1">&lt;&lt;</a></li>';
                }
                
                // Enlace a página anterior
                if ($page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?codigo=' . @$_GET['codigo'] . '&marca=' . @$_GET['marca'] . '&modelo=' . @$_GET['modelo'] . '&anio_inicio=' . @$_GET['anio_inicio'] . '&anio_fin=' . @$_GET['anio_fin'] . '&page=' . $prevPage . '">&lt;</a></li>';
                }
                
                // Enlaces a páginas intermedias
                for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++) {
                    echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '"><a class="page-link" href="?codigo=' . @$_GET['codigo'] . '&marca=' . @$_GET['marca'] . '&modelo=' . @$_GET['modelo'] . '&anio_inicio=' . @$_GET['anio_inicio'] . '&anio_fin=' . @$_GET['anio_fin'] . '&page=' . $i . '">' . $i . '</a></li>';
                }
                
                // Enlace de puntos suspensivos y enlace para ir a la última página
                if ($totalPages > 5) {
                    if ($page < $totalPages - 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    echo '<li class="page-item"><a class="page-link" href="?codigo=' . @$_GET['codigo'] . '&marca=' . @$_GET['marca'] . '&modelo=' . @$_GET['modelo'] . '&anio_inicio=' . @$_GET['anio_inicio'] . '&anio_fin=' . @$_GET['anio_fin'] . '&page=' . $totalPages . '">' . $totalPages . '</a></li>';
                }
                
                // Enlace a página siguiente
                if ($page < $totalPages) {
                    echo '<li class="page-item"><a class="page-link" href="?codigo=' . @$_GET['codigo'] . '&marca=' . @$_GET['marca'] . '&modelo=' . @$_GET['modelo'] . '&anio_inicio=' . @$_GET['anio_inicio'] . '&anio_fin=' . @$_GET['anio_fin'] . '&page=' . $nextPage . '">&gt;</a></li>';
                }
                
                // Enlace a página última
                if ($page < $totalPages - 2) {
                    echo '<li class="page-item"><a class="page-link" href="?codigo=' . @$_GET['codigo'] . '&marca=' . @$_GET['marca'] . '&modelo=' . @$_GET['modelo'] . '&anio_inicio=' . @$_GET['anio_inicio'] . '&anio_fin=' . @$_GET['anio_fin'] . '&page=' . $totalPages . '">&gt;&gt;</a></li>';
                }
            }
            ?>
        </ul>
    </nav>

  </div>
</section>
<!-- Products -->

<?php include 'footer.php'; ?>
