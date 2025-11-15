<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AVICOLA EL CARMEN</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/dist/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

    <!-- jQuery -->
    <script src="<?php echo $URL; ?>/public/templeates/AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>



    <!-- BIBLIOTECA sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        .main-sidebar {
            position: fixed;
            top: 0;
            /* Ajusta este valor al alto de la barra de navegación si es necesario */
            bottom: 0;
            left: 0;
            overflow-y: auto;
            /* Permite el scroll en la barra lateral si es más larga que la pantalla */
            overflow-x: hidden;
            /* Evita el desplazamiento horizontal */
        }

        .content-wrapper {
            margin-left: 250px;
            /* Ajusta este valor al ancho de la sidebar */
            overflow-y: auto;
            height: calc(100vh - 150px);
            /* Ajusta '70px' al alto de tu navbar */
            padding-top: 10px;
            /* Esto es para asegurarse de que el contenido no quede debajo de la navbar */
        }
    </style>


</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link">Avicola EL Carmen</a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="<?php echo $URL; ?>/index.php" class="brand-link">
                <img src="<?php echo $URL; ?>/public/img/logopollo.webp" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">AVICONT</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="<?php echo $URL; ?>/public/img/Gatouser.png" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">Cobranzas</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!--KADEX -->
                        <li class="nav-item">
                            <a href="<?php echo $URL; ?>/cobranzas/" class="nav-link">
                                <i class="nav-icon fa fa-bars"></i>
                                <p>
                                    KARDEX
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>