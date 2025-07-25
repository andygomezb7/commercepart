<?php
	include('secure/class/inventario.php');
	$inventario = new Inventario($db);
	//
    $query = "SELECT r.id as id_repuesto, r.nombre as nombre_repuesto, r.descripcion, r.imagen, m.nombre as marca_nombre, p.precio_minimo, p.precio_sugerido, p.precio_maximo, SUM(coalesce(movimientos.inventario, 0)) AS total_stock FROM repuestos AS r LEFT JOIN marcas_codigos AS m ON r.marca_id = m.id LEFT JOIN precios AS p ON r.id = p.repuesto_id AND p.tipo_precio = '3' LEFT JOIN (SELECT
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
                    b.id, r.id) AS movimientos ON r.id = movimientos.repuesto_id WHERE r.id = " . $pr;

	$getActualRepuesto = $db->query($query)->fetch_assoc();
	if (@$_SESSION['usuario_id']) {
		$isMyBodega = $db->query("SELECT bodega_id FROM usuarios_bodegas WHERE usuario_id = '".$_SESSION['usuario_id']."' AND empresa_id = ". $_SESSION['empresa_id'])->fetch_assoc();
	} else {
		// $isMyBodega = array(
		// 	'bodega_id' => 
		// );
	}

    // @$isMyBodega['bodega_id']
    if ($getActualRepuesto['nombre_repuesto']) {
?>
<div class="container">
        	<div class="row mt-4 pt-5">
               <div class="col-md-5 item-photo">
<!--                		<ul id="glasscase" class="gc-start">
	                    <li><img src="https://source.unsplash.com/featured?technology" alt="Text" data-gc-caption="Your caption text" /></li>
	                    <li><img src="https://source.unsplash.com/featured?Technology" alt="Text" /></li>
	                    <li><img src="https://source.unsplash.com/featured?tEchnology" alt="Text" /></li>
	                    <li><img src="https://source.unsplash.com/featured?teChnology" alt="Text" /></li>
	                    <li><img src="https://source.unsplash.com/featured?TecHnology" alt="Text" /></li>
	                    <li><img src="https://source.unsplash.com/featured?TechNology" alt="Text" /></li>
	                </ul> -->
                    <img style="max-width:100%;" src="<?php echo $getActualRepuesto['imagen']; ?>" />
                </div>
                <div class="col-md-7" style="border:0px solid gray">
                    <!-- Datos del vendedor y titulo del producto -->
                    <h3><?php echo $getActualRepuesto['nombre_repuesto']; ?></h3>    
                    <h5 style="color:#337ab7">Marca: <a href="#"><?php echo $getActualRepuesto['marca_nombre']; ?></a> · 
                    								<small style="color:#337ab7">(<?php echo intval($getActualRepuesto['total_stock']); ?>) En inventario</small>
                    </h5>
        
                    <!-- Precios -->
                    <h6 class="title-price"><small>PRECIO</small></h6>
                    <h3 style="margin-top:0px;">Q <?php echo $getActualRepuesto['precio_sugerido']; ?></h3>
        
                    <!-- Detalles especificos del producto -->
                    <div class="section" style="padding-bottom:20px;">
                        <h6 class="title-attr"><small>CANTIDAD</small></h6>                    
                        <div class="mt-3">
                            <div class="btn-minus"><span class="fas fa-minus"></span></div>
                            <input value="1" />
                            <div class="btn-plus"><span class="fas fa-plus"></span></div>
                        </div>
                    </div>                
        
                    <!-- Botones de compra -->
                    <div class="section" style="padding-bottom:20px;">
                        <button class="btn btn-success" <?php echo (!@$_SESSION['usuario_id'] ? 'disabled' : ''); ?>><span style="margin-right:20px" class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Agregar al carro</button>
                        <!-- <h6><a href="#"><span class="glyphicon glyphicon-heart-empty" style="cursor:pointer;"></span> Agregar a lista de deseos</a></h6> -->
                    </div>                                        
                </div>                              
        
                <div class="col-md-12 mt-5">
                    <ul class="menu-items">
                        <li class="active">Detalle del producto</li>
                    </ul>
                    <div style="width:100%;border-top:1px solid silver">
                        <p style="padding:15px;">
                            <?php echo $getActualRepuesto['descripcion']; ?>
                        </p>
                    </div>
                </div>		
            </div>
        </div>
<script type="text/javascript" src="scripts/zoomproduct.js"></script>
<link rel="stylesheet" type="text/css" href="styles/product.css">
<?php } else { ?>
    <div class="page-wrap d-flex flex-row align-items-center my-5 py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 text-center">
                    <span class="display-1 d-block">404</span>
                    <div class="mb-4 lead">The page you are looking for was not found.</div>
                    <a href="#" class="btn btn-link">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>