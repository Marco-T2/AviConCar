<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/tipopersonas/listado_tipopersonas.php');


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
                            <h3 class="card-title">Creacion Nueva persona</h3>
                        </div>
                        <div class="card-body" style="display: block;">
                            <div class="row">
                                <div class="col-md-12">
                                    <form action="../app/controllers/personas/create.php" method="post">
                                        <div class="form-group">
                                            <label for="">Nombres completo</label>
                                            <input type="text" name="name_persona" class="form-control" placeholder="Nombre completo" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Tipo persona (puedes seleccionar varios)</label>
                                            <select id="id_tipoPersona" class="class form-control" name="id_tipoPersona[]" multiple size="6">
                                                <?php
                                                foreach ($tipopersonas_datos as $tipopersonas_dato) {
                                                ?>
                                                    <option value="<?php echo $tipopersonas_dato['id_tipoPersona']; ?>"><?php echo $tipopersonas_dato['name_tipoPersona']; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                            <small class="form-text text-muted">Mantener presionada la tecla Ctrl / Cmd para seleccionar múltiples.</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="">Tags (separados por coma)</label>
                                            <input type="text" name="tags" class="form-control" placeholder="p.ej. cajas, empleado, fletero">
                                            <small class="form-text text-muted">Los tags permiten filtrar en informes (ej. 'cajas').</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Direccion</label>
                                            <input type="text" name="direccion" class="form-control" placeholder="Escriba la direccion" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Celular</label>
                                            <input type="text" name="celular" class="form-control" placeholder="Escriba el # de celular" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Descripcion</label>
                                            <input type="text" name="descripcion" class="form-control" placeholder="Poner una descripcion" required>
                                        </div>
                                        <div class="form-group" hidden>
                                            <input type="text" name="id_usuario"  class="form-control" value="<?php echo $id_usuario ?>">
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