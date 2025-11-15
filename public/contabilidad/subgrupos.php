<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');


include('../app/controllers/contabilidad/listargrupos.php');
include('../app/controllers/contabilidad/listarsubgrupos.php');


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
                            <span class="text-sm" style="font-size: 0.85rem; margin-bottom: 0; ">Subgrupos</span>
                        </div>
                        <div class="text-left"> <!-- Alinea el botón a la derecha -->
                            <button type="button" style="margin-left: 1.5rem;" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-create-subgrupo">
                                <i class="fa fa-plus fa-sm"></i> Nueva SubGrupo
                            </button>
                        </div>
                        <div class="card-body" style="display: block;">
                            <table id="example1" class="table table-bordered table-striped table-sm" style="font-size: 0.85rem; vertical-align: middle;">
                                <thead>
                                    <tr>
                                        <th style="width: 2%;"><i class="fa fa-list-ol" aria-hidden="true"></i></th>
                                        <th style="width: 20%;">Nombre</th>
                                        <th style="width: 10%;">Grupo</th>
                                        <th style="width: 10%;">PATH</th>
                                        <th style="width: 2%; text-align: center;"><i class="fa fa-filter fa-sm" aria-hidden="true"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0;
                                    foreach ($subgrupos_datos as $subgrupos_dato) { // Cambio aquí: usa $subgrupos_dato para cada elemento
                                        $id_subgrupo = $subgrupos_dato['id_subgrupo'];
                                    ?>
                                        <tr>
                                            <td style="vertical-align: middle;"><?php echo ++$contador; ?></td>
                                            <td style="vertical-align: middle;"><?php echo htmlspecialchars($subgrupos_dato['name_subgrupo']); ?></td>
                                            <td style="vertical-align: middle;"><?php echo htmlspecialchars($subgrupos_dato['id_grupo'] . " - " . $subgrupos_dato['name_grupo']); ?></td>
                                            <td style="vertical-align: middle;"><?php echo htmlspecialchars($subgrupos_dato['path']); ?></td>
                                            <td style="vertical-align: middle;text-align: center;">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-update<?php echo $id_subgrupo; ?>">
                                                        <i class="fa fa-pencil-alt fa-sm"></i>
                                                    </button>
                                                    <a href="#" onclick="confirmDelete(<?php echo $id_subgrupo; ?>);" type="button" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash fa-sm"></i>
                                                    </a>
                                                    <script>
                                                        function confirmDelete(id) {
                                                            Swal.fire({
                                                                title: '¿Estás seguro de eliminar esta Sub Cuenta?',
                                                                text: "No podrás revertir esto",
                                                                icon: 'warning',
                                                                showCancelButton: true,
                                                                confirmButtonColor: '#3085d6',
                                                                cancelButtonColor: '#d33',
                                                                confirmButtonText: 'Sí, bórralo'
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    window.location.href = "../app/controllers/contabilidad/deletesubgrupo.php?id=" + id;
                                                                }
                                                            });
                                                        }
                                                    </script>
                                                    <div class="modal fade" id="modal-update<?php echo $id_subgrupo; ?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form action="../app/controllers/contabilidad/update_subgrupos.php" method="POST">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Editar Sub-Grupo</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Nombre del subgrupo</label>
                                                                                <input type="text" name="name_subgrupo" class="form-control" value="<?php echo htmlspecialchars($subgrupos_dato['name_subgrupo']); ?>" required>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Grupo</label>
                                                                                <select name="id_grupo" class="form-control" required>
                                                                                    <option value="" disabled>Seleccionar grupo</option>
                                                                                    <?php foreach ($grupos_datos as $grupo) : ?>
                                                                                        <option value="<?php echo htmlspecialchars($grupo['id_grupo']); ?>" <?php echo ($grupo['id_grupo'] == $subgrupos_dato['id_grupo']) ? 'selected' : ''; ?>>
                                                                                            <?php echo htmlspecialchars($grupo['id_grupo'] . ' - ' . $grupo['name_grupo']); ?>
                                                                                        </option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                                                        <input type="hidden" name="id_subgrupo" value="<?php echo $id_subgrupo; ?>">
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
<div class="modal fade" id="modal-create-subgrupo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Crear nuevo Subgrupo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name_subgrupo">Nombre del subgrupo <b>*</b></label>
                    <input type="text" id="name_subgrupo" class="form-control" required>
                    <small style="color: red; display:none;" id="lbl_create_subgrupo"> *Este campo es requerido</small>
                </div>
                <div class="form-group">
                    <label for="id_grupo">Grupo <b>*</b></label>
                    <select id="id_grupo" class="form-control" required>
                        <option value="" disabled selected>Seleccionar grupo</option>
                        <?php foreach ($grupos_datos as $grupo_dato) : ?>
                            <option value="<?php echo htmlspecialchars($grupo_dato['id_grupo']); ?>">
                                <?php echo htmlspecialchars($grupo_dato['id_grupo'] . ' - ' . $grupo_dato['name_grupo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color: red; display:none;" id="lbl_create_id_grupo"> *Este campo es requerido</small>
                </div>
                <input type="hidden" id="id_usuario_subgrupo" class="form-control" value="<?php echo $id_usuario; ?>">
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_create_subgrupo">Guardar</button>
            </div>
        </div>
    </div>
</div>


<!-- /.modal -->

<script>
    $('#btn_create_subgrupo').click(function() {
        var name_subgrupo = $('#name_subgrupo').val();
        var id_grupo = $('#id_grupo').val(); // Asegúrate de que este es el ID correcto.
        var id_usuario = $('#id_usuario_subgrupo').val(); // Y este también.

        var isValid = true;

        if (name_subgrupo === "") {
            $('#name_subgrupo').focus();
            $('#lbl_create_subgrupo').show();
            isValid = false;
        }

        if (!id_grupo) {
            $('#id_grupo').focus();
            $('#lbl_create_id_grupo').show();
            isValid = false;
        }

        if (isValid) {
            var data = {
                name_subgrupo: name_subgrupo,
                id_grupo: id_grupo,
                id_usuario: id_usuario
            };

            var url = "../app/controllers/contabilidad/create_subgrupos.php";

            $.post(url, data, function(response) {
                $('#modal-create-subgrupo').modal('hide');
                location.reload(); // Asumiendo que quieres recargar la página para ver los cambios.
            });
        }
    });
</script>




<div id="respuesta"></div>