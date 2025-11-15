<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/usuarios/show_usuario.php');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-md-6">
          <div class="card card-outline card-success">
            <div class="card-header">
              <h3 class="card-title">iNFORMACION DE USUARIO</h3>
            </div>
            <div class="card-body" style="display: block;">
              <div class="row">
                <div class="col-md-12">
                  <form action="../app/controllers/usuarios/create.php" method="post">
                    <div class="form-group">
                      <label for="">Nombres</label>
                      <input type="text" name="nombres" class="form-control" readonly value="<?php echo $nombres; ?>">
                    </div>
                    <div class="form-group">
                      <label for="">Usuario</label>
                      <input type="text" name="usuario" class="form-control" readonly value="<?php echo $usuario; ?>">
                    </div>
                    <div class="form-group">
                      <label for="">Rol del usuario</label>
                      <input type="text" name="rol" class="form-control" readonly value="<?php echo $rol; ?>">
                    </div>
                    <div class="form-group">
                      <label for="">Fecha de creacion</label>
                      <input type="text" name="fyh_creacion" class="form-control" readonly value="<?php echo $fyh_creacion; ?>">
                    </div>
                    <hr>
                    <div class=form-group>
                      <a href="index.php" class="btn btn-secondary">Volver</a>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
include('../layout/parte2.php');
?>