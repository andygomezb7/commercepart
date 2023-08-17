<?php
    session_start();
    require_once '../secure/class/User.php';
    // Crear una instancia de la clase User
    $user = new User($db);

    if (!$_SESSION['loggedin'] && !$_SESSION['admin']) {
        header('location: ../index.php');
    }

    $tipo_get = intval($_REQUEST['tipo']);

    // Obtener informacion del usuario logueado
    $thisUser = $user->getUserByEmail($_SESSION['email']);
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
    <div class="container-fluid">
        <div class="row" style="height:100%;">
            <!-- Menú lateral -->
            <nav class="col-md-2 col-lg-2 d-md-block bg-dark sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav nav-admin flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white bg-white rounded mt-3 mb-3 mx-auto" style="max-width: 70%;" href="#"><img class="w-100" src="../styles/images/arsa-png.png" /></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white <?php echo (!$tipo_get?'bg-dark':''); ?>" href="#"><i class="fas fa-home"></i> <span>Inicio</a>
                        </li>
                        <div class="dropdown-divider"></div>
                        <li class="nav-item">
                            <a class="nav-link text-white <?php echo ($tipo_get==2?'bg-dark':''); ?>" href="?tipo=2"><i class="fas fa-users"></i> <span>Usuarios</span></a>
                        </li>
                        <div class="dropdown-divider"></div>
                        <li class="nav-item">
                            <a class="nav-link text-white  <?php echo ($tipo_get==3?'bg-dark':''); ?>" href="?tipo=3"><i class="fas fa-car"></i> <span>Repuestos</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white  <?php echo ($tipo_get==7?'bg-dark':''); ?>" href="?tipo=7"><i class="fas fa-list"></i> <span>Categorías</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white  <?php echo ($tipo_get==5?'bg-dark':''); ?>" href="?tipo=5"><i class="fas fa-car-side"></i> <span>Marcas</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white  <?php echo ($tipo_get==6?'bg-dark':''); ?>" href="?tipo=6"><i class="fas fa-car-side"></i> <span>Modelos</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white  <?php echo ($tipo_get==11?'bg-dark':''); ?>" href="?tipo=11"><i class="fas fa-oil-can"></i> <span>Motores</span></a>
                        </li>
                        <div class="dropdown-divider"></div>
                        <li class="nav-item">
                            <a class="nav-link text-white  <?php echo ($tipo_get==8?'bg-dark':''); ?>" href="?tipo=8"><i class="fas fa-tools"></i> <span>Asignación de marcas y modelos</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white  <?php echo ($tipo_get==10?'bg-dark':''); ?>" href="?tipo=10"><i class="fas fa-tools"></i> <span>Asignación motores a marca/modelo</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white  <?php echo ($tipo_get==9?'bg-dark':''); ?>" href="?tipo=9"><i class="fas fa-tools"></i> <span>Asignación de códigos a repuestos</span></a>
                        </li>
                        <div class="dropdown-divider"></div>
                        <li class="nav-item">
                            <a class="nav-link text-white  <?php echo ($tipo_get==4?'bg-dark':''); ?>" href="?tipo=4"><i class="fas fa-map-pin"></i> <span>Bodegas</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#"><i class="fas fa-location-arrow"></i> <span>Ubicaciones</span></a>
                        </li>
                        <div class="dropdown-divider"></div>
                        <li class="nav-item">
                            <a class="nav-link text-white  <?php echo ($tipo_get==12?'bg-dark':''); ?>" href="?tipo=12"><i class="fas fa-history"></i> <span>Pedidos</span></a>
                        </li>
                        <div class="dropdown-divider"></div>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#"><i class="fas fa-"></i> <span>Habilidades</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#"><i class="fas fa-"></i> <span>Catálogo</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#"><i class="fas fa-"></i> <span>Registro</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#"><i class="fas fa-"></i> <span>Contacto</span></a>
                        </li>
                    </ul>
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Opciones de Administración</span>
                        <a class="d-flex align-items-center text-muted" href="#" aria-label="Agregar opción">
                            <span data-feather="plus-circle"></span>
                        </a>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?tipo=13">Importancion de repuestos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Asignar Pedidos a Empleados</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Reportes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Configuración</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Contenido principal -->
            <main role="main" class="main-content col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <nav class="navbar navbar-fixed navbar-expand-lg navbar-dark bg-dark">
                    <label class="navbar-brand"><a href="javascript:void(0)" onclick="history.back()" class="text-white"><i class="fas fa-caret-square-left"></i></a> <?php echo (is_array($parameters_page) ? $parameters_page['header'] : 'Dashboard'); ?></label>
                    <?php if(is_array($parameters_page)) { ?>
                     <ul class="navbar-nav">
                        <?php
                            if(is_array(@$parameters_page['buttons'])) {
                                foreach ($parameters_page['buttons'] AS $row) {
                                    echo '<li class="nav-item">
                                            <a href="'.$row['action'].'" class="btn btn-'.$row['type'].'">'.$row['name'].'</a>
                                        </li>';
                                }
                            }
                        ?>
                    </ul>
                    <?php } ?>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                      <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                      <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo $thisUser['nombre']; ?>
                          </a>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#">View Profile</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Settings</a>
                            <a class="dropdown-item" href="#">Help</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Log Out</a>
                          </div>
                        </li>
                      </ul>
                    </div>
                </nav>
                <div class="container mt-3 mb-3">