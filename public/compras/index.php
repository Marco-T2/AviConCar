<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/despachos/listado_despachos.php');
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <a href="create.php" type="button" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Nuevo despacho</a>
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
                            <h3 class="card-title">Despacho</h3>
                        </div>
                        <div class="card-body" style="display: block;">
                            <table id="example1" class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Nro</th>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Nro </th>
                                        <th>Cliente</th>
                                        <th>VentaBs</th>
                                        <th>Descripcion</th>
                                        <th>
                                            <center>Acciones</center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0;
                                    foreach ($transacciones_datos as $transacciones_dato) {

                                        $id_comprobante = $transacciones_dato['id_comprobante'];
                                    ?>
                                        <tr>
                                            <td><?php echo $contador = $contador + 1; ?></td>
                                            <td><?php echo $transacciones_dato['fecha_comprobante']; ?></td>
                                            <td><?php echo $transacciones_dato['name_tipocomprobante']; ?></td>
                                            <td><?php echo $transacciones_dato['num_comprobante']; ?></td>
                                            <td><?php echo $transacciones_dato['name_persona']; ?></td>
                                            <td><?php echo $transacciones_dato['subTotal']; ?></td>
                                            <td><?php echo $transacciones_dato['descripcion']; ?></td>
                                            <td>
                                                <center>
                                                    <div class="btn-group">
                                                        <a href="print.php?id=<?php echo $id_comprobante; ?>" type="button" class="btn btn-warning btn-sm"><i class="fas fa-print"></i></a>
                                                        <a href="update.php?id=<?php echo $id_comprobante; ?>" type="button" class="btn btn-success btn-sm"><i class="fa fa-pencil-alt"></i></a>
                                                        <a href="#" onclick="confirmDelete(<?php echo $id_comprobante; ?>);" type="button" class="btn btn-danger btn-sm">
                                                            <i class="fa fa-trash"></i>
                                                        </a>

                                                        <script>
                                                            function confirmDelete(id) {
                                                                Swal.fire({
                                                                    title: '¿Estás seguro?',
                                                                    text: "No podrás revertir esto",
                                                                    icon: 'warning',
                                                                    showCancelButton: true,
                                                                    confirmButtonColor: '#3085d6',
                                                                    cancelButtonColor: '#d33',
                                                                    confirmButtonText: 'Sí, bórralo'
                                                                }).then((result) => {
                                                                    if (result.isConfirmed) {
                                                                        window.location.href = "../app/controllers/despachos/delete.php?id=" + id;
                                                                    }
                                                                });
                                                            }
                                                        </script>
                                                    </div>
                                                </center>
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

<script>
    if (sessionStorage.getItem('mensaje') && sessionStorage.getItem('icono')) {
        Swal.fire({
            icon: sessionStorage.getItem('icono'),
            title: sessionStorage.getItem('mensaje'),
            showConfirmButton: false,
            timer: 1500
        });

        // Limpiar sessionStorage después de mostrar el mensaje
        sessionStorage.removeItem('mensaje');
        sessionStorage.removeItem('icono');
    }
</script>
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
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Despacho",
                "infoEmpty": "Mostrando 0 a 0 de 0 Despacho",
                "infoFiltered": "(Filtrado de _MAX_ total Despacho)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Despacho",
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
</script>