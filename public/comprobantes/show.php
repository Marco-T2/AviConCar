<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/tipocomprobantes/listado_tipocomprobantes.php');
include('../app/controllers/personas/listado_personas.php');
include('../app/controllers/subCuentas/listado_subCuentas.php');

include('../app/controllers/comprobantes/update_comprobantes.php')





?>

<!-- Content Wrapper. Contains page content -->

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mathjs/9.5.0/math.min.js"></script>

<!-- Content Wrapper. Contains page content -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title card-title-sm" style="font-size: 0.85rem">VISTA COMPROBANTE</h3>
                        </div>
                        <div class="card-body" style="display: block;">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- REGISTRAR EL METODO AL CONTROLADOR -->
                                    <form action="#" method="post">
                                        <div class="row mb-2">
                                            <div class="form-group" hidden>
                                                <input hidden type="text" id="id_comprobante" name="id_comprobante" class="form-control form-control-sm" value="<?php echo $id_comprobante; ?>">
                                                <input hidden type="text" id="id_personakardex" name="id_personakardex" class="form-control form-control-sm" value="<?php echo $id_personakardex; ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <?php
                                                $nombreTipocomprobanteSeleccionado = "";
                                                foreach ($tipocomprobantesComprobantes_datos as $tipocomprobantes_dato) {
                                                    if ($tipocomprobantes_dato['id_tipocomprobante'] == $id_tipocomprobante) {
                                                        $nombreTipocomprobanteSeleccionado = $tipocomprobantes_dato['name_tipocomprobante'];
                                                        break; // Encuentra la coincidencia y sale del bucle.
                                                    }
                                                }
                                                ?>
                                                <label for="id_tipocomprobante" class="form-label text-sm">Tipo de Comprobante:</label>
                                                <input type="text" class="form-control form-control-sm" id="id_tipocomprobante" name="id_tipocomprobante" value="<?php echo htmlspecialchars($nombreTipocomprobanteSeleccionado, ENT_QUOTES, 'UTF-8'); ?>" disabled>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label text-sm">Nro Comprobante</label>
                                                <input type="number" id="num_comprobante" name="num_comprobante" class="form-control form-control-sm" value="<?php echo $num_comprobante ?>" placeholder="#" disabled>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label text-sm">Fecha</label>
                                                <input type="date" id="fecha_comprobante" name="fecha_comprobante" class="form-control form-control-sm" value="<?php echo $fecha_comprobante; ?>" disabled>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label text-sm">Hora</label>
                                                <input type="time" id="hora_comprobante" name="hora_comprobante" class="form-control form-control-sm" value="<?php echo $hora_comprobante; ?>" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label text-sm">Descripcion</label>
                                            <input type="text" id="descripcion" name="descripcion" class="form-control form-control-sm" placeholder="Poner una descripcion" value="<?php echo $descripcionC; ?>" disabled>
                                        </div>
                                        <div class="form-group" hidden>
                                            <input type="text" id="id_usuario" name="id_usuario" class="form-control" value="<?php echo $id_usuario; ?>">
                                        </div>
                                        <hr>
                                        <div class="table-responsive">
                                            <table id="invoice_item_table" class="table table-bordered table-striped table-sm" style="font-size: 0.85rem; vertical-align: middle;">
                                                <tr>
                                                    <th style="width: 2%;"><i class="fa fa-list-ol" aria-hidden="true"></i></th>
                                                    <th style="width: 12%;">Cuenta</th>
                                                    <th style="width: 6%;">Debe</th>
                                                    <th style="width: 6%;">Haber</th>
                                                    <th style="width: 12%;">Descripcion</th>
                                                    <th style="width: 8%;">Persona</th>
                                                </tr>
                                                <?php
                                                $saldoDebe = 0;
                                                $saldoHaber = 0;

                                                $contador = 0;
                                                foreach ($transacciones_datos as $detalletransacciones_dato) {
                                                    $contador++;

                                                    // Acumula los valores de debe y haber en cada iteración
                                                    $saldoDebe += $detalletransacciones_dato['debe'];
                                                    $saldoHaber += $detalletransacciones_dato['haber'];
                                                ?>
                                                    <tr id="row_id_<?php echo $contador ?>">
                                                        <td><?php echo $contador; ?></td>
                                                        <?php
                                                        $nombreSubcuentaSeleccionada = ""; // Inicializa la variable antes del bucle
                                                        foreach ($subcuentasActivoCorriente_datos as $subcuentas_dato) {
                                                            if ($subcuentas_dato['id_subCuenta'] == $detalletransacciones_dato['id_subCuenta']) {
                                                                $nombreSubcuentaSeleccionada = $subcuentas_dato['name_subCuenta'];
                                                                break; // Encuentra la coincidencia y sale del bucle.
                                                            }
                                                        }
                                                        ?>
                                                        <td><?php echo htmlspecialchars($nombreSubcuentaSeleccionada, ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td style="text-align: right;"><?php echo htmlspecialchars($detalletransacciones_dato['debe'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td style="text-align: right;"><?php echo htmlspecialchars($detalletransacciones_dato['haber'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($detalletransacciones_dato['descripcion'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <?php
                                                        $nombrePersonaSeleccionada = ""; // Inicializa la variable antes del bucle
                                                        foreach ($personas_datos as $personas_dato) {
                                                            if ($personas_dato['id_persona'] == $detalletransacciones_dato['id_persona']) {
                                                                $nombrePersonaSeleccionada = $personas_dato['name_persona'];
                                                                break; // Encuentra la coincidencia y sale del bucle.
                                                            }
                                                        }
                                                        ?>
                                                        <td><?php echo htmlspecialchars($nombrePersonaSeleccionada, ENT_QUOTES, 'UTF-8'); ?></td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                                <tr id="row_id_final">
                                                    <td colspan="2"></td>
                                                    <th style="text-align: right;" id="total_debe"><?php echo number_format($saldoDebe, 2, '.', ''); ?></th>
                                                    <th style="text-align: right;" id="total_haber"><?php echo number_format($saldoHaber, 2, '.', ''); ?></th>
                                                    <th colspan="3"></th>
                                                </tr>
                                            </table>
                                        </div>
                                        <br>
                                        <?php
                                        $transaccionPrimera = reset($transacciones_datos);  // Obtiene la primera transacción.
                                        $id_persona = $transaccionPrimera['id_persona'];  // Obtiene el id_persona de la primera transacción.
                                        ?>
                                        <div class="modal-footer justify-content-between">
                                            <a href="<?php echo $URL; ?>/kardex/kardex.php?id=<?php echo $id_persona; ?>" class="btn btn-secondary btn-sm">Regresar</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
include('../layout/mensajes.php');
?>