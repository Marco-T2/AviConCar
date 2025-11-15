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
                <div class="col-md-12">
                    <div class="card card-primary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">TIPO PERSONA</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-create">
                                    <i class="fa fa-plus"></i> Nueva Tipo persona
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="display: block;">
                            <table id="example1" class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th style="width: 2%;">Nro</th>
                                        <th style="width: 20%;">Tipo de persona</th>
                                        <th style="width: 30%;">Descripcion</th>
                                        <th style="width: 2%;"><i class="fa fa-filter" aria-hidden="true"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0;
                                    foreach ($tipopersonas_datos as $tipopersonas_dato) {

                                        $id_tipoPersona = $tipopersonas_dato['id_tipoPersona'];
                                    ?>
                                        <tr>
                                            <td><?php echo $contador = $contador + 1; ?></td>
                                            <td><?php echo $tipopersonas_dato['name_tipoPersona']; ?></td>
                                            <td><?php echo $tipopersonas_dato['descripcion']; ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-update<?php echo $id_tipoPersona; ?>">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </button>
                                                    <a href="#" onclick="confirmDelete(<?php echo $id_tipoPersona; ?>);" type="button" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </div>
                                                <script>
                                                    function confirmDelete(id) {
                                                        Swal.fire({
                                                            title: '¿Estás seguro de eliminar este tipo de persona?',
                                                            text: "No podrás revertir esto",
                                                            icon: 'warning',
                                                            showCancelButton: true,
                                                            confirmButtonColor: '#3085d6',
                                                            cancelButtonColor: '#d33',
                                                            confirmButtonText: 'Sí, bórralo'
                                                        }).then((result) => {
                                                            if (result.isConfirmed) {
                                                                window.location.href = "../app/controllers/tipopersonas/delete_tipopersonas.php?id=" + id;
                                                            }
                                                        });
                                                    }
                                                </script>
                                                <div class="modal fade" id="modal-update<?php echo $id_tipoPersona; ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="../app/controllers/tipopersonas/update_tipopersonas.php" method="POST">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Editar Tipo de Persona</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label>Nombre del Tipo de Persona</label>
                                                                            <input type="text" name="name_tipoPersona" class="form-control" value="<?php echo htmlspecialchars($tipopersonas_dato['name_tipoPersona']); ?>" required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Descripción</label>
                                                                            <select name="descripcion" class="form-control" required>
                                                                                <option value="" disabled>Seleccionar descripción</option>
                                                                                <option value="Cliente" <?php echo ($tipopersonas_dato['descripcion'] == 'Cliente') ? 'selected' : ''; ?>>Cliente</option>
                                                                                <option value="Proveedor" <?php echo ($tipopersonas_dato['descripcion'] == 'Proveedor') ? 'selected' : ''; ?>>Proveedor</option>
                                                                                <option value="Fletero" <?php echo ($tipopersonas_dato['descripcion'] == 'Fletero') ? 'selected' : ''; ?>>Fletero</option>
                                                                                <option value="Matadero" <?php echo ($tipopersonas_dato['descripcion'] == 'Matadero') ? 'selected' : ''; ?>>Matadero</option>
                                                                                <option value="Otros" <?php echo ($tipopersonas_dato['descripcion'] == 'Otros') ? 'selected' : ''; ?>>Otros</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer justify-content-between">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                                    <input type="hidden" name="id_tipoPersona" value="<?php echo $id_tipoPersona; ?>">
                                                                </div>
                                                            </form>
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
                <h4 class="modal-title">Crear nuevo Tipo de Persona</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name_tipoPersona">Nombre del tipo de persona <b>*</b></label>
                    <input type="text" id="name_tipoPersona" class="form-control" required>
                    <small style="color: red; display:none;" id="lbl_create_tipoPersona"> *Este campo es requerido</small>
                </div>
                <div class="form-group">
                    <label for="descripcion_tipoPersona">Descripción <b>*</b></label>
                    <select id="descripcion_tipoPersona" class="form-control" required>
                        <option value="" disabled selected>Seleccionar descripción</option>
                        <option value="Cliente">Cliente</option>
                        <option value="Proveedor">Proveedor</option>
                        <option value="Fletero">Fletero</option>
                        <option value="Matadero">Matadero</option>
                        <option value="Otros">Otros</option>
                    </select>
                    <small style="color: red; display:none;" id="lbl_create_descripcion"> *Este campo es requerido</small>
                </div>
                <input type="hidden" id="id_usuario_tipoPersona" class="form-control" value="<?php echo $id_usuario; ?>">
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_create_tipoPersona">Guardar</button>
            </div>
        </div>
    </div>
</div>


<script>
    $('#btn_create_tipoPersona').click(function() {
        var name_tipoPersona = $('#name_tipoPersona').val().trim();
        var descripcion = $('#descripcion_tipoPersona').val().trim();
        var id_usuario = $('#id_usuario_tipoPersona').val();

        // Restablecer advertencias
        $('#lbl_create_tipoPersona').hide();
        $('#lbl_create_descripcion').hide();

        // Validación básica
        var isValid = true;
        if (!name_tipoPersona) {
            $('#lbl_create_tipoPersona').show();
            $('#name_tipoPersona').focus();
            isValid = false;
        }

        if (!descripcion) {
            $('#lbl_create_descripcion').show();
            $('#descripcion_tipoPersona').focus();
            isValid = false;
        }

        // Solo envía datos si ambos campos son válidos.
        if (isValid) {
            var data = {
                name_tipoPersona: name_tipoPersona,
                descripcion: descripcion,
                id_usuario: id_usuario
            };

            var url = "../app/controllers/tipopersonas/create_tipopersonas.php"; // Asegúrate de que esta ruta es correcta

            $.post(url, data, function(response) {
                $('#modal-create').modal('hide');
                location.reload(); // O manejar la respuesta como prefieras.
            }).fail(function() {
                alert("Error al enviar los datos. Por favor, inténtalo de nuevo.");
            });
        }
    });
</script>