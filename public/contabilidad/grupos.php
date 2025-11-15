<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/contabilidad/listargrupos.php');


$currentFile = basename($_SERVER['PHP_SELF']);

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <?php
    include('../layout/plancontable.php');
    ?>
    <div style="margin-bottom: 5px;"></div>
    <!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary collapsed-card">
                        <div style="margin-bottom: 10px;"></div>
                        <div class="text-center" style="gap: 0.10rem;">
                            <h4 style="font-size: 0.9rem; margin-bottom: 0;"><strong>AVICOLA EL CARMEN</strong></h4>
                            <span class="text-sm" style="font-size: 0.85rem; margin-bottom: 0; ">Grupos</span>
                        </div>
                        <div class="text-left"> <!-- Alinea el botón a la derecha -->
                            <button type="button" style="margin-left: 1.5rem;" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-create">
                                <i class="fa fa-plus fa-sm"></i> Nuevo grupo
                            </button>
                        </div>
                        <div class="card-body" style="display: block;">
                            <table id="example1" class="table table-bordered table-striped table-sm" style="font-size: 0.85rem; vertical-align: middle;">
                                <thead>
                                    <tr>
                                        <th style="width: 2%;"><i class="fa fa-list-ol" aria-hidden="true"></i></th>
                                        <th style="width: 20%;">Nombre</th>
                                        <th style="width: 10%;">Naturaleza</th>
                                        <th style="width: 10%;">PATH</th>
                                        <th style="width: 2%; text-align: center;"><i class="fa fa-filter fa-sm" aria-hidden="true"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0;
                                    foreach ($grupos_datos as $grupo_dato) { // Cambio aquí: usa $grupo_dato para cada elemento
                                        $id_grupo = $grupo_dato['id_grupo'];
                                    ?>
                                        <tr>
                                            <td style="vertical-align: middle;"><?php echo ++$contador; ?></td>
                                            <td style="vertical-align: middle;"><?php echo htmlspecialchars($grupo_dato['name_grupo']); ?></td>
                                            <td style="vertical-align: middle;"><?php echo htmlspecialchars($grupo_dato['naturaleza']); ?></td>
                                            <td style="vertical-align: middle;"><?php echo htmlspecialchars($grupo_dato['path']); ?></td>
                                            <td style="vertical-align: middle;text-align: center;">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-update<?php echo $id_grupo; ?>">
                                                        <i class="fa fa-pencil-alt fa-sm"></i>
                                                    </button>
                                                    <!-- Modal de edición -->
                                                    <div class="modal fade" id="modal-update<?php echo $id_grupo; ?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form action="../app/controllers/contabilidad/update_grupos.php" method="POST">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Editar Grupo</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Nombre del grupo</label>
                                                                                <input type="text" name="name_grupo" class="form-control" value="<?php echo htmlspecialchars($grupo_dato['name_grupo']); ?>" required>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Naturaleza</label>
                                                                                <select name="naturaleza" class="form-control" required>
                                                                                    <option value="Debito" <?php echo ($grupo_dato['naturaleza'] == 'Debito') ? 'selected' : ''; ?>>Debito</option>
                                                                                    <option value="Credito" <?php echo ($grupo_dato['naturaleza'] == 'Credito') ? 'selected' : ''; ?>>Credito</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                                                        <input type="hidden" name="id_grupo" value="<?php echo $id_grupo; ?>">
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
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


<!-- /.modal registrar cuentas -->
<div class="modal fade" id="modal-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Crear nuevo Grupo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name_grupo">Nombre del grupo <b>*</b></label>
                    <input type="text" id="name_grupo" class="form-control" required>
                    <small style="color: red; display:none;" id="lbl_create"> *Este campo es requerido</small>
                </div>
                <div class="form-group">
                    <label for="naturaleza">Naturaleza <b>*</b></label>
                    <select id="naturaleza" class="form-control" required>
                        <option value="" disabled selected>Seleccionar</option>
                        <option value="Debito">Debito</option>
                        <option value="Credito">Credito</option>
                    </select>
                    <small style="color: red; display:none;" id="lbl_create2"> *Este campo es requerido</small>
                </div>
                <input type="hidden" id="id_usuario" class="form-control" value="<?php echo $id_usuario; ?>">
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_create">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- /.modal -->

<script>
    $('#btn_create').click(function() {
        var name_grupo = $('#name_grupo').val();
        var naturaleza = $('#naturaleza').val();
        var id_usuario = $('#id_usuario').val();

        // Inicializar estado válido.
        var isValid = true;

        // Restablecer advertencias
        $('#lbl_create').hide();
        $('#lbl_create2').hide();

        if (name_grupo === "") {
            $('#name_grupo').focus();
            $('#lbl_create').show();
            isValid = false;
        }

        if (naturaleza === "" || naturaleza === null) {
            $('#naturaleza').focus();
            $('#lbl_create2').show();
            isValid = false;
        }

        // Solo envía datos si ambos campos son válidos.
        if (isValid) {
            var data = {
                name_grupo: name_grupo,
                naturaleza: naturaleza,
                id_usuario: id_usuario
            };

            var url = "../app/controllers/contabilidad/create_grupos.php";

            $.post(url, data, function(response) {
                $('#modal-create').modal('hide');
                location.reload(); // O manejar la respuesta como prefieras.
            });
        }
    });
</script>



<div id="respuesta"></div>