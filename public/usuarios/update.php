<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');


include('../app/controllers/usuarios/update_usuario.php');
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
              <h3 class="card-title">EDITAR USUARIOS</h3>
            </div>
            <div class="card-body" style="display: block;">
              <div class="row">
                <div class="col-md-12">
                  <form action="../app/controllers/usuarios/update.php" method="post">
                    <input type="text" name="id_usuario" value="<?php echo $id_usuario; ?>" hidden>
                    <div class="form-group">
                      <label for="">Nombres</label>
                      <input type="text" name="nombres" class="form-control" value="<?php echo $nombres; ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="">Usuario</label>
                      <input type="text" name="usuario" class="form-control" value="<?php echo $usuario; ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="">Rol de usuario</label>
                      <select name="rol" id="" class="class form-control">
                        <option value="">Seleccionar</option>
                        <?php foreach ($roles_datos as $dato_rol) { ?>
                          <option value="<?php echo $dato_rol['id_rol']; ?>" <?php if ($dato_rol['rol'] == $rol) {
                                                                                echo 'selected="selected"';
                                                                              } ?>>
                            <?php echo $dato_rol['rol']; ?>
                          </option>
                        <?php } ?>
                      </select>

                    </div>
                    <div class="form-group">
                      <label for="">Constraseña</label>
                      <input type="password" name="password_user" class="form-control" placeholder="Nueva contraseña">
                    </div>
                    <div class="form-group">
                      <label for="">Confirmar Constraseña</label>
                      <input type="password" name="password_userRepet" class="form-control" placeholder="*********************">
                    </div>
                    <hr>
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