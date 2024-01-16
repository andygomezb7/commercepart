<?php

// Configuración de la base de datos (reemplaza los valores con los tuyos)
$dbHost = 'localhost';
$dbUser = 'wortdebk_oficial';
$dbPassword = 'hG)2?SugB=pJ';
$dbName = 'wortdebk_package';

// Conexión a la base de datos
$db = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

// Verificar errores de conexión
if ($db->connect_error) {
  die("Error de conexión a la base de datos: " . $db->connect_error);
}