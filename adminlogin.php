<?php
   if (@$_SESSION['usuario_id']) {
      header('location: root/');
   }
?>
<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Wortit</title>
   <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
   <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
   <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
   <!------ Include the above in your HEAD tag ---------->
   <link rel="stylesheet" type="text/css" href="styles/adminlogin.css">
   <link rel="stylesheet" href="styles/css/mdb.min.css" />
   <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" />
</head>
<body class="d-flex flex-column" style="height: 100vh;">
   <div class="sidenav">
      <div class="login-main-text">
         <img src="styles/images/uranologo.png" style="width: 230px;margin: 0 auto 31px auto;">
         <h2>ERP inteligente</h2>
         <p>Permítanos ser su socio en el éxito de su negocio, Con "Urano", la tecnología se convierte en su aliado capaz de todo. ¡Únase a la revolución de la gestión empresarial con nosotros!</p>
         <h2 style="font-size:19px;">Contactenos: <a href="mailito:ventas@wortit.net" style="color: #212529;">ventas@wortit.net</a></h2>
      </div>
   </div>
   <div class="main h-100">
      <div class="col-md-12 col-sm-12 d-flex justify-content-center align-items-center h-100 login-background">
         <div class="login-form card shadow-2-strong p-5 col-5" style="border-radius: 1rem;">
            <h3 class="mb-5 font-weight-light">Inicia Sesión</h3>
            <form action="login.php" method="POST">
               <div data-mdb-input-init="" class="form-outline mb-4">
                  <input type="email" name="email" id="typeEmailX-2" class="form-control form-control-lg">
                  <label class="form-label" for="typeEmailX-2">Correo Electrónico</label>
               </div>
               <div data-mdb-input-init class="form-outline mb-4">
                  <input type="password" name="password" id="typePasswordX-2" class="form-control form-control-lg" />
                  <label class="form-label" for="typePasswordX-2">Contaseña</label>
               </div>
               <button type="submit" class="btn btn-dark text-white w-100">Entrar</button>
               <!-- <button type="submit" class="btn btn-secondary">Register</button> -->
            </form>
         </div>
      </div>
   </div>
   <!-- MDB -->
   <script type="text/javascript" src="scripts/js/mdb.umd.min.js"></script>
</body>
</html>