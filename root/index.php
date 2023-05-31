<?php
     include('header.php');

     $tipo =  filter_input(INPUT_GET, 'tipo', FILTER_SANITIZE_EMAIL);
     if (!$tipo) {
?>
    <!-- Contenido del dashboard -->
    <h1 class="mt-4">Dashboard</h1>
    <p>Bienvenido al panel de administración.</p>
<?php
    } else if ($tipo == 2) {
        include('usuarios.php');
    } else if ($tipo == 3) {
        include('repuestos.php');
    } else if ($tipo == 4) {
        include('bodegas.php');
    } else if ($tipo == 5) {
        include('marcas.php');
    } else if ($tipo == 6) {
        include('modelos.php');
    } else if ($tipo == 7) {
        include('categorias.php');
    } else if ($tipo == 8) {
        include('modelosmarcas.php');
    }

    include('footer.php');
?>