<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/cuentas/listado_cuentas.php');
include('../app/controllers/subCuentas/listado_subCuentas.php');


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Listado de Sub cuentas</h1>

                    <br>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-create">
                        <i class="fa fa-plus"></i> Nueva Subcuenta
                    </button>
                    <input id="idsDropdownTypeaheadTextField23" aria-invalid="false" class="idsF TextField-TFInput-6b79cba TextField-quickbooks-3c83307 TextField-light-d154fc2 TextField-TFNoErrorText-8f1be96 TextField-TFNotDisabled-13357f4 TextField-TFAddonAfter-854667c" type="text" placeholder=" " aria-required="false" data-testid="__textField" aria-autocomplete="list" aria-controls="idsDropdownTypeahead22-idsMenu" aria-expanded="true" aria-haspopup="listbox" aria-label="cuenta" autocomplete="off" role="combobox" tabindex="0" value="">
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">Sub cuentas</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                                </button>
                            </div>
                            
                        </div>
                        <div class="card-body" style="display: block;">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nro</th>
                                        <th>Sub cuenta</th>
                                        <th>Cuenta</th>
                                        <th>Registrado por</th>
                                        <th>
                                            <center>Acciones</center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0;
                                    foreach ($subcuentas_datos as $subcuentas_dato) {

                                        $id_subCuenta = $subcuentas_dato['id_subCuenta'];
                                    ?>
                                        <tr>
                                            <td><?php echo $contador = $contador + 1; ?></td>
                                            <td><?php echo $subcuentas_dato['name_subCuenta']; ?></td>
                                            <td><?php echo $subcuentas_dato['id_cuenta'];
                                                echo " ";
                                                echo $subcuentas_dato['name_cuenta']; ?></td>
                                            <td><?php echo $subcuentas_dato['nombres']; ?></td>
                                            <td>
                                                <center>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-update<?php echo $id_subCuenta; ?>">
                                                            <i class="fa fa-plus"></i> Editar
                                                        </button>
                                            

                                                        <!-- /.modal registrar cuentas -->
                                                        <div class="modal fade" id="modal-update<?php echo $id_subCuenta; ?>">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Actualizar Sub-cuenta</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                        
                                                                    </div>
                                                                    <div class="modal-body">

                                                                        <div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <div class="form-group">
                                                                                        <label for="">Nombre de la subcuenta <b>*</b></label>
                                                                                        <input type="text" id="name_subcuenta<?php echo $id_subCuenta; ?>" class="form-control" value="<?php echo $subcuentas_dato['name_subCuenta']; ?>" required>
                                                                                        <small style="color: red; display:none;" id="lbl_create4<?php echo $id_subCuenta; ?>"> *Este campo es requerido</small>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="">Cuenta <b>*</b></label>
                                                                                        <select id="id_cuenta<?php echo $id_subCuenta; ?>" class="class form-control">
                                                                                            <option value="" disabled selected>Seleccionar cuenta</option>
                                                                                            <?php
                                                                                            foreach ($cuentas_datos as $cuentas_dato) {
                                                                                            ?>
                                                                                                <option value="<?php echo $cuentas_dato['id_cuenta']; ?>"><?php echo $cuentas_dato['name_cuenta']; ?></option>
                                                                                            <?php
                                                                                            }
                                                                                            ?>
                                                                                        </select>
                                                                                        <small style="color: red; display:none;" id="lbl_create5<?php echo $id_subCuenta; ?>"> *Seleccion una cuenta </small>
                                                                                    </div>
                                                                                    <div class="form-group" hidden>
                                                                                        <input type="text" id="id_usuario<?php echo $id_subCuenta; ?>" class="form-control" value="<?php echo $id_usuario ?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                        <button type="button" class="btn btn-primary" id="btn_update<?php echo $id_subCuenta; ?>">Guardar</button>

                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </div>
                                                            <!-- /.modal-dialog -->
                                                        </div>
                                                        <!-- /.modal -->
                                                        <script>
                                                            $('#btn_update<?php echo $id_subCuenta ?>').click(function() {
                                                                var id_subCuenta = '<?php echo $id_subCuenta; ?>';
                                                                var name_subcuenta = $('#name_subcuenta<?php echo $id_subCuenta; ?>').val();
                                                                var id_cuenta = $('#id_cuenta<?php echo $id_subCuenta; ?>').val();
                                                                var id_usuario = $('#id_usuario<?php echo $id_subCuenta; ?>').val();

                                                                console.log("Enviando datos al servidor:");
                                                                console.log("name_subcuenta:", name_subcuenta);
                                                                console.log("id_cuenta:", id_cuenta);
                                                                console.log("id_usuario:", id_usuario);

                                                                if (name_subcuenta == "" || id_cuenta === null || id_cuenta === undefined || id_cuenta == 0) {
                                                                    // Al menos uno de los campos está vacío, muestra la advertencia correspondiente
                                                                    if (name_subcuenta == "") {
                                                                        $('#name_subcuenta<?php echo $id_subCuenta; ?>').focus();
                                                                        $('#lbl_create4<?php echo $id_subCuenta; ?>').css('display', 'block');
                                                                    }

                                                                    if (id_cuenta === null || id_cuenta === undefined || id_cuenta == 0) {
                                                                        $('#id_cuenta<?php echo $id_subCuenta; ?>').focus();
                                                                        $('#lbl_create5<?php echo $id_subCuenta; ?>').css('display', 'block');
                                                                    }
                                                                } else {
                                                                    // Ambos campos están llenos, realiza la operación
                                                                    var url = "../app/controllers/subCuentas/update_de_subcuentas.php";

                                                                    $.get(url, {
                                                                        id_subCuenta: id_subCuenta,
                                                                        name_subcuenta: name_subcuenta,
                                                                        id_cuenta: id_cuenta,
                                                                        id_usuario: id_usuario
                                                                    }, function(datos) {
                                                                        $('#respuesta_update<?php echo $id_subCuenta; ?>').html(datos);
                                                                    });
                                                                }
                                                            })
                                                        </script>
                                                        <div id="respuesta_update<?php echo $id_subCuenta; ?>"></div>

                                                    </div>


                                                </center>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Nro</th>
                                        <th>Sub cuenta</th>
                                        <th>Cuenta</th>
                                        <th>Registrado por</th>
                                        <th>
                                            <center>Acciones</center>
                                        </th>
                                    </tr>
                                </tfoot>
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

<script>
    $(function() {
        $("#example1").DataTable({
            "pageLength": 5,
            language: {
                "emptyTable": "No hay información",
                "decimal": "",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ SubCuentas",
                "infoEmpty": "Mostrando 0 a 0 de 0 SubCuentas",
                "infoFiltered": "(Filtrado de _MAX_ total SubCuentas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ SubCuentas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscador:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            buttons: [{
                    extend: 'collection',
                    text: 'Reportes',
                    orientation: 'landscape',
                    buttons: [{
                        text: 'Copiar',
                        extend: 'copy'
                    }, {
                        extend: 'pdf',
                    }, {
                        extend: 'csv',
                    }, {
                        extend: 'excel',
                    }, {
                        text: 'Imprimir',
                        extend: 'print'
                    }]
                },
                {
                    extend: 'colvis',
                    text: 'Filtro de columnas'
                }
            ],
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<!-- /.modal registrar cuentas -->
<div class="modal fade" id="modal-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Crear nueva Sub cuenta</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-group">
                                <label for="">Nombre de la subcuenta <b>*</b></label>
                                <input type="text" id="name_subcuenta" class="form-control" required>
                                <small style="color: red; display:none;" id="lbl_create"> *Este campo es requerido</small>
                            </div>
                            <div class="form-group">
                                <label for="">Cuenta <b>*</b></label>
                                <select id="id_cuenta" class="class form-control">
                                    <option value="" disabled selected>Seleccionar cuenta</option>
                                    <?php
                                    foreach ($cuentas_datos as $cuentas_dato) {
                                    ?>
                                        <option value="<?php echo $cuentas_dato['id_cuenta']; ?>"><?php echo $cuentas_dato['name_cuenta']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <small style="color: red; display:none;" id="lbl_create2"> *Seleccion una cuenta </small>
                            </div>
                            <div class="form-group" hidden>
                                <input type="text" id="id_usuario" class="form-control" value="<?php echo $id_usuario ?>">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_create">Guardar</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
    $('#btn_create').click(function() {
        var name_subcuenta = $('#name_subcuenta').val();
        var id_cuenta = $('#id_cuenta').val();
        var id_usuario = $('#id_usuario').val();

        if (name_subcuenta == "" || id_cuenta === null || id_cuenta === undefined || id_cuenta == 0) {
            // Al menos uno de los campos está vacío, muestra la advertencia correspondiente
            if (name_subcuenta == "") {
                $('#name_subcuenta').focus();
                $('#lbl_create').css('display', 'block');
            }

            if (id_cuenta === null || id_cuenta === undefined || id_cuenta == 0) {
                $('#id_cuenta').focus();
                $('#lbl_create2').css('display', 'block');
            }
        } else {
            // Ambos campos están llenos, realiza la operación
            var url = "../app/controllers/subCuentas/registro_de_subcuentas.php";
            $.get(url, {
                name_subcuenta: name_subcuenta,
                id_cuenta: id_cuenta,
                id_usuario: id_usuario
            }, function(datos) {
                $('#respuesta').html(datos);
            });
        }
    });
</script>
<div id="respuesta"></div>