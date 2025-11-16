<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SISTEMA CONTABLE 2025</title>

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
          <a href="#" class="nav-link">SISTEMA CONTABLE 2025 - Avicola EL Carmen</a>
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
            <a href="#" class="d-block"><?php echo $nombres_sesion; ?></a>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            <!--MENU -->
            <li class="nav-item">
              <a href="<?php echo $URL; ?>/index.php" class="nav-link">
                <i class="nav-icon fas fa-home nav-icon"></i>
                <p>
                  MENU
                </p>
              </a>
            </li>

            <!--KADEX -->
            <li class="nav-item">
              <a href="<?php echo $URL; ?>/kardex/" class="nav-link">
                <i class="nav-icon fa fa-bars"></i>
                <p>
                  KARDEX
                </p>
              </a>
            </li>

            <li class="nav-item" hidden>
              <a href="#" class="nav-link">
                <i class="nav-icon fa fa-bars"></i>
                <p>
                  KARDEX
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo $URL; ?>/kardex/" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kardex clientes</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo $URL; ?>/kardex/" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kardex proveedores</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo $URL; ?>/kardex/" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Fleteros</p>
                  </a>
                </li>
              </ul>
            </li>

            <!--NOTAS DE DESPACHO -->
            <li class="nav-item">
              <a href="<?php echo $URL; ?>/despachos/" class="nav-link">
                <i class="nav-icon fa fa-window-maximize"></i>
                <p>
                  NOTAS DE DESPACHO
                </p>
              </a>
            </li>

            <!-- CAJAS -->
            <li class="nav-item">
              <a href="<?php echo $URL; ?>/cajas/" class="nav-link">
                <i class="nav-icon fas fa-boxes"></i>
                <p>
                  CAJAS
                </p>
              </a>
            </li>

            <li class="nav-item" hidden>
              <a href="#" class="nav-link">
                <i class="nav-icon fa fa-window-maximize"></i>
                <p>
                  Venta de pollo
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo $URL; ?>/despachos/" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Nota Despachos</p>
                  </a>
                </li>
              </ul>
            </li>

            <!--COMPROBANTES -->

            <li class="nav-item">
              <a href="<?php echo $URL; ?>/comprobantes/" class="nav-link">
                <i class="nav-icon fa fa-window-maximize"></i>
                <p>
                  COMPROBANTES
                </p>
              </a>
            </li>

            <li class="nav-item" hidden>
              <a href="#" class="nav-link">
                <i class="nav-icon fa fa-window-maximize"></i>
                <p>
                  Comprobantes
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo $URL; ?>/comprobantes" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>+ Comprobantes</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo $URL; ?>/tipopersonas" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Reportes</p>
                  </a>
                </li>
              </ul>
            </li>

            <!--PERSONAS -->

            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fa fa-user-plus"></i>
                <p>
                  Registro personas
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo $URL; ?>/personas" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Personas</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo $URL; ?>/tipopersonas" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tipo personas</p>
                  </a>
                </li>
              </ul>
            </li>

            <!--CONTABILIDAD -->
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fa fa-balance-scale"></i>
                <p>
                  Contabilidad
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="#" class="nav-link" id="linkPlanDeCuentas">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Plan de cuentas</p>
                  </a>
                </li>
                <script>
                  document.getElementById('linkPlanDeCuentas').addEventListener('click', function(e) {
                    e.preventDefault();

                    Swal.fire({
                      title: 'Acceso restringido',
                      text: 'Ingrese la contraseña para acceder al Plan de cuentas',
                      input: 'password',
                      inputAttributes: {
                        autocapitalize: 'off',
                        autocorrect: 'off'
                      },
                      showCancelButton: true,
                      confirmButtonText: 'Acceder',
                      showLoaderOnConfirm: true,
                      preConfirm: (password) => {
                        // Aquí deberías verificar la contraseña, por ejemplo, mediante una petición AJAX.
                        // Por ahora, simplemente compararemos con una contraseña estática '1234'.
                        if (password !== 'ctarqui') {
                          Swal.showValidationMessage('Contraseña incorrecta');
                          return false;
                        }
                        return true;
                      },
                      allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                      if (result.isConfirmed) {
                        // Si la contraseña es correcta, redirige al usuario.
                        window.location.href = "<?php echo $URL; ?>/contabilidad";
                      }
                    });
                  });
                </script>
              </ul>
            </li>

            <!--ADMINISTRACION -->
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fa fa-address-card"></i>
                <p>
                  Administracion
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo $URL; ?>/usuarios" class="nav-link">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Usuarios</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo $URL; ?>/configuracion" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Roles</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo $URL; ?>/configuracion/gestion.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Gestion</p>
                  </a>
                </li>
              </ul>
            </li>

            <!--CERRAR SESION -->
            <li class="nav-item">
              <a href="<?php echo $URL ?>/app/controllers/login/cerrar_sesion.php" class="nav-link" style="background-color: #d9534f;">
                <i class="nav-icon fas fa-door-closed"></i>
                <p>
                  Cerrar session
                </p>
              </a>
            </li>

          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>