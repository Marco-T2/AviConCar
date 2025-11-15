<?php
include('../app/config.php');
//include('../layout/sesion.php');
include('../layout/parte11.php');
include('../app/controllers/contabilidad/listarmayorsubcuenta.php');
include('../app/controllers/gestion/listarperiodo.php');


$name_subCuenta = $listartransaccionessubcuentas[0]['name_subCuenta'] ?? 'Nombre no disponible';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary collapsed-card">
                        <div class="container mt-3">
                            <div class="row g-2">

                                <!-- Botón Aplicar Filtros e Imprimir -->
                                <div class="col-12 col-md-6 d-flex justify-content-md-end align-items-center flex-wrap">
                                    <button type="button" id="aplicarFiltros" class="btn btn-success btn-sm me-2 mb-2 mb-md-0" hidden>Aplicar</button>
                                    <span class="text-sm align-self-center mx-2"></span>
                                    <button type="button" id="imprimir" class="btn btn-warning btn-sm mb-2 mb-md-0" target="_blank" hidden><i class="fa fa-print fa-sm"></i> Imprimir</button>
                                    <span class="text-sm align-self-center mx-2"></span>
                                    <a href="<?php echo $URL; ?>/despachos/create.php?id=<?php echo $id_persona; ?>" type="button" class="btn btn-primary btn-sm mb-2 mb-md-0" hidden><i class="fa fa-plus fa-sm"></i> Despacho</i></a>
                                    <span class="text-sm align-self-center mx-2"></span>
                                    <a href="<?php echo $URL; ?>/comprobantes/create.php?id=<?php echo $id_persona; ?>" type="button" class="btn btn-primary btn-sm mb-2 mb-md-0" hidden><i class="fa fa-plus fa-sm"></i> Comprobante</i></a>
                                    <span class="text-sm align-self-center mx-2"></span>
                                    <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $URL . '/kardex/kardex.php'; ?>" class="btn btn-secondary btn-sm">Regresar</a>
                                </div>
                            </div>
                        </div>

                        <script>
                            $(document).ready(function() {
                                $('#aplicarFiltros').click(function() {
                                    var startDate = $('#fecha_inicio').val();
                                    var endDate = $('#fecha_fin').val();
                                    var idPersona = $('#id_persona').val(); // Obtener el ID de la persona

                                    // Formatea las fechas para mostrarlas en el span
                                    var startDateFormatted = formatDate(startDate);
                                    var endDateFormatted = formatDate(endDate);

                                    // Actualiza el texto del span con las fechas formateadas
                                    $('#dateRangeSpan').text(startDateFormatted + ' - ' + endDateFormatted);

                                    // Realiza la solicitud AJAX
                                    $.ajax({
                                        url: '../app/controllers/informes/listarkardexfiltro.php',
                                        type: 'POST',
                                        data: {
                                            fecha_inicio: startDate,
                                            fecha_fin: endDate,
                                            id_persona: idPersona // Incluir el ID en los datos enviados
                                        },
                                        success: function(response) {
                                            // Actualiza la tabla con la respuesta
                                            $('#example1 tbody').html(response);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error("Error: " + status + " " + error);
                                        }
                                    });
                                });

                                var baseURL = '<?php echo $URL; ?>';

                                $('#imprimir').click(function() {
                                    var startDate = $('#fecha_inicio').val();
                                    var endDate = $('#fecha_fin').val();
                                    var idPersona = $('#id_persona').val();
                                    window.open(baseURL + '/kardex/kardexprint.php?fecha_inicio=' + startDate + '&fecha_fin=' + endDate + '&id_persona=' + idPersona, '_blank');
                                });

                                function formatDate(dateString) {
                                    // Añadir "T00:00" para evitar la interpretación en UTC
                                    var date = new Date(dateString + 'T00:00');
                                    var day = ('0' + date.getDate()).slice(-2);
                                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                                    var year = date.getFullYear();
                                    return day + ' ' + getMonthName(parseInt(month, 10)) + ', ' + year;
                                }

                                function getMonthName(month) {
                                    var months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                                        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
                                    ];
                                    return months[month - 1];
                                }
                            });
                        </script>



                        <hr>
                        <div class="d-flex flex-column justify-content-center align-items-center text-center" style="gap: 0.10rem;">
                            <h4 style="font-size: 0.9rem; margin-bottom: 0;"><strong>AVICOLA EL CARMEN</strong></h4>
                            <span class="text-sm" style="font-size: 0.85rem; margin-bottom: 0;">Libro mayor - <?php echo htmlspecialchars($name_subCuenta); ?></span>
                        </div>



                        <div class="card-body" style="display: block;">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped table-sm" style="font-size: 0.85rem; vertical-align: middle;">
                                    <thead>
                                        <tr>
                                            <th style="width: 6%;">Fecha</th>
                                            <th style="width: 10%;">Doc</th>
                                            <th style="width: 5%;">Nro</th>
                                            <th style="width: 25%;">Detalle</th>
                                            <th style="width: 6%;">Debe</th>
                                            <th style="width: 6%;">Haber</th>
                                            <th style="width: 6%;">Saldo</th>
                                            <th style="width: 2%;" hidden>
                                                <center><i class="fa fa-filter" aria-hidden="true"></i></center>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $Saldo = 0;
                                        foreach ($listartransaccionessubcuentas as $listartransaccionessubcuent) {
                                            $Saldo += $listartransaccionessubcuent['debe'] - $listartransaccionessubcuent['haber'];
                                            $urlDestino = ""; // Inicializamos la variable
                                            switch ($listartransaccionessubcuent['nombre_categoria']) {
                                                case 'Comprobantes-Contables':
                                                    $urlDestino = $URL . "/comprobantes/show.php?id=" . $listartransaccionessubcuent['id_comprobante'];
                                                    break;
                                                case 'Comprobantes-Ventas':
                                                    $urlDestino = $URL . "/despachos/show.php?id=" . $listartransaccionessubcuent['id_comprobante'];
                                                    break;
                                                default:
                                                    $urlDestino = $URL . "/otra_categoria/update.php?id=" . $listartransaccionessubcuent['id_comprobante'];
                                                    break;
                                            }
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;"><?php echo date('d/m/Y', strtotime($listartransaccionessubcuent['fecha_comprobante'])); ?></td>
                                                <td style="vertical-align: middle;"><?php echo htmlspecialchars($listartransaccionessubcuent['name_tipocomprobante']); ?></td>
                                                <td style="vertical-align: middle;"><?php echo htmlspecialchars($listartransaccionessubcuent['num_comprobante']); ?></td>
                                                <td style="vertical-align: middle;"><?php echo htmlspecialchars($listartransaccionessubcuent['descripcion']); ?></td>
                                                <td style="vertical-align: middle; text-align: right;"><?php echo htmlspecialchars(number_format($listartransaccionessubcuent['debe'], 2)); ?></td>
                                                <td style="vertical-align: middle; text-align: right;"><?php echo htmlspecialchars(number_format($listartransaccionessubcuent['haber'], 2)); ?></td>
                                                <td style="vertical-align: middle; text-align: right;"><?php echo htmlspecialchars(number_format($Saldo, 2)); ?></td>
                                                <td style="vertical-align: middle;" hidden>
                                                    <div class="text-center">
                                                        <div class="btn-group">
                                                            <a href="<?php echo $urlDestino; ?>" class="btn btn-success btn-sm">
                                                                <i class="fa fa-eye fa-sm"></i>
                                                            </a>
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
            </div>
        </div><!-- /.container-fluid -->
    </div>
</div>
<!-- /.content-wrapper -->

<?php
include('../layout/parte22.php');
include('../layout/mensajes.php')
?>

<script>
    $(function() {
        $("#example1").DataTable({
            "pageLength": 20,
            "order": [],
            language: {
                "emptyTable": "No hay información",
                "decimal": "",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Clientes",
                "infoEmpty": "Mostrando 0 a 0 de 0 Usuarios",
                "infoFiltered": "(Filtrado de _MAX_ total Clientes)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Clientes",
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
            }, ],
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>