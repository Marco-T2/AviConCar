<?php
include('../app/config.php');
//include('../layout/sesion.php');
include('../layout/parte11.php');
include('../app/controllers/informes/listarpersona.php');
include('../app/controllers/informes/kardex.php');


include('../app/controllers/gestion/listarperiodo.php');

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
                                <!-- Seleccionar Periodo y Fecha Inicio/Fin -->
                                <div class="col-12 col-md-6 d-flex flex-wrap align-items-center">
                                    <input type="hidden" id="id_persona" value="<?php echo $id_persona ?>">
                                    <!-- Dropdown de Reporte -->
                                    <div class="me-2 mb-2 mb-md-0">
                                        <div class="dropdown">
                                            <label for="dropdownMenuButton" class="form-label text-sm">Reporte:</label>
                                            <select id="id_periodo" class="form-control form-control-sm btn-primary" name="id_periodo">
                                                <option value="" disabled selected>Seleccionar</option>
                                                <?php foreach ($periodos_datos as $periodo) { ?>
                                                    <option value="<?php echo $periodo['id_periodo']; ?>">
                                                        <?php echo $periodo['name_periodo']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <span class="text-sm align-self-center mx-2"></span>
                                    <!-- Fecha Inicio -->
                                    <div class="me-2 mb-2 mb-md-0">
                                        <label for="fecha_inicio" class="form-label text-sm">Fecha Inicio:</label>
                                        <input type="date" class="form-control form-control-sm" id="fecha_inicio" name="fecha_inicio" value=<?php echo $fechaQuincenal ?>>
                                    </div>

                                    <!-- Fecha Fin -->
                                    <div class="mb-2 mb-md-0">
                                        <label for="fecha_fin" class="form-label text-sm">Fecha Fin:</label>
                                        <input type="date" class="form-control form-control-sm" id="fecha_fin" name="fecha_fin" value=<?php echo $fecha ?>>
                                    </div>

                                    <script>
                                        $(document).ready(function() {
                                            $('#id_periodo').change(function() {
                                                var selectedPeriodId = $(this).val();

                                                switch (selectedPeriodId) {
                                                    case '1': // Hoy
                                                        setDates(new Date(), new Date());
                                                        break;
                                                    case '2': // Esta semana
                                                        setDates(getStartOfWeek(new Date()), getEndOfWeek(new Date()));
                                                        break;
                                                    case '3': // En lo que va de semana
                                                        setDates(getStartOfWeek(new Date()), new Date());
                                                        break;
                                                    case '4': // Este mes
                                                        setDates(getStartOfMonth(new Date()), getEndOfMonth(new Date()));
                                                        break;
                                                    case '5': // En lo que va de mes
                                                        setDates(getStartOfMonth(new Date()), new Date());
                                                        break;
                                                    case '6': // Este año
                                                        setDates(getStartOfYear(new Date()), getEndOfYear(new Date()));
                                                        break;
                                                    case '7': // En lo que va de año
                                                        setDates(getStartOfYear(new Date()), new Date());
                                                        break;
                                                        // ... continuar con los casos adicionales si los hay
                                                }
                                            });

                                            function setDates(startDate, endDate) {
                                                $('#fecha_inicio').val(formatDate(startDate));
                                                $('#fecha_fin').val(formatDate(endDate));
                                            }

                                            function formatDate(date) {
                                                var day = ('0' + date.getDate()).slice(-2);
                                                var month = ('0' + (date.getMonth() + 1)).slice(-2);
                                                var year = date.getFullYear();
                                                return year + '-' + month + '-' + day;
                                            }

                                            function getStartOfWeek(date) {
                                                var day = date.getDay();
                                                var diff = date.getDate() - day + (day === 0 ? -6 : 1); // Adjust to Monday
                                                return new Date(date.setDate(diff));
                                            }

                                            function getEndOfWeek(date) {
                                                var day = date.getDay();
                                                var diff = date.getDate() - day + (day === 0 ? -6 : 7); // Adjust to Sunday
                                                return new Date(date.setDate(diff));
                                            }

                                            function getStartOfMonth(date) {
                                                return new Date(date.getFullYear(), date.getMonth(), 1);
                                            }

                                            function getEndOfMonth(date) {
                                                return new Date(date.getFullYear(), date.getMonth() + 1, 0);
                                            }

                                            function getStartOfYear(date) {
                                                return new Date(date.getFullYear(), 0, 1);
                                            }

                                            function getEndOfYear(date) {
                                                return new Date(date.getFullYear(), 11, 31);
                                            }
                                        });
                                    </script>

                                </div>

                                <!-- Botón Aplicar Filtros e Imprimir -->
                                <div class="col-12 col-md-6 d-flex justify-content-md-end align-items-center flex-wrap">
                                    <button type="button" id="aplicarFiltros" class="btn btn-success btn-sm me-2 mb-2 mb-md-0">Aplicar</button>
                                    <span class="text-sm align-self-center mx-2"></span>
                                    <button type="button" id="imprimir" class="btn btn-warning btn-sm mb-2 mb-md-0" target="_blank"><i class="fa fa-print fa-sm"></i> Imprimir</button>
                                    <span class="text-sm align-self-center mx-2"></span>
                                    <a href="<?php echo $URL; ?>/cobranzas" type="button" class="btn btn-secondary btn-sm mb-2 mb-md-0">Regresar</i></a>
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
                            <span class="text-sm" style="font-size: 0.85rem; margin-bottom: 0;">Kardex - <?php echo $name_persona; ?></span>
                            <span id="dateRangeSpan" style="font-size: 0.85rem;">dia mes - dia mes, año</span>
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
                                            <th style="width: 2%;">
                                                <center><i class="fa fa-filter" aria-hidden="true"></i></center>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        <?php
                                        $Saldo = 0;
                                        foreach ($listarSaldoClientes_datos as $listarSaldoClientes_dato) {
                                            // Actualiza el saldo: suma el 'debe' y resta el 'haber'.
                                            $Saldo += $listarSaldoClientes_dato['debe'] - $listarSaldoClientes_dato['haber'];
                                            $urlDestino = "";

                                            switch ($listarSaldoClientes_dato['nombre_categoria']) {
                                                case 'Comprobantes-Contables':
                                                    $urlDestino = $URL . "/cobranzas/show2.php?id=" . $listarSaldoClientes_dato['id_comprobante'];
                                                    break;
                                                case 'Comprobantes-Ventas':
                                                    $urlDestino = $URL . "/cobranzas/show1.php?id=" . $listarSaldoClientes_dato['id_comprobante'];
                                                    break;
                                                default:
                                                    $urlDestino = $URL . "/otra_categoria/update.php?id=" . $listarSaldoClientes_dato['id_comprobante'];
                                                    break;
                                            }
                                        ?>
                                            <tr>
                                                <td style="vertical-align: middle;"><?php echo date('d/m/Y', strtotime($listarSaldoClientes_dato['fecha_comprobante'])); ?></td>
                                                <td style="vertical-align: middle;"><?php echo $listarSaldoClientes_dato['name_tipocomprobante']; ?></td>
                                                <td style="vertical-align: middle;"><?php echo $listarSaldoClientes_dato['num_comprobante']; ?></td>
                                                <td style="vertical-align: middle;"><?php echo $listarSaldoClientes_dato['descripcion']; ?></td>
                                                <td style="vertical-align: middle; text-align: right;"><?php echo number_format($listarSaldoClientes_dato['debe'], 2); ?></td>
                                                <td style="vertical-align: middle; text-align: right;"><?php echo number_format($listarSaldoClientes_dato['haber'], 2); ?></td>
                                                <td style="vertical-align: middle; text-align: right;"><?php echo number_format($Saldo, 2); ?></td>
                                                <td style="vertical-align: middle;">
                                                    <div class="text-center">
                                                        <div class="btn-group">
                                                            <a href="<?php echo $urlDestino; ?>" type="button" class="btn btn-success btn-sm">
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
            "pageLength": 10,
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