<?php
   session_start();
   if(isset($_SESSION['usuario_id'])){
       session_destroy();
       header('location: index.php');
   }
   else{
       header('location: index.php?already');
   }
?>