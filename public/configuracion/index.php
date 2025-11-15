<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/roles/listado_roles.php');


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary collapsed-card">
            <div class="card-header">
              <h3 class="card-title">ROLES</h3>
              <div class="card-tools">
                <a href="create.php" type="button" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Nuevo ROL</a>
              </div>
            </div>
            <div class="card-body" style="display: block;">
              <table id="example1" class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th style="width: 2%">Nro</th>
                    <th style="width: 80%">Nombre del rol</th>
                    <th style="width: 2%; text-align: center; vertical-align: middle;"><i class="fa fa-filter" aria-hidden="true"></i></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $contador = 0;
                  foreach ($roles_datos as $roles_datos) {
                    $id_rol = $roles_datos['id_rol'];
                  ?>
                    <tr>
                      <td><?php echo $contador = $contador + 1; ?></td>
                      <td><?php echo $roles_datos['rol']; ?></td>
                      <td>
                        <div class="btn-group">
                          <a href="update.php?id=<?php echo $id_rol; ?>" type="button" class="btn btn-success btn-sm"><i class="fa fa-pencil-alt"></i></a>
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