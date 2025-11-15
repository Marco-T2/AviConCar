<?php
include('../app/config.php');
//include('../layout/sesion.php');
include('../layout/parte11.php');
include('../app/controllers/tipocomprobantes/listado_tipocomprobantes.php');
include('../app/controllers/subCuentas/listado_subCuentas.php');
include('../app/controllers/personas/listado_personas.php');
include('../app/controllers/tipoproducto/listado_tipoproducto.php');

include('../app/controllers/despachos/update_detalleList.php');

$transaccion1 = $transacccionescuentas_datos[0] ?? null;
$transaccion2 = $transacccionescuentas_datos[1] ?? null;

$total = $transaccion1 ? $transaccion1['debe'] : 0;
?>

<!-- Content Wrapper. Contains page content -->

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mathjs/9.5.0/math.min.js"></script>

<!-- Content Wrapper. Contains page content -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title card-title-sm" style="font-size: 0.85rem">VISTA DESPACHO</h3>
                        </div>
                        <div class="card-body" style="display: block;">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- REGISTRAR EL METODO AL CONTROLADOR -->
                                    <form action="#" method="post">
                                        <div class="row mb-2">
                                            <div class="form-group" hidden>
                                                <input type="text" id="id_comprobante" name="id_comprobante" class="form-control" value="<?php echo $id_comprobante; ?>">
                                            </div>
                                            <div class="col-md-3" hidden>
                                                <?php
                                                $nombreTipocomprobanteSeleccionado = "";
                                                foreach ($tipocomprobantesVentas_datos as $tipocomprobantes_dato) {
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
                                                <label class="form-label text-sm" for="">CLIENTE</label><br>
                                                <?php
                                                $nombrePersonaSeleccionada = "";
                                                foreach ($personas_datos as $personas_dato) {
                                                    if ($personas_dato['id_persona'] == $id_persona) {
                                                        $nombrePersonaSeleccionada = $personas_dato['id_persona'] . ' - ' . $personas_dato['name_persona'];
                                                        break;  // Sale del bucle una vez que encuentra la coincidencia correcta.
                                                    }
                                                }
                                                ?>
                                                <span class="form-control form-control-sm"><?php echo htmlspecialchars($nombrePersonaSeleccionada, ENT_QUOTES, 'UTF-8'); ?></span>
                                                <small style="color: red; display:none;" id="lbl_persona"> *Este campo es requerido</small>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label text-sm">Nro DESPACHO</label>
                                                <input type="number" id="num_comprobante" name="num_comprobante" class="form-control form-control-sm" placeholder="#" required value="<?php echo $num_comprobante ?>" disabled>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label text-sm">FECHA</label>
                                                <input type="date" id="fecha_comprobante" name="fecha_comprobante" class="form-control form-control-sm" value="<?php echo $fecha_comprobante; ?>" required disabled>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label text-sm">HORA</label>
                                                <input type="time" name="hora_comprobante" id="hora_comprobante" class="form-control form-control-sm" value="<?php echo $hora_comprobante; ?>" required disabled>
                                            </div>
                                        </div>
                                        <div class="row mb-2" hidden>
                                            <div class="col-md-3">
                                                <label class="form-label text-sm" for="id_subcuenta1">Cuenta</label><br>
                                                <?php
                                                $nombreSubcuentaSeleccionada = "";
                                                foreach ($subcuentasActivoCorriente_datos as $subcuentas_dato) {
                                                    if ($transaccion1 && $subcuentas_dato['id_subCuenta'] == $transaccion1['id_subCuenta']) {
                                                        $nombreSubcuentaSeleccionada = $subcuentas_dato['id_subCuenta'] . ' - ' . $subcuentas_dato['name_subCuenta'];
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <span class="form-control form-control-sm"><?php echo htmlspecialchars($nombreSubcuentaSeleccionada, ENT_QUOTES, 'UTF-8'); ?></span>
                                                <small style="color: red; display:none;" id="lbl_subcuentas1">*Este campo es requerido</small>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label text-sm" for="id_subcuenta2">Venta</label><br>
                                                <?php
                                                $nombreSubcuentaSeleccionada = "";
                                                foreach ($subcuentasVentas_datos as $subcuentas_dato) {
                                                    if ($transaccion2 && $subcuentas_dato['id_subCuenta'] == $transaccion2['id_subCuenta']) {
                                                        $nombreSubcuentaSeleccionada = $subcuentas_dato['id_subCuenta'] . ' - ' . $subcuentas_dato['name_subCuenta'];
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <span class="form-control form-control-sm"><?php echo htmlspecialchars($nombreSubcuentaSeleccionada, ENT_QUOTES, 'UTF-8'); ?></span>
                                                <small style="color: red; display:none;" id="lbl_subcuentas2">*Este campo es requerido</small>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-sm">DESCRIPCION</label>
                                            <input type="text" id="descripcion" name="descripcion" class="form-control form-control-sm" placeholder="Poner una descripción" required value="<?php echo $descripcionC ?>" disabled>
                                        </div>
                                        <div class="form-group" hidden>
                                            <input type="text" id="id_usuario" name="id_usuario" class="form-control" value="<?php echo $id_usuario; ?>">
                                        </div>
                                        <hr>
                                        <div class="table-responsive">
                                            <table id="invoice_item_table" class="table table-bordered table-striped table-sm" style="font-size: 0.85rem; vertical-align: middle;">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 2%;"><i class="fa fa-list-ol" aria-hidden="true"></i></th>
                                                        <th style="width: 5%;">Tipo</th>
                                                        <th style="width: 12%;">Descripcion</th>
                                                        <th style="width: 3%;">NroCajas</th>
                                                        <th style="width: 5%;text-align: center;">Peso/Bruto</th>
                                                        <th style="width: 5%;text-align: center;">Peso/Neto</th>
                                                        <th style="width: 5%;">Bs</th>
                                                        <th style="width: 5%;">SubTotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $contador = 0;
                                                    foreach ($detalletransacciones_datos as $detalletransacciones_dato) {
                                                        //$id_detalletransacciones = $detalletransacciones_dato['id_detalletransacciones'];
                                                        $contador++;
                                                    ?>
                                                        <tr id="row_id_<?php echo $contador ?>">
                                                            <td><?php echo $contador; ?></td>
                                                            <?php
                                                            $nombreTipoProductoSeleccionado = "";
                                                            foreach ($tipoproductos_datos as $tipoproductos_dato) {
                                                                if ($tipoproductos_dato['id_tipoProducto'] == $detalletransacciones_dato['id_tipoProducto']) {
                                                                    $nombreTipoProductoSeleccionado = $tipoproductos_dato['name_tipoProducto'];
                                                                    break; // Encuentra la coincidencia y sale del bucle.
                                                                }
                                                            }
                                                            ?>
                                                            <td><?php echo htmlspecialchars($nombreTipoProductoSeleccionado, ENT_QUOTES, 'UTF-8'); ?></td>
                                                            <td><?php echo htmlspecialchars($detalletransacciones_dato['descripcion'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                            <td style="text-align: right;"><?php echo $detalletransacciones_dato['cantidadCajas']; ?></td>
                                                            <td style="text-align: right;"><?php echo $detalletransacciones_dato['pesoB_kg']; ?></td>
                                                            <td style="text-align: right;"><?php echo $detalletransacciones_dato['pesoN_kg']; ?></td>
                                                            <td style="text-align: right;"><?php echo $detalletransacciones_dato['precio']; ?></td>
                                                            <td style="text-align: right;"><?php echo $detalletransacciones_dato['subTotal']; ?></td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr id="row_id_final">
                                                        <td colspan="7" style="text-align: right;"><strong>TOTAL Bs:</strong></td>
                                                        <td colspan="7" style="text-align: right;"><strong id="total"><?php echo number_format($total, 2, '.', ','); ?></strong></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <a href="<?php echo $URL; ?>/cobranzas/kardex1.php?id=<?php echo $id_persona; ?>" class="btn btn-secondary btn-sm">Regresar</a>
                                        </div>
                                    </form>
                                    <!-- /.CODIGO PARA AGREGAR NUEVA FILA -->
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

<?php
include('../layout/parte22.php');
include('../layout/mensajes.php');
?>