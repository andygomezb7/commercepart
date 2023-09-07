<?php
	include('secure/class/inventario.php');
	$inventario = new Inventario($db);
	//
	$getActualRepuesto = $db->query("SELECT r.id as id_repuesto, r.nombre as nombre_repuesto, r.descripcion, r.imagen, m.nombre as marca_nombre, p.precio_minimo, p.precio_sugerido, p.precio_maximo FROM repuestos AS r LEFT JOIN marcas_codigos AS m ON r.marca_id = m.id LEFT JOIN precios AS p ON r.id = p.repuesto_id AND p.tipo_precio = '3' WHERE r.id = " . $pr)->fetch_assoc();
	if (@$_SESSION['usuario_id']) {
		$isMyBodega = $db->query("SELECT bodega_id FROM usuarios_bodegas WHERE usuario_id = '".$_SESSION['usuario_id']."' AND empresa_id = ". $_SESSION['empresa_id'])->fetch_assoc();
	} else {
		// $isMyBodega = array(
		// 	'bodega_id' => 
		// );
	}

	$inventarioBodega = $inventario->obtenerTotalRepuestosPorBodega(@$isMyBodega['bodega_id'], $pr)->fetch_assoc();
?>
<div class="container">
        	<div class="row mt-4 pt-5">
               <div class="col-md-5 item-photo">
                    <img style="max-width:100%;" src="<?php echo $getActualRepuesto['imagen']; ?>" />
                </div>
                <div class="col-md-7" style="border:0px solid gray">
                    <!-- Datos del vendedor y titulo del producto -->
                    <h3><?php echo $getActualRepuesto['nombre_repuesto']; ?></h3>    
                    <h5 style="color:#337ab7">Marca: <a href="#"><?php echo $getActualRepuesto['marca_nombre']; ?></a> · 
                    								<small style="color:#337ab7">(<?php echo intval(@$inventarioBodega['total']); ?>) En inventario</small>
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
                        <button class="btn btn-success" <?php echo (!$_SESSION['usuario_id'] ? 'disabled' : ''); ?>><span style="margin-right:20px" class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Agregar al carro</button>
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
<style type="text/css">
	ul > li{margin-right:25px;font-weight:lighter;cursor:pointer}
	li.active{border-bottom:3px solid silver;}

	.item-photo{display:flex;justify-content:center;align-items:center;border-right:1px solid #f6f6f6;}
	.menu-items{list-style-type:none;font-size:23px;display:inline-flex;margin-bottom:0;margin-top:20px}
	.btn-success{width:100%;border-radius:0;}
	.section{width:100%;margin-left:-15px;padding:2px;padding-left:15px;padding-right:15px;background:#f8f9f9}
	.title-price{margin-top:30px;margin-bottom:0;color:black}
	.title-attr{margin-top:0;margin-bottom:0;color:black;}
	.btn-minus{cursor:pointer;font-size:20px;display:flex;align-items:center;padding:5px;padding-left:10px;padding-right:10px;border:1px solid gray;border-radius:2px;border-right:0;}
	.btn-plus{cursor:pointer;font-size:20px;display:flex;align-items:center;padding:5px;padding-left:10px;padding-right:10px;border:1px solid gray;border-radius:2px;border-left:0;}
	div.section > div {width:100%;display:inline-flex;}
	div.section > div > input {margin:0;padding-left:5px;font-size:10px;padding-right:5px;max-width:18%;text-align:center;}
	.attr,.attr2{cursor:pointer;margin-right:5px;height:20px;font-size:10px;padding:2px;border:1px solid gray;border-radius:2px;}
	.attr.active,.attr2.active{ border:1px solid orange;}

	@media (max-width: 426px) {
	    .container {margin-top:0px !important;}
	    .container > .row{padding:0 !important;}
	    .container > .row > .col-xs-12.col-sm-5{
	        padding-right:0 ;    
	    }
	    .container > .row > .col-xs-12.col-sm-9 > div > p{
	        padding-left:0 !important;
	        padding-right:0 !important;
	    }
	    .container > .row > .col-xs-12.col-sm-9 > div > ul{
	        padding-left:10px !important;
	        
	    }            
	    .section{width:104%;}
	    .menu-items{padding-left:0;}
	}
</style>