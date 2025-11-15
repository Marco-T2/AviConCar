<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/contabilidad/listargrupos.php');
include('../app/controllers/contabilidad/listarsubgrupos.php');
include('../app/controllers/contabilidad/listarcuentas.php');
include('../app/controllers/contabilidad/listarsubcuentas.php');

$currentFile = basename($_SERVER['PHP_SELF']);

?>

<!-- Inclusión de CSS de Select2 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mathjs/9.5.0/math.min.js"></script>

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
                            <span class="text-sm" style="font-size: 0.85rem; margin-bottom: 0; ">Subcuentas</span>
                        </div>
                        <div class="text-left"> <!-- Alinea el botón a la derecha -->
                            <button type="button" style="margin-left: 1.5rem;" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-create">
                                <i class="fa fa-plus fa-sm"></i> Nuevo Subcuenta
                            </button>
                        </div>
                        <div class="card-body" style="display: block;">
                            <table id="example1" class="table table-bordered table-striped table-sm" style="font-size: 0.85rem; vertical-align: middle;">
                                <thead>
                                    <tr>
                                        <th style="width: 2%;"><i class="fa fa-list-ol" aria-hidden="true"></i></th>
                                        <th style="width: 12%;">Nombre</th>
                                        <th style="width: 8%;">Grupo</th>
                                        <th style="width: 15%;">Sub-Grupo</th>
                                        <th style="width: 10%;">Cuenta</th>
                                        <th style="width: 5%;">PATH</th>
                                        <th style="width: 2%; text-align: center;"><i class="fa fa-filter fa-sm" aria-hidden="true"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0;
                                    foreach ($subcuentas_datos as $subcuentas_dato) {

                                        $id_subcuenta = $subcuentas_dato['id_subcuenta'];
                                    ?>
                                        <tr>
                                            <td style="vertical-align: middle;"><?php echo $contador = $contador + 1; ?></td>
                                            <td style="vertical-align: middle;"><?php echo htmlspecialchars($subcuentas_dato['name_subcuenta']); ?></td>
                                            <td style="vertical-align: middle;"><?php echo htmlspecialchars($subcuentas_dato['grupo_path'] . ' - ' . $subcuentas_dato['name_grupo']); ?></td>
                                            <td style="vertical-align: middle;"><?php echo htmlspecialchars($subcuentas_dato['subgrupo_path'] . ' - ' . $subcuentas_dato['name_subgrupo']); ?></td>
                                            <td style="vertical-align: middle;"><?php echo htmlspecialchars($subcuentas_dato['cuenta_path'] . ' - ' . $subcuentas_dato['name_cuenta']); ?></td>
                                            <td style="vertical-align: middle;"><?php echo htmlspecialchars($subcuentas_dato['subcuenta_path']); ?></td>
                                            <td style="vertical-align: middle;text-align: center;">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-update<?php echo $id_subcuenta; ?>">
                                                        <i class="fa fa-pencil-alt fa-sm"></i>
                                                    </button>
                                                    <a href="#" onclick="confirmDelete(<?php echo $id_subcuenta; ?>);" type="button" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash fa-sm"></i>
                                                    </a>
                                                    <script>
                                                        function confirmDelete(id) {
                                                            Swal.fire({
                                                                title: '¿Estás seguro de eliminar esta subcuenta?',
                                                                text: "No podrás revertir esto",
                                                                icon: 'warning',
                                                                showCancelButton: true,
                                                                confirmButtonColor: '#3085d6',
                                                                cancelButtonColor: '#d33',
                                                                confirmButtonText: 'Sí, bórralo'
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    window.location.href = "../app/controllers/contabilidad/deletesubcuentas.php?id=" + id;
                                                                }
                                                            });
                                                        }
                                                    </script>

                                                    <!-- /.modal editar cuentas -->
                                                    <div class="modal fade" id="modal-update<?php echo $id_subcuenta; ?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form action="../app/controllers/contabilidad/update_subcuentas.php" method="POST">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Editar Subcuenta</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Nombre de la subcuenta</label>
                                                                                <input type="text" name="name_subcuenta" class="form-control" value="<?php echo htmlspecialchars($subcuentas_dato['name_subcuenta']); ?>" required>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Cuenta</label>
                                                                                <br>
                                                                                <select name="id_cuenta" class="form-control select2" required>
                                                                                    <option value="" disabled>Seleccionar cuenta</option>
                                                                                    <?php foreach ($cuentas_datos as $cuenta) : ?>
                                                                                        <option value="<?php echo htmlspecialchars($cuenta['id_cuenta']); ?>" <?php echo ($cuenta['id_cuenta'] == $subcuentas_dato['id_cuenta']) ? 'selected' : ''; ?>>
                                                                                            <?php echo htmlspecialchars($cuenta['cuenta_path'] . ' - ' . $cuenta['name_cuenta']); ?>
                                                                                        </option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                                                        <input type="hidden" name="id_subcuenta" value="<?php echo $id_subcuenta; ?>">
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

                                    <script>
                                        $(document).ready(function() {
                                            $('.select2').select2({
                                                placeholder: 'Seleccionar',
                                                theme: 'Bootstrap',
                                                width: '200px',
                                            });
                                        });
                                    </script>
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


