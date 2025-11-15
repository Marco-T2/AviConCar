<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/contabilidad/listargrupos.php');
include('../app/controllers/contabilidad/listarsubgrupos.php');
include('../app/controllers/contabilidad/listarcuentas.php');

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
                            <span class="text-sm" style="font-size: 0.85rem; margin-bottom: 0; ">Cuentas</span>
                        </div>
                        <div class="text-left"> <!-- Alinea el botón a la derecha -->
                            <button type="button" style="margin-left: 1.5rem;" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-create">
                                <i class="fa fa-plus fa-sm"></i> Nuevo cuenta
                            </button>
                        </div>
                        <div class="card-body" style="display: block;">
                            <table id="example1" class="table table-bordered table-striped table-sm" style="font-size: 0.85rem; vertical-align: middle;">
                                <thead>
                                    <tr>
                                        <th style="width: 2%;"><i class="fa fa-list-ol" aria-hidden="true"></i></th>
                                        <th style="width: 15%;">Nombre</th>
                                        <th style="width: 15%;">Sub-Grupo</th>
                                        <th style="width: 10%;">PATH</th>
                                        <th style="width: 2%; text-align: center;"><i class="fa fa-filter fa-sm" aria-hidden="true"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0;
                                    foreach ($cuentas_datos as $cuentas_dato) {

                                        $id_cuenta = $cuentas_dato['id_cuenta'];
                                    ?>
                                        <tr>
                                            <td style="vertical-align: middle;"><?php echo $contador = $contador + 1; ?></td>
                                            <td style="vertical-align: middle;"><?php echo $cuentas_dato['name_cuenta']; ?></td>
                                            <td style="vertical-align: middle;"><?php echo htmlspecialchars($cuentas_dato['subgrupo_path'] . ' - ' . $cuentas_dato['name_subgrupo']); ?></td>
                                            <td style="vertical-align: middle;"><?php echo $cuentas_dato['cuenta_path']; ?></td>
                                            <td style="vertical-align: middle; text-align: center;">

                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-update<?php echo $id_cuenta; ?>">
                                                        <i class="fa fa-pencil-alt fa-sm"></i>
                                                    </button>
                                                    <a href="#" onclick="confirmDelete(<?php echo $id_cuenta; ?>);" type="button" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash fa-sm"></i>
                                                    </a>
                                                    <script>
                                                        function confirmDelete(id) {
                                                            Swal.fire({
                                                                title: '¿Estás seguro de eliminar esta cuenta?',
                                                                text: "No podrás revertir esto",
                                                                icon: 'warning',
                                                                showCancelButton: true,
                                                                confirmButtonColor: '#3085d6',
                                                                cancelButtonColor: '#d33',
                                                                confirmButtonText: 'Sí, bórralo'
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    window.location.href = "../app/controllers/contabilidad/deletecuenta.php?id=" + id;
                                                                }
                                                            });
                                                        }
                                                    </script>
                                                    <!-- /.modal editar cuentas -->
                                                    <div class="modal fade" id="modal-update<?php echo $id_cuenta; ?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form action="../app/controllers/contabilidad/update_cuentas.php" method="POST">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Editar Cuenta</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Nombre de la cuenta</label>
                                                                                <input type="text" name="name_cuenta" class="form-control" value="<?php echo htmlspecialchars($cuentas_dato['name_cuenta']); ?>" required>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Sub-grupo</label>
                                                                                <select name="id_subgrupo" class="form-control" required>
                                                                                    <option value="" disabled>Seleccionar sub-grupo</option>
                                                                                    <?php foreach ($subgrupos_datos as $subgrupo) : ?>
                                                                                        <option value="<?php echo htmlspecialchars($subgrupo['id_subgrupo']); ?>" <?php echo ($subgrupo['id_subgrupo'] == $cuentas_dato['id_subgrupo']) ? 'selected' : ''; ?>>
                                                                                            <?php echo htmlspecialchars($subgrupo['path'] . ' - ' . $subgrupo['name_subgrupo']); ?>
                                                                                        </option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                                                        <input type="hidden" name="id_cuenta" value="<?php echo $id_cuenta; ?>">
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
                <h4 class="modal-title">Crear nueva cuenta</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name_cuenta">Nombre de la cuenta <b>*</b></label>
                        <input type="text" id="name_cuenta" class="form-control" required>
                        <small style="color: red; display:none;" id="lbl_create"> *Este campo es requerido</small>
                    </div>
                    <div class="form-group">
                        <label for="id_subgrupo">Sub-grupo <b>*</b></label>
                        <select id="id_subgrupo" class="form-control" required>
                            <option value="" disabled selected>Seleccionar sub-grupo</option>
                            <?php foreach ($subgrupos_datos as $subgrupo) : ?>
                                <option value="<?php echo $subgrupo['id_subgrupo']; ?>">
                                    <?php echo htmlspecialchars($subgrupo['path'] . ' - ' . $subgrupo['name_subgrupo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small style="color: red; display:none;" id="lbl_create2"> *Este campo es requerido</small>
                    </div>
                    <input type="hidden" id="id_usuario" value="<?php echo htmlspecialchars($id_usuario); ?>">
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_create">Guardar</button>
            </div>
        </div>
    </div>
</div>




<script>
    $('#btn_create').click(function() {
        // Obtener valores del formulario
        var name_cuenta = $('#name_cuenta').val();
        var id_subgrupo = $('#id_subgrupo').val();
        var id_usuario = $('#id_usuario').val();

        // Reiniciar las alertas de error
        $('#lbl_create').hide();
        $('#lbl_create2').hide();

        // Verificar que los campos no estén vacíos
        if (name_cuenta === "" || id_subgrupo === "") {
            // Mostrar error si el nombre de la cuenta está vacío
            if (name_cuenta === "") {
                $('#name_cuenta').focus();
                $('#lbl_create').show();
            }

            // Mostrar error si el ID del subgrupo está vacío
            if (id_subgrupo === "") {
                $('#id_subgrupo').focus();
                $('#lbl_create2').show();
            }
        } else {
            // Si ambos campos tienen información, realizar la petición
            var url = "../app/controllers/contabilidad/create_cuentas.php";

            // Uso de AJAX para enviar información al servidor
            $.post(url, {
                name_cuenta: name_cuenta,
                id_subgrupo: id_subgrupo,
                id_usuario: id_usuario
            }, function(datos) {
                // Mostrar datos o respuesta del servidor en algún elemento, por ejemplo, #respuesta
                $('#respuesta').html(datos);

                // Opcional: Cerrar el modal y recargar la página para ver los cambios
                $('#modal-create').modal('hide');
                location.reload();
            });
        }
    });
</script>

<div id="respuesta"></div>