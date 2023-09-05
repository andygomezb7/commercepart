<?php
  session_start();
  require_once 'secure/class/user.php';

  // Crear una instancia de la clase User
  $user = new User($db);
  if ($user->validateSession()) {
    // Obtener informacion del usuario logueado
    $thisUser = $user->getUserByEmail(@$_SESSION['email']);
    $getCompany = $db->query("SELECT id, nombre, image FROM empresas WHERE id = " . $_SESSION['empresa_id'])->fetch_assoc();
  } else {
    $getCompany = $db->query("SELECT id, nombre, image FROM empresas WHERE domain = '" . $_SERVER['HTTP_HOST'] . "'")->fetch_assoc();
    if (!@$getCompany['id']) {
      $getCompany = $db->query("SELECT id, nombre, image FROM empresas WHERE id = 1")->fetch_assoc();
    }
  }

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
  <!-- Jumbotron -->
  <div class="p-3 text-center bg-white border-bottom">
    <div class="container">
      <div class="row gy-3">
        <!-- Left elements -->
        <div class="col-lg-2 col-sm-4 col-4">
          <a href="https://mdbootstrap.com/" target="_blank" class="float-start">
            <img src="<?php echo $getCompany['image']; ?>" height="70" />
          </a>
        </div>
        <!-- Left elements -->

        <!-- Center elements -->
        <div class="order-lg-last col-lg-5 col-sm-8 col-8 d-flex align-self-center">
          <div class="d-flex float-end">
          <?php
            if (!$user->validateSession()) {
          ?>
            <a href="javascript:void(0)" data-toggle="modal" data-target="#loginModal" class="me-1 border rounded py-1 px-3 nav-link d-flex align-items-center mr-2" target="_blank"> <i class="fas fa-user-alt m-1 me-md-2"></i><p class="d-none d-md-block mb-0">Sign in</p> </a>
          <?php
            } else {
          ?>
            <a href="javascript:void(0)" class="border rounded py-1 px-3 nav-link d-flex align-items-center" target="_blank"> <i class="fas fa-user m-1 me-md-2 mr-2"></i><p class="d-none d-md-block mb-0"><?php echo $thisUser['nombre']; ?></p> </a>
          <?php } ?>

          <?php
            if (@$_SESSION['admin']) {
          ?>
            <a href="root/" class="border rounded py-1 px-3 nav-link d-flex align-items-center" target="_blank"> <i class="fas fa-external-link-square-alt m-1 me-md-2"></i><p class="d-none d-md-block mb-0">Administración</p> </a>
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
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <!-- Container wrapper -->
    <div class="container justify-content-center justify-content-md-between">
      <!-- Toggle button -->
      <button class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#navbarLeftAlignExample" aria-controls="navbarLeftAlignExample" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-bars"></i>
      </button>

      <!-- Collapsible wrapper -->
      <div class="collapse navbar-collapse" id="navbarLeftAlignExample">
        <!-- Left links -->
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="#">Categorias</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Autos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Motos</a>
          </li>
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
