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
</head>
<body>
   <div class="sidenav" style="background:#66615b;">
      <div class="login-main-text">
         <img src="https://demo.planigo.app/CONFIG/img/logo2.png" style="width: 230px;margin: 0 auto 31px auto;">
         <h2 style="color:#bebebe!important;">Paquetes Planigo<br> Inicio de sesión</h2>
         <p>Gestion completa de tus envios.</p>
         <!--<h2 style="font-size:19px;">Contactenos: <a href="mailito:ventas@wortit.net" style="color: #4bdb65;">ventas@wortit.net</a></h2>-->
      </div>
   </div>
   <div class="main">
      <div class="col-md-6 col-sm-12">
         <div class="login-form">
            <form action="login.php" method="POST">
               <div class="form-group">
                  <label>Correo electrónico</label>
                  <input type="text" name="email" class="form-control" placeholder="User Name">
               </div>
               <div class="form-group">
                  <label>Contraseña</label>
                  <input type="password" name="password" class="form-control" placeholder="Password">
               </div>
               <button type="submit" class="btn btn-black">Iniciar sesión</button>
               <!-- <button type="submit" class="btn btn-secondary">Register</button> -->
            </form>
         </div>
      </div>
   </div>
</body>
</html>