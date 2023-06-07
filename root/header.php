<?php
session_start();
require_once '../secure/trun.php';

if (!$_SESSION['loggedin'] && !$_SESSION['admin']) {
    header('location: ../index.php');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/core.css">
    <title>Dashboard</title>
</head>

<body>
    <div class="container-fluid">
        <div class="row" style="height:100%;">
            <!-- Menú lateral -->
            <nav class="col-md-2 col-lg-2 d-md-block bg-dark sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#"><img class="w-100" src="../styles/images/arsa-png.png" /></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Inicio</a>
                        </li>
                        <div class="dropdown-divider"></div>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?tipo=2">Usuarios</a>
                        </li>
                        <div class="dropdown-divider"></div>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?tipo=3">Repuestos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?tipo=7">Categorías</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?tipo=5">Marcas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?tipo=6">Modelos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?tipo=11">Motores</a>
                        </li>
                        <div class="dropdown-divider"></div>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?tipo=8">Asignación de marcas y modelos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?tipo=10">Asignación motores a marca/modelo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?tipo=9">Asignación de códigos a repuestos</a>
                        </li>
                        <div class="dropdown-divider"></div>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="?tipo=4">Bodegas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Ubicaciones</a>
                        </li>
                        <div class="dropdown-divider"></div>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Pedidos</a>
                        </li>
                        <div class="dropdown-divider"></div>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Habilidades</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Catálogo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Registro</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Contacto</a>
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
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">