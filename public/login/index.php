<?php
// Debe ir en la línea 1, sin espacios antes
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$respuesta = '';
if (isset($_SESSION['mensaje'])) {
    $respuesta = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']); // opcional: limpiar flash
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Avicont</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../public/templeates/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../public/templeates/AdminLTE-3.2.0/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../public/templeates/AdminLTE-3.2.0/dist/css/adminlte.min.css">

  <!-- sweetalert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="hold-transition login-page">

<?php if ($respuesta !== ''): ?>
<script>
  Swal.fire({
    title: "Datos incorrectos",
    text: "<?php echo htmlspecialchars($respuesta, ENT_QUOTES, 'UTF-8'); ?>",
    showConfirmButton: false,
    timer: 1500,
    icon: "error"
  });
</script>
<?php endif; ?>


  <div class="login-box">

    <!-- /.login-logo -->
    <center>
      <img src="../public/img/Logo2.jpg" alt="" width="150">
    </center>
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="#" class="h1"><b>Avi</b>CONT</a>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Inicio de sesion</p>

        <form action="../app/controllers/login/ingreso.php" method="post">
          <div class="input-group mb-3">
            <input type="text" name="user" class="form-control" placeholder="Usuario">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fa fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" name="password_user" class="form-control" placeholder="Contraseña">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <!-- /.col -->
            <div class="col-12">
              <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
            </div>
            <!-- /.col -->
          </div>
        </form>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="../public/templeates/AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="../public/templeates/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../public/templeates/AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>
</body>

</html>
<?php
//echo $password_user = password_hash('admin', PASSWORD_DEFAULT);
?>