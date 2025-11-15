<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/usuarios/show_usuario.php');


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Usuarios</h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-md-6">
          <div class="card card-outline card-danger">
            <div class="card-header">
              <h3 class="card-title">¿Esta seguro de eliminar a este usuario?</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                </button>
              </div>
            </div>
            <div class="card-body" style="display: block;">
              <div class="row">
                <div class="col-md-12">
                  <form action="../app/controllers/usuarios/delete_usuario.php" method="post">
                    <div class="form-group">
                      <input type="text" name="id_usuario" value="<?php echo $id_usuario_get; ?>" hidden>
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
                      <button class="btn btn-danger">Elimiar</button>
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
include('../layout/mensajes.php');
?>