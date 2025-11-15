<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/usuarios/listado_usuarios.php');


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary collapsed-card">
            <div class="card-header">
              <h3 class="card-title">USUARIOS</h3>
              <div class="card-tools">
                <a href="create.php" type="button" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Nuevo Usuario</a>
              </div>
            </div>
            <div class="card-body" style="display: block;">
              <table id="example1" class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th style="width: 2%;">Nro</th>
                    <th style="width: 15%;">Nombre</th>
                    <th style="width: 10%;">Usuario</th>
                    <th style="width: 15%;">Rol de usuario</th>
                    <th style="width: 2%; text-align: center; vertical-align: middle;"><i class="fa fa-filter" aria-hidden="true"></i></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $contador = 0;
                  foreach ($usuarios_datos as $usuario_datos) {
                    $id_usuario = $usuario_datos['id_usuario'];
                  ?>
                    <tr>
                      <td><?php echo $contador = $contador + 1; ?></td>
                      <td><?php echo $usuario_datos['nombres']; ?></td>
                      <td><?php echo $usuario_datos['usuario']; ?></td>
                      <td><?php echo $usuario_datos['rol']; ?></td>
                      <td>
                        <div class="btn-group">
                          <a href="show.php?id=<?php echo $id_usuario; ?>" type="button" class="btn btn-info"><i class="fa fa-eye"></i></a>
                          <a href="update.php?id=<?php echo $id_usuario; ?>" type="button" class="btn btn-success"><i class="fa fa-pencil-alt"></i></a>
                          <a href="delete.php?id=<?php echo $id_usuario; ?>" type="button" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                        </div>
                      </td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
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
include('../layout/mensajes.php')
?>