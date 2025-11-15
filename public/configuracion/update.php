<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/roles/update_roles.php');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6">
          <div class="card card-outline card-success">
            <div class="card-header">
              <h3 class="card-title">EDITAR ROL</h3>
            </div>
            <div class="card-body" style="display: block;">
              <div class="row">
                <div class="col-md-12">
                  <form action="../app/controllers/roles/update.php" method="post">
                    <div class="form-group">
                      <input type="text" name="$id_rol" value="<?php echo $id_rol_get; ?>" hidden>
                      <label for="">Nombres del rol</label>
                      <input type="text" name="rol" class="form-control" placeholder="Escriba el nombre del rol" value="<?php echo $rol ?>" required>
                      <hr>
                    </div>
                    <div class=form-group>
                      <button type="submit" class="btn btn-success">Actualizar</button>
                      <a href="index.php" class="btn btn-secondary">Cancelar</a>
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
include('../layout/mensajes.php');
?>