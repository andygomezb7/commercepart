<?php 
include 'header.php'; 

// Obtener la lista de marcas
$queryMarcas = "SELECT id, nombre FROM marcas";
$resultMarcas = $db->query($queryMarcas);
$marcas = $resultMarcas->fetch_all(MYSQLI_ASSOC);

// Obtener la lista de modelos
$queryModelos = "SELECT id, nombre FROM modelos";
$resultModelos = $db->query($queryModelos);
$modelos = $resultModelos->fetch_all(MYSQLI_ASSOC);

// Parámetros de búsqueda
$marcaId = isset($_GET['marca']) ? $_GET['marca'] : "";
$modeloId = isset($_GET['modelo']) ? $_GET['modelo'] : "";
$anioInicio = isset($_GET['anio_inicio']) ? $_GET['anio_inicio'] : "";
$anioFin = isset($_GET['anio_fin']) ? $_GET['anio_fin'] : "";

// Construir la consulta SQL con los parámetros de búsqueda
$query = "SELECT r.nombre AS repuesto, rm.fecha_inicio, rm.fecha_fin, m.nombre AS modelo, mc.nombre AS marca
          FROM repuestos r
          LEFT JOIN repuesto_modelos rm ON r.id = rm.id_repuesto
          LEFT JOIN modelos m ON rm.id_modelo = m.id
          LEFT JOIN marcas mc ON rm.marca_id = mc.id
          WHERE 1 = 1";

if (!empty($marcaId)) {
    $query .= " AND mc.id = " . $marcaId;
}

if (!empty($modeloId)) {
    $query .= " AND m.id = " . $modeloId;
}

if (!empty($anioInicio) && !empty($anioFin)) {
    $query .= " AND ((rm.fecha_inicio <= " . $anioFin . " AND rm.fecha_fin >= " . $anioInicio . ") OR (rm.fecha_inicio >= " . $anioInicio . " AND rm.fecha_fin <= " . $anioFin . "))";
}

$result = $db->query($query);

if ($result === false) {
    die("Error en la consulta: " . $db->error);
}

$repuestos = $result->fetch_all(MYSQLI_ASSOC);
?>

<!-- Contenido principal de la página -->
<div class="container">

<div class="row">
    <h1>Listado de Repuestos</h1><br>
    <div class="container">
        <form action="" method="get">
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
                        <?php if (!empty($repuesto['marca'])) : ?>
                            <h6 class="card-subtitle mb-2 text-muted">Marca:</h6>
                            <span class="badge badge-secondary"><?php echo $repuesto['marca']; ?></span>
                        <?php endif; ?>
                        <?php if (!empty($repuesto['modelo'])) : ?>
                            <h6 class="card-subtitle mb-2 text-muted">Modelo:</h6>
                            <span class="badge badge-primary"><?php echo $repuesto['modelo']; ?></span>
                        <?php endif; ?>
                        <?php if (!empty($repuesto['fecha_inicio']) && !empty($repuesto['fecha_fin'])) : ?>
                            <h6 class="card-subtitle mb-2 text-muted">Años Disponibles:</h6>
                            <?php $aniosDisponibles = range($repuesto['fecha_inicio'], $repuesto['fecha_fin']); ?>
                            <?php foreach ($aniosDisponibles as $anio) : ?>
                                <span class="badge badge-info"><?php echo $anio; ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</div>

<?php include 'footer.php'; ?>