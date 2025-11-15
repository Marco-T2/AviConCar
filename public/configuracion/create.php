<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6">
          <div class="card card-outline card-success">
            <div class="card-header">
              <h3 class="card-title">NUEVO ROL</h3>
            </div>
            <div class="card-body" style="display: block;">
              <div class="row">
                <div class="col-md-12">
                  <form action="../app/controllers/roles/create.php" method="post">
                    <div class="form-group">
                      <label for="">Nombres del rol</label>
                      <input type="text" name="rol" class="form-control" placeholder="Escriba el nombre del rol" required>
                      <hr>
                    </div>
                    <div class=form-group>
                      <button type="submit" class="btn btn-success">Guardar</button>
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