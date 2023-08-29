<?php 
include 'header.php'; 

// Obtener la lista de marcas
$queryMarcas = "SELECT id, nombre FROM marcas";
$resultMarcas = $db->query($queryMarcas);
$marcas = $resultMarcas->fetch_assoc();

// Obtener la lista de modelos
$queryModelos = "SELECT id, nombre FROM modelos";
$resultModelos = $db->query($queryModelos);
$modelos = $resultModelos->fetch_assoc();

// Parámetros de búsqueda
$marcaId = isset($_GET['marca']) ? $_GET['marca'] : "";
$modeloId = isset($_GET['modelo']) ? $_GET['modelo'] : "";
$anioInicio = isset($_GET['anio_inicio']) ? $_GET['anio_inicio'] : "";
$anioFin = isset($_GET['anio_fin']) ? $_GET['anio_fin'] : "";
$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : "";

// Construir la consulta SQL con los parámetros de búsqueda
$query = "SELECT r.nombre AS repuesto, GROUP_CONCAT(DISTINCT c.codigo SEPARATOR ',') AS codigos, GROUP_CONCAT(DISTINCT mc.nombre, ', ', m.nombre, ': ', rm.fecha_inicio, ' - ', rm.fecha_fin SEPARATOR '/') AS detalles
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

$result = $db->query($query);

if ($result === false) {
    die("Error en la consulta: " . $db->error);
}

$repuestos = $result->fetch_assoc();
?>

<!-- Contenido principal de la página -->
<div class="container">

    <div class="row">
        <h1>Listado de Repuestos</h1><br>
        <div class="container">
            <form action="" method="get">
                <div class="form-group">
                    <label for="codigo">Código:</label>
                    <input type="text" class="form-control" id="codigo" name="codigo" value="<?php echo $codigo; ?>">
                    <small class="form-text text-muted">Al llenar este campo anulas todos los demás.</small>
                </div>
                <div class="form-group">
                    <label for="marca">Marca:</label>
                    <select class="form-control" id="marca" name="marca">
                        <option value="">Todas las marcas</option>
                        <?php foreach ($marcas as $marca) : ?>
                            <option value="<?php echo $marca['id']; ?>" <?php echo ($marcaId == $marca['id']) ? "selected" : ""; ?>><?php echo $marca['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modelo">Modelo:</label>
                    <select class="form-control" id="modelo" name="modelo">
                        <option value="">Todos los modelos</option>
                        <?php foreach ($modelos as $modelo) : ?>
                            <option value="<?php echo $modelo['id']; ?>" <?php echo ($modeloId == $modelo['id']) ? "selected" : ""; ?>><?php echo $modelo['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rango-anios">Rango de Años:</label>
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
            <br>
            <?php foreach ($repuestos as $repuesto) : ?>
                <div class="col-md-12 mb-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $repuesto['repuesto']; ?></h5>
                            <?php if (!empty($repuesto['codigos'])) : ?>
                                <h6 class="card-subtitle mb-2 text-muted">Códigos:</h6>
                                <?php $codigos = explode(',', $repuesto['codigos']); ?>
                                <?php foreach ($codigos as $codigo) : ?>
                                    <div class="badge badge-success"><?php echo $codigo; ?></div>
                                <?php endforeach; ?>
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
    </div>
</div>

<?php include 'footer.php'; ?>