<script>
    $(function() {
        var table = $("#example1").DataTable({
            "pageLength": 5,
            language: {
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ SubCuentas",
                "infoEmpty": "Mostrando 0 a 0 de 0 SubCuentas",
                "infoFiltered": "(Filtrado de _MAX_ total SubCuentas)",
                "lengthMenu": "Mostrar _MENU_ SubCuentas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscador:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "searching": true,
            "search": {
                "smart": true,
                "search": function(search) {
                    if (search.length >= 3) {
                        table.column(1).search(search).draw();
                    }
                    if (search.length === 0) {
                        table.column(1).search('').draw();
                    }
                }
            }
        });

        // Evento para manejar la entrada de búsqueda personalizada
        $('input[type="search"]').on('keyup', function() {
            var searchTerm = $(this).val();
            table.search(searchTerm).draw();
        });
    });
</script>

<!-- /.modal registrar subcuentas -->
<div class="modal fade" id="modal-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Crear nueva subcuenta</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name_subcuenta">Nombre de la subcuenta <b>*</b></label>
                        <input type="text" id="name_subcuenta" class="form-control" required>
                        <small style="color: red; display:none;" id="lbl_create_subcuenta"> *Este campo es requerido</small>
                    </div>
                    <div class="form-group">
                        <label for="id_cuenta">Cuenta <b>*</b></label>
                        <br>
                        <select id="id_cuenta" class="form-control select2" required>
                            <option value="" disabled selected>Seleccionar cuenta</option>
                            <?php foreach ($cuentas_datos as $cuenta) : ?>
                                <option value="<?php echo $cuenta['id_cuenta']; ?>">
                                    <?php echo htmlspecialchars($cuenta['cuenta_path'] . ' - ' . $cuenta['name_cuenta']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small style="color: red; display:none;" id="lbl_create_cuenta"> *Este campo es requerido</small>
                    </div>
                    <input type="hidden" id="id_usuario_subcuenta" value="<?php echo htmlspecialchars($id_usuario); ?>">
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_create_subcuenta">Guardar</button>
            </div>
        </div>
    </div>
</div>



<script>
    $('#btn_create_subcuenta').click(function() {
        var name_subcuenta = $('#name_subcuenta').val().trim();
        var id_cuenta = $('#id_cuenta').val();
        var id_usuario = $('#id_usuario_subcuenta').val();

        // Restablecer advertencias
        $('#lbl_create_subcuenta, #lbl_create_cuenta').hide();

        // Validación básica
        var isValid = true;
        if (!name_subcuenta) {
            $('#lbl_create_subcuenta').show();
            $('#name_subcuenta').focus();
            isValid = false;
        }

        if (!id_cuenta) {
            $('#lbl_create_cuenta').show();
            $('#id_cuenta').focus();
            isValid = false;
        }

        if (isValid) {
            $.ajax({
                url: "../app/controllers/contabilidad/create_subcuenta.php",
                type: "POST",
                data: {
                    name_subcuenta: name_subcuenta,
                    id_cuenta: id_cuenta,
                    id_usuario: id_usuario
                },
                success: function(response) {
                    // Recargar la página o manejar la respuesta
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Manejar errores
                    alert("Ocurrió un error: " + error);
                }
            });
        }
    });
</script>

<div id="respuesta"></div>