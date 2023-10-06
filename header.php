<?php
  session_start();
  require_once 'secure/class/user.php';

  // Crear una instancia de la clase User
  $user = new User($db);
  if ($user->validateSession()) {
    // Obtener informacion del usuario logueado
    $thisUser = $user->getUserByEmail(@$_SESSION['email']);
    $getCompany = $db->query("SELECT id, nombre, image, telefono,direccion,email FROM empresas WHERE id = " . $_SESSION['empresa_id'])->fetch_assoc();
  } else {
    $getCompany = $db->query("SELECT id, nombre, image, telefono,direccion,email FROM empresas WHERE domain = '" . $_SERVER['HTTP_HOST'] . "'")->fetch_assoc();
    if (!@$getCompany['id']) {
      $getCompany = $db->query("SELECT id, nombre, image, telefono,direccion,email FROM empresas WHERE id = 1")->fetch_assoc();
    }
    $_SESSION['empresa_id'] = $getCompany['id'];
  }

  $codigo_get = @$_GET['codigo'];
  $categoria_get = @$_GET['categoria'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $getCompany['nombre']; ?></title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="styles/core.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <style>
    /* Estilos adicionales para el menú */
    .navbar {
      background-color: #ffffff;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
      font-size: 24px;
      font-weight: bold;
      color: #333333;
    }

    .navbar-nav .nav-item {
      margin-right: 15px;
    }

    .navbar-nav .nav-link {
      color: #333333;
    }

    .navbar-nav .nav-link:hover {
      color: #007bff;
    }

    /* Estilos adicionales para el popup de inicio de sesión */
    .login-popup {
      max-width: 400px;
      margin: 50px auto;
    }
  </style>
</head>
<body>

  <!--Main Navigation-->
<header>

  <!-- top bar -->
  <div class="topbar topbar-dark bg-dark">
    <div class="container">
      <div class="topbar-text text-nowrap d-inline-block"><i class="ci-support"></i><span class="text-muted me-1">Contactanos </span><a class="topbar-link" href="tel:502<?php echo $getCompany['telefono']; ?>">(502) <?php echo $getCompany['telefono']; ?></a></div>
      <div class="tns-carousel tns-controls-static d-none d-md-block">
        <div class="tns-outer" id="tns1-ow"><div class="tns-inner" id="tns1-iw"><div class="tns-carousel-inner tns-slider tns-gallery tns-subpixel tns-calc tns-horizontal" data-carousel-options="{&quot;mode&quot;: &quot;gallery&quot;, &quot;nav&quot;: false}" id="tns1">
          <div class="topbar-text tns-item tns-fadeIn tns-slide-active" id="tns1-item0" style="left: 0%;"><i class="fas fa-map-marker-alt"></i> <?php echo $getCompany['direccion']; ?></div>
          
          
        </div></div></div>
      </div>
      <?php if (@$_SESSION['usuario_id']) { ?>
        <div class="ms-3 text-nowrap">
          <a class="topbar-link me-4 d-none d-md-inline-block" href="logout.php"><i class="fas fa-sign-out-alt"></i>Cerrar sesión</a>
        </div>
      <?php } ?>
    </div>
  </div>

  <!-- Jumbotron -->
  <div class="p-3 text-center bg-white border-bottom">
    <div class="container">
      <div class="row gy-3">
        <!-- Left elements -->
        <div class="col-lg-2 col-sm-4 col-4">
          <a href="?inicio" class="float-start">
            <img src="<?php echo $getCompany['image']; ?>" height="70" />
          </a>
        </div>
        <!-- Left elements -->

        <!-- Center elements -->
        <div class="order-lg-last col-lg-10 col-sm-8 col-8 d-flex flex-row-reverse">
          <div class="d-flex flex-row align-items-center">
          <?php
            if (!$user->validateSession()) {
          ?>
            <a href="javascript:void(0)" data-toggle="modal" data-target="#loginModal" class="me-1 border rounded py-1 px-3 nav-link d-flex align-items-center mr-2 text-dark"> <i class="fas fa-user-alt m-1 me-md-2"></i><p class="d-none d-md-block mb-0">Iniciar sesión</p> </a>
          <?php
            } else {

              if (@$_SESSION['admin']) {
                echo '<a href="root/" class="border rounded py-1 px-3 nav-link d-flex align-items-center mr-2 text-dark" target="_blank"> <i class="fas fa-external-link-square-alt m-1 me-md-2"></i><p class="d-none d-md-block mb-0">Administración</p> </a>';
              }
          ?>
            <a href="javascript:void(0)" class="border rounded py-1 px-3 nav-link d-flex align-items-center mr-2 text-dark"> <i class="fas fa-user m-1 me-md-2 mr-2"></i><p class="d-none d-md-block mb-0"><?php echo $thisUser['nombre']; ?></p> </a>

            <a href="cart.php" class="border rounded py-1 px-3 nav-link d-flex align-items-center mr-2 text-dark">
                <i class="fas fa-shopping-cart m-1 me-md-2 mr-2"></i>
                <p class="d-none d-md-block mb-0">Carrito
                  <span class="badge badge-dark">0</span>
                </p>
            </a>
          <?php } ?>

            <!-- <a href="javascript:void(0)" class="me-1 border rounded py-1 px-3 nav-link d-flex align-items-center" target="_blank"> <i class="fas fa-heart m-1 me-md-2"></i><p class="d-none d-md-block mb-0">Wishlist</p> </a> -->
            <!-- <a href="javascript:void(0)" class="border rounded py-1 px-3 nav-link d-flex align-items-center" target="_blank"> <i class="fas fa-shopping-cart m-1 me-md-2"></i><p class="d-none d-md-block mb-0">My cart</p> </a> -->
          </div>
        </div>
        <!-- Center elements -->

        <!-- Right elements -->
<!--         <div class="col-lg-5 col-md-12 col-12 align-self-center">
          <div class="input-group">
            <div class="form-outline flex-fill">
              <input type="search" id="form1" class="form-control rounded-0" />
            </div>
            <button type="button" class="btn btn-primary shadow-0">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div> -->
        <!-- Right elements -->
      </div>
    </div>
  </div>
  <!-- Jumbotron -->

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-light border shadow-none">
    <!-- Container wrapper -->
    <div class="container justify-content-center justify-content-md-between">
      <!-- Toggle button -->
      <div class="w-100 d-flex justify-content-center mb-2 d-block d-lg-none">
        <button class="navbar-toggler text-dark border" type="button" data-toggle="collapse" data-target="#navbarLeftAlignExample" aria-expanded="false" aria-controls="navbarLeftAlignExample">
          <i class="fas fa-bars"></i>
        </button>
      </div>

      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a href="?p=brands" class="nav-link text-dark"><i class="fas fa-clipboard-list"></i> Marcas</a>
        </li>
      </ul>

      <!-- Collapsible wrapper -->
      <div class="collapse navbar-collapse justify-content-end" id="navbarLeftAlignExample">
        <!-- Left links -->
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown mr-0 bg-dark">
            <a class="nav-link dropdown-toggle pb-1 text-light" href="javascript:void(0)" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Categorias</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
              <a class="dropdown-item" href="?inicio">Todas las categorias</a>
              <?php
                $categorias = $db->query("SELECT id,nombre FROM categorias WHERE empresa_id = " . $_SESSION['empresa_id']);
                foreach ($categorias AS $categoria) {
                  echo '<a class="dropdown-item'.($categoria_get&&$categoria_get==$categoria['id']?' active':'').'" href="?categoria='.$categoria['id'].'">'.$categoria['nombre'].'</a>';
                }
              ?>
            </div>
          </li>
          <form class="form-inline" method="GET">
            <div class="input-group">
              <input class="form-control border border-dark" type="search" value="<?php echo $codigo_get; ?>" name="codigo" placeholder="Buscar: Código" aria-label="Buscar: Código">
              <div class="input-group-append">        
                  <button class="btn btn-dark my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
              </div>
            </div>      
          </form>
        </ul>
        <!-- Left links -->
      </div>
    </div>
    <!-- Container wrapper -->
  </nav>
  <!-- Navbar -->
</header>

<?php
  if (!$user->validateSession()) {
?>
  <!-- Popup de inicio de sesión -->
  <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog login-popup" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loginModalLabel">Iniciar sesión</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="login.php" method="post">
            <div class="form-group">
              <label for="email">E-mail</label>
              <input type="text" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
              <label for="password">Contraseña</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar sesión</button>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
