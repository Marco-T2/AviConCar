<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/roles/listado_roles.php');


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
              <h3 class="card-title">NUEVO USUARIO</h3>
            </div>
            <div class="card-body" style="display: block;">
              <div class="row">
                <div class="col-md-12">
                  <form action="../app/controllers/usuarios/create.php" method="post">
                    <div class="form-group">
                      <label for="">Nombres</label>
                      <input type="text" name="nombres" class="form-control" placeholder="Nombre completo" required>
                    </div>
                    <div class="form-group">
                      <label for="">Usuario</label>
                      <input type="text" name="usuario" class="form-control" placeholder="Escriba el nombre de usuario" required>
                    </div>
                    <div class="form-group">
                      <label for="">Rol de usuario</label>
                      <select name="rol" id="" class="class form-control">
                        <option value="">Seleccionar</option>
                        <?php foreach ($roles_datos as $rol) { ?>
                          <option value="<?php echo $rol['id_rol']; ?>"><?php echo $rol['rol']; ?></option>
                        <?php } ?>
                      </select>

                    </div>
                    <div class="form-group">
                      <label for="">Constraseña</label>
                      <input type="password" name="password_user" class="form-control" placeholder="Escriba su contraseña" required>
                    </div>
                    <div class="form-group">
                      <label for="">Confirmar Constraseña</label>
                      <input type="password" name="password_userRepet" class="form-control" placeholder="*********************" required>
                    </div>
                    <hr>
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