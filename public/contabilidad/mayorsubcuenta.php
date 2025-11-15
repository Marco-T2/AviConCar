<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

// Controller que debe exponer: $listartransaccionessubcuentas, $name_subCuenta (opcional), y opcionalmente saldo_corriente por fila
include('../app/controllers/contabilidad/listarmayorsubcuenta.php');
include('../app/controllers/gestion/listarperiodo.php');

// Nombre de subcuenta (preferir variable del controller; fallback al primer registro)
if (!isset($name_subCuenta)) {
    $name_subCuenta = $listartransaccionessubcuentas[0]['name_subCuenta'] ?? 'Nombre no disponible';
}

// Resuelve id_subcuenta desde GET para la vista y el AJAX
$id_subcuenta_get = isset($_GET['id_subcuenta']) ? (int)$_GET['id_subcuenta'] : 0;

// Rango de fecha inicial para el span (usa lo que venga del controller o variables de periodo)
$fecha_inicio_v = $_GET['fecha_inicio'] ?? ($fechaQuincenal ?? '');
$fecha_fin_v    = $_GET['fecha_fin']    ?? ($fecha ?? '');
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

                                    <!-- id_subcuenta para AJAX -->
                                    <input type="hidden" id="id_subcuenta" value="<?php echo (int)$id_subcuenta_get; ?>">

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
                                        <input type="date" class="form-control form-control-sm" id="fecha_inicio" name="fecha_inicio"
                                               value="<?php echo htmlspecialchars($fecha_inicio_v); ?>">
                                    </div>

                                    <!-- Fecha Fin -->
                                    <div class="mb-2 mb-md-0">
                                        <label for="fecha_fin" class="form-label text-sm">Fecha Fin:</label>
                                        <input type="date" class="form-control form-control-sm" id="fecha_fin" name="fecha_fin"
                                               value="<?php echo htmlspecialchars($fecha_fin_v); ?>">
                                    </div>

                                    <script>
                                        $(document).ready(function () {
                                            $('#id_periodo').change(function () {
                                                var selectedPeriodId = $(this).val();

                                                switch (selectedPeriodId) {
                                                    case '1': setDates(new Date(), new Date()); break; // Hoy
                                                    case '2': setDates(getStartOfWeek(new Date()), getEndOfWeek(new Date())); break; // Esta semana
                                                    case '3': setDates(getStartOfWeek(new Date()), new Date()); break; // En lo que va de semana
                                                    case '4': setDates(getStartOfMonth(new Date()), getEndOfMonth(new Date())); break; // Este mes
                                                    case '5': setDates(getStartOfMonth(new Date()), new Date()); break; // En lo que va de mes
                                                    case '6': setDates(getStartOfYear(new Date()), getEndOfYear(new Date())); break; // Este año
                                                    case '7': setDates(getStartOfYear(new Date()), new Date()); break; // En lo que va de año
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
                                                var d = new Date(date);
                                                var day = d.getDay();
                                                var diff = d.getDate() - day + (day === 0 ? -6 : 1); // Lunes
                                                return new Date(d.setDate(diff));
                                            }

                                            function getEndOfWeek(date) {
                                                var d = new Date(date);
                                                var day = d.getDay();
                                                var diff = d.getDate() - day + (day === 0 ? -6 : 7); // Domingo
                                                return new Date(d.setDate(diff));
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

                                    <!-- Dejas oculto imprimir si aún no tienes endpoint de print para MAYOR -->
                                    <button type="button" id="imprimir" class="btn btn-warning btn-sm mb-2 mb-md-0" target="_blank" hidden>
                                        <i class="fa fa-print fa-sm"></i> Imprimir
                                    </button>

                                    <span class="text-sm align-self-center mx-2"></span>
                                    <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $URL . '/kardex/kardex.php'; ?>" class="btn btn-secondary btn-sm">Regresar</a>
                                </div>
                            </div>
                        </div>

                        <script>
                            $(document).ready(function () {
                                // Pinta el rango en el encabezado apenas carga
                                (function initRangeSpan() {
                                    var startDate = $('#fecha_inicio').val();
                                    var endDate = $('#fecha_fin').val();
                                    if (startDate && endDate) {
                                        $('#dateRangeSpan').text(formatDateHuman(startDate) + ' - ' + formatDateHuman(endDate));
                                    }
                                })();

                                $('#aplicarFiltros').click(function () {
                                    var startDate = $('#fecha_inicio').val();
                                    var endDate = $('#fecha_fin').val();
                                    var idSubcuenta = $('#id_subcuenta').val();

                                    if (!idSubcuenta) {
                                        alert('Falta id_subcuenta');
                                        return;
                                    }

                                    // Actualiza el span
                                    $('#dateRangeSpan').text(formatDateHuman(startDate) + ' - ' + formatDateHuman(endDate));

                                    // AJAX para refrescar cuerpo de la tabla
                                    $.ajax({
                                        url: '../app/controllers/informes/listarkardexfiltro.php',
                                        type: 'POST',
                                        data: {
                                            fecha_inicio: startDate,
                                            fecha_fin: endDate,
                                            id_subcuenta: idSubcuenta
                                        },
                                        success: function (response) {
                                            $('#example1 tbody').html(response);
                                        },
                                        error: function (xhr, status, error) {
                                            console.error("Error: " + status + " " + error);
                                        }
                                    });
                                });

                                function formatDateHuman(dateString) {
                                    var date = new Date(dateString + 'T00:00');
                                    var day = ('0' + date.getDate()).slice(-2);
                                    var months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                                        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                                    var monthName = months[date.getMonth()];
                                    var year = date.getFullYear();
                                    return day + ' ' + monthName + ', ' + year;
                                }
                            });
                        </script>

                        <hr>
                        <div class="d-flex flex-column justify-content-center align-items-center text-center" style="gap: 0.10rem;">
                            <h4 style="font-size: 0.9rem; margin-bottom: 0;"><strong>AVICOLA EL CARMEN</strong></h4>
                            <span class="text-sm" style="font-size: 0.85rem; margin-bottom: 0;">
                                Libro mayor - <?php echo htmlspecialchars($name_subCuenta); ?>
                            </span>
                            <span id="dateRangeSpan" style="font-size: 0.85rem;">día mes - día mes, año</span>
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
                                        // Fallback en PHP si el controller aún no devuelve saldo_corriente
                                        $SaldoAcum = 0.0;

                                        if (empty($listartransaccionessubcuentas)) {
                                            echo "<tr><td colspan='8' class='text-center text-muted'>Sin movimientos en el rango</td></tr>";
                                        } else {
                                            foreach ($listartransaccionessubcuentas as $row) {

                                                // Construye link destino por categoría
                                                $urlDestino = $URL . "/otra_categoria/update.php?id=" . ($row['id_comprobante'] ?? 0);
                                                if (!empty($row['nombre_categoria'])) {
                                                    switch ($row['nombre_categoria']) {
                                                        case 'Comprobantes-Contables':
                                                            $urlDestino = $URL . "/comprobantes/show.php?id=" . (int)$row['id_comprobante'];
                                                            break;
                                                        case 'Comprobantes-Ventas':
                                                            $urlDestino = $URL . "/despachos/show.php?id=" . (int)$row['id_comprobante'];
                                                            break;
                                                    }
                                                }

                                                // Saldo mostrado: si viene saldo_corriente desde SQL úsalo; si no, acumula aquí
                                                if (isset($row['saldo_corriente'])) {
                                                    $saldoMostrar = (float)$row['saldo_corriente'];
                                                } else {
                                                    $SaldoAcum += ((float)$row['debe'] - (float)$row['haber']);
                                                    $saldoMostrar = $SaldoAcum;
                                                }
                                                ?>
                                                <tr>
                                                    <td style="vertical-align: middle;"><?php echo !empty($row['fecha_comprobante']) ? date('d/m/Y', strtotime($row['fecha_comprobante'])) : ''; ?></td>
                                                    <td style="vertical-align: middle;"><?php echo htmlspecialchars($row['name_tipocomprobante'] ?? ''); ?></td>
                                                    <td style="vertical-align: middle;"><?php echo htmlspecialchars($row['num_comprobante'] ?? ''); ?></td>
                                                    <td style="vertical-align: middle;"><?php echo htmlspecialchars($row['descripcion'] ?? ''); ?></td>
                                                    <td style="vertical-align: middle; text-align: right;"><?php echo number_format((float)($row['debe'] ?? 0), 2); ?></td>
                                                    <td style="vertical-align: middle; text-align: right;"><?php echo number_format((float)($row['haber'] ?? 0), 2); ?></td>
                                                    <td style="vertical-align: middle; text-align: right;"><?php echo number_format((float)$saldoMostrar, 2); ?></td>
                                                    <td style="vertical-align: middle;" hidden>
                                                        <div class="text-center">
                                                            <div class="btn-group">
                                                                <a href="<?php echo htmlspecialchars($urlDestino, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-success btn-sm">
                                                                    <i class="fa fa-eye fa-sm"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>

                    </div> <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div>
</div>
<!-- /.content-wrapper -->

<?php
include('../layout/parte2.php');
include('../layout/mensajes.php')
?>

<script>
    $(function () {
        $("#example1").DataTable({
            pageLength: 20,
            order: [],
            language: {
                emptyTable: "No hay información",
                decimal: "",
                info: "Mostrando _START_ a _END_ de _TOTAL_ Registros",
                infoEmpty: "Mostrando 0 a 0 de 0 Registros",
                infoFiltered: "(Filtrado de _MAX_ total Registros)",
                thousands: ",",
                lengthMenu: "Mostrar _MENU_ Registros",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                search: "Buscador:",
                zeroRecords: "Sin resultados encontrados",
                paginate: { first: "Primero", last: "Último", next: "Siguiente", previous: "Anterior" }
            },
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            buttons: [{
                extend: 'collection',
                text: 'Reportes',
                orientation: 'landscape',
                buttons: [
                    { text: 'Copiar', extend: 'copy' },
                    { extend: 'pdf' },
                    { extend: 'csv' },
                    { extend: 'excel' },
                    { text: 'Imprimir', extend: 'print' }
                ]
            }],
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>
