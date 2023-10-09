<?php
  // Obtener la lista de marcas
  $resultMarcas = $db->query("SELECT id, nombre, imagen FROM marcas_codigos WHERE empresa_id = " . $getCompany['id']);
  $marcas = $resultMarcas;
?>
<div class="container my-5">
  <div class="row">
    <?php foreach ($marcas AS $marca) { ?>
      <div class="col-lg-4">
        <img class="rounded-circle" src="<?php echo $marca['imagen']; ?>" alt="Generic placeholder image" width="140" height="140">
        <h2><?php echo $marca['nombre']; ?></h2>
        <!-- <p>.</p> -->
        <p><a class="btn btn-secondary" href="?p=store&repmarca=<?php echo $marca['id']; ?>" role="button">Ver m&aacute;s »</a></p>
      </div><!-- /.col-lg-4 -->
    <?php } ?>
  </div>
</div>