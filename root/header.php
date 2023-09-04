<?php
    session_start();
    require_once '../secure/class/user.php';
    // Crear una instancia de la clase User
    $user = new User($db);

    if (!$_SESSION['loggedin'] && !$_SESSION['admin']) {
        header('location: ../index.php');
    }

    $tipo_get = intval(@$_REQUEST['tipo']);

    // Obtener informacion del usuario logueado
    $thisUser = $user->getUserByEmail($_SESSION['email']);

    $principalMenu = array(
                    array(
                        array(
                            "type" => "image",
                            "link" => "?dashboard",
                            "text" => "../styles/images/arsa-png.png",
                            "icon" => "",
                        ),
                        array(
                            "type" => "text",
                            "link" => "?dashboard",
                            "text" => "Inicio",
                            "icon" => "home",
                        ),
                        array(
                            "type" => "text",
                            "link" => 2,
                            "text" => "Usuarios",
                            "icon" => "users",
                        ),
                    ),
                    'Inventario',
                    array(
                        array(
                            "type" => "text",
                            "link" => 3,
                            "text" => "Códigos",
                            "icon" => "car",
                        ),
                        array(
                            "type" => "text",
                            "link" => 14,
                            "text" => "Orden de compra",
                            "icon" => "boxes",
                        ),
                        array(
                            "type" => "text",
                            "link" => 18,
                            "text" => "Precios",
                            "icon" => "dollar-sign",
                        ),
                        array(
                            "type" => "text",
                            "link" => 19,
                            "text" => "Monedas",
                            "icon" => "search-dollar",
                        ),
                        array(
                            "type" => "text",
                            "link" => 17,
                            "text" => "Marcas de códigos",
                            "icon" => "clipboard-list",
                        ),
                        array(
                            "type" => "text",
                            "link" => 7,
                            "text" => "Categorías",
                            "icon" => "list",
                        ),
                    ),
                    'Bodegas',
                    array(
                        array(
                            "type" => "text",
                            "link" => 4,
                            "text" => "Bodegas",
                            "icon" => "warehouse",
                        ),
                        array(
                            "type" => "text",
                            "link" => 15,
                            "text" => "Proveedores",
                            "icon" => "sitemap",
                        ),
                        array(
                            "type" => "text",
                            "link" => 16,
                            "text" => "Clientes",
                            "icon" => "users",
                        ),
                        // array(
                        //     "type" => "text",
                        //     "link" => 1,
                        //     "text" => "Ubicaciones",
                        //     "icon" => "location-arrow",
                        // ),
                        array(
                            "type" => "text",
                            "link" => 12,
                            "text" => "Pedidos",
                            "icon" => "history",
                        ),
                    ),
                    'Buscador personalizado',
                        array(
                            array(
                                "type" => "text",
                                "link" => 5,
                                "text" => "Marcas de autos",
                                "icon" => "car-side",
                            ),
                            array(
                                "type" => "text",
                                "link" => 6,
                                "text" => "Modelos de autos",
                                "icon" => "car-side",
                            ),
                            array(
                                "type" => "text",
                                "link" => 11,
                                "text" => "Motores de autos",
                                "icon" => "oil-can",
                            ),
                        ),
                        'Asignaciones',
                        array(
                            array(
                                "type" => "text",
                                "link" => 8,
                                "text" => "Asignación de marcas y modelos",
                                "icon" => "tools",
                            ),
                            array(
                                "type" => "text",
                                "link" => 10,
                                "text" => "Asignación de motores a marca/modelos",
                                "icon" => "tools",
                            ),
                            // array(
                            //     "type" => "text",
                            //     "link" => 9,
                            //     "text" => "Asignación de códigos a repuestos",
                            //     "icon" => "tools",
                            // ),
                        ),
                    'Rangos y permisos',
                    array(
                        array(
                            "type" => "text",
                            "link" => 1,
                            "text" => "Habilidades",
                            "icon" => "user-slash",
                        ),
                        array(
                            "type" => "text",
                            "link" => 1,
                            "text" => "Rangos",
                            "icon" => "user-lock",
                        ),
                    ),
                    'Opciones de administración',
                    array(
                        array(
                            "type" => "text",
                            "link" => 13,
                            "text" => "Importación de repuestos desde excel",
                            "icon" => "file-export",
                        ),
                        array(
                            "type" => "text",
                            "link" => 1,
                            "text" => "Reportes",
                            "icon" => "file",
                        ),
                        array(
                            "type" => "text",
                            "link" => 1,
                            "text" => "Configuración",
                            "icon" => "tools",
                        ),
                    ),
    );
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../styles/core.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
    <title><?php echo (is_array($parameters_page) ? $parameters_page['title'] : 'Dashboard'); ?></title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>

    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Alpha Parts Software</a>
        <!-- <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search"> -->
        <button class="navbar-toggler border-0 d-none d-md-block" type="button" data-toggle="collapse" data-target="#sidebarCollapse" aria-controls="sidebarCollapse" aria-expanded="false" aria-label="Toggle sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <button class="navbar-toggler border-0 d-md-none d-bg-block" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <ul class="navbar-nav flex-row ml-3">
            <?php if (!is_array($parameters_page)) { ?>
                <li class="nav-item mr-2 d-none d-md-block">
                    <a href="javascript:void(0)" onclick="history.back()" class="nav-link text-white">
                        <i class="fas fa-angle-right"></i> Regresar</a>
                </li>
            <?php } ?>
            <li class="nav-item mr-2">
                <a href="?tipo=<?php echo $tipo_get; ?>" class="nav-link text-white">
                    <i class="fas fa-angle-right"></i> <?php echo (is_array($parameters_page) ? $parameters_page['header'] : 'Dashboard'); ?></a>
            </li>
            <?php if(is_array($parameters_page)) { ?>
                    <?php
                        if(is_array(@$parameters_page['buttons'])) {
                            foreach ($parameters_page['buttons'] AS $row) {
                                echo '<li class="nav-item mr-2">
                                        <a href="'.$row['action'].'" class="nav-link '.$row['type'].'">'.$row['name'].'</a>
                                    </li>';
                            }
                        }
                    ?>
            <?php } ?>
        </ul>

        <div class="collapse navbar-collapse collapseFullMenu" id="navbarCollapse">
            <?php
                foreach ($principalMenu as $object) {
                    if (is_array($object)) {
                        echo '<ul class="nav flex-column">';
                        foreach ($object as $option) {
                            if ($option['type']=='text') {
                                echo '<li class="nav-item">
                                        <a class="nav-link px-3 py-3 '.(is_numeric($option['link']) && $option['link']==$tipo_get || !is_numeric($option['link']) && !$tipo_get ?'active':'').'" 
                                            href="'.(is_numeric($option['link'])?'?tipo='.$option['link']:$option['link']).'">
                                                '.(isset($option['icon'])?'<i class="fas fa-'.$option['icon'].'"></i>':'').' 
                                                <span>'.$option['text'].'</span>
                                        </a>
                                    </li>';
                            } else if ($option['type']=='image') {
                                echo '<li class="nav-item">
                                        <a class="nav-link bg-white rounded mt-3 mb-3 mx-auto" style="max-width: 46%;" href="'.$option['link'].'"><img class="w-100" src="'.$option['text'].'" /></a>
                                    </li>';
                            }
                        }
                        echo '</ul>';
                    } else {
                        echo "<h6 class=\"sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted\">
                              <span>$object</span>
                            </h6>";
                    }
                }
            ?>
        </div>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="dropdown01" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php echo $thisUser['nombre']; ?>
              </a>
              <!-- dropdown-menu-right -->
              <div class="dropdown-menu" aria-labelledby="dropdown01">
                <a class="dropdown-item" href="#">View Profile</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Settings</a>
                <a class="dropdown-item" href="#">Help</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Log Out</a>
              </div>
            </li>
        </ul>
    </nav>

    <div class="container-fluid">
        <div class="row" style="height:100%;">
            <!-- col-md-2 col-lg-2 d-md-block bg-dark sidebar  -->
            <nav class="col-md-2 d-bg-none d-md-block bg-light sidebar collapse show d-none d-md-block" id="sidebarCollapse">
                <div class="sidebar-sticky">
                    <?php
                        foreach ($principalMenu as $object) {
                            if (is_array($object)) {
                                echo '<ul class="nav flex-column">';
                                foreach ($object as $option) {
                                    if ($option['type']=='text') {
                                        echo '<li class="nav-item">
                                                <a class="nav-link 
                                                '.(is_numeric($option['link']) && $option['link']==$tipo_get || !is_numeric($option['link']) && !$tipo_get ?'active':'').'" 
                                                    href="'.(is_numeric($option['link'])?'?tipo='.$option['link']:$option['link']).'">
                                                        '.(isset($option['icon'])?'<i class="fas fa-'.$option['icon'].'"></i>':'').' 
                                                        <span>'.$option['text'].'</span>
                                                </a>
                                            </li>';
                                    } else if ($option['type']=='image') {
                                        echo '<li class="nav-item">
                                                <a class="nav-link bg-white rounded mt-3 mb-3 mx-auto" style="max-width: 70%;" href="'.$option['link'].'"><img class="w-100" src="'.$option['text'].'" /></a>
                                            </li>';
                                    }
                                }
                                echo '</ul>';
                            } else {
                                echo "<h6 class=\"sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted\">
                                      <span>$object</span>
                                    </h6>";
                            }
                        }
                    ?>
                </div>
            </nav>

            <!-- main-content col-md-9 ml-sm-auto col-lg-10 px-md-4 -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="container mt-3 mb-5 pb-4">