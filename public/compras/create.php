<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/tipocomprobantes/listado_tipocomprobantes.php');
include('../app/controllers/subCuentas/listado_subCuentas.php');
include('../app/controllers/personas/listado_personas.php');
include('../app/controllers/tipoproducto/listado_tipoproducto.php');


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
                            <h3 class="card-title">PRO-FORMA COMPRA DE POLLO VIVO</h3>
                        </div>
                        <div class="card-body" style="display: block;">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- REGISTRAR EL METODO AL CONTROLADOR -->
                                    <form action="#" method="post">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="">Tipo despacho</label>
                                                <select id="id_tipocomprobante" class="form-control" name="id_tipocomprobante">
                                                    <option value="" disabled selected>Seleccionar</option>
                                                    <?php
                                                    foreach ($tipocomprobantes_datos as $tipocomprobantes_dato) {
                                                    ?>
                                                        <option value="<?php echo $tipocomprobantes_dato['id_tipocomprobante']; ?>"><?php echo $tipocomprobantes_dato['name_tipocomprobante']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="">Nro despacho</label>
                                                <input type="number" id="num_comprobante" name="num_comprobante" class="form-control" placeholder="#" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="">Fecha</label>
                                                <input type="date" id="fecha_comprobante" name="fecha_comprobante" class="form-control" value="<?php echo $fecha; ?>" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="">Hora</label>
                                                <input type="time" name="hora_comprobante" id="hora_comprobante" class="form-control" placeholder="" value="<?php echo $hora; ?>" required>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="">Granjero</label><br>
                                                <select style="width: 100%; font-size: 12px;" class="js-select2 form-control" id="id_persona" name="id_persona">
                                                    <option value="" disabled selected>Seleccionar</option>
                                                    <?php foreach ($personas_datos as $personas_dato) : ?>
                                                        <option value="<?php echo $personas_dato['id_persona']; ?>">
                                                            <?php echo $personas_dato['id_persona'] . ' - ' . $personas_dato['name_persona']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <small style="color: red; display:none;" id="lbl_persona"> *Este campo es requerido</small>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="">Cuenta de Pago</label><br>
                                                <select style="width: 100%; font-size: 12px;" class="js-select2 form-control" id="id_subcuenta1" name="id_subcuentas">
                                                    <option value="" disabled selected>Seleccionar</option>
                                                    <?php foreach ($subcuentas_datos as $subcuentas_dato) : ?>
                                                        <option value="<?php echo $subcuentas_dato['id_subCuenta']; ?>">
                                                            <?php echo $subcuentas_dato['id_subCuenta'] . ' - ' . $subcuentas_dato['name_subCuenta']; ?>
                                                        </option><?php endforeach; ?>
                                                </select>
                                                <small style="color: red; display:none;" id="lbl_subcuentas1"> *Este campo es requerido</small>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="">Cuenta de Compra</label><br>
                                                <select style="width: 100%; font-size: 12px;" class="js-select2 form-control" id="id_subcuenta2" name="id_subcuentas">
                                                    <option value="" disabled selected>Seleccionar</option>
                                                    <?php foreach ($subcuentas_datos as $subcuentas_dato) : ?>
                                                        <option value="<?php echo $subcuentas_dato['id_subCuenta']; ?>">
                                                            <?php echo $subcuentas_dato['id_subCuenta'] . ' - ' . $subcuentas_dato['name_subCuenta']; ?>
                                                        </option><?php endforeach; ?>
                                                </select>
                                                <small style="color: red; display:none;" id="lbl_subcuentas2"> *Este campo es requerido</small>
                                            </div>
                                        </div>
                                        <script>
                                            $(document).ready(function() {
                                                $('.js-select2').select2({
                                                    placeholder: 'Seleccionar',
                                                    theme: 'Bootstrap',
                                                    width: '280px',
                                                });
                                            });
                                        </script>
                                        <br>
                                        <div class="form-group">
                                            <label for="">Descripcion</label>
                                            <input type="text" id="descripcion" name="descripcion" class="form-control" placeholder="Poner una descripcion" required>
                                        </div>
                                        <div class="form-group" hidden>
                                            <input type="text" id="id_usuario" name="id_usuario" class="form-control" value="<?php echo $id_usuario; ?>">
                                        </div>
                                        <hr>
                                    </form>

                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="card" style="margin: 0; padding: 0; border: none;">
                                                <div class="card-header" style="text-align: center; padding: 0;">
                                                    <h3 style="font-weight: bold; font-size: 15px; margin-top: 0; margin-bottom: 0; padding: 5px 0;">DETALLE</h3>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-bordered table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 150px">CONCEPTO</th>
                                                                <th style="width: 20px">CANTIDAD</th>
                                                                <th style="width: 20px">PESO</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Pollo recibido</td>
                                                                <td>4320</td>
                                                                <td>10921</td>
                                                            </tr>
                                                            <tr>
                                                                <td>(-) Tiki</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                            </tr>
                                                            <tr>
                                                                <td>(-) Buche</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="col-md-7">
                                            <div class="card" style="margin: 0; padding: 0; border: none;">
                                                <div class="card-header" style="text-align: center; padding: 0;">
                                                    <h3 style="font-weight: bold; font-size: 15px; margin-top: 0; margin-bottom: 0; padding: 5px 0;">DESCUENTO</h3>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-bordered table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 40px">CONCEPTO</th>
                                                                <th style="width: 40px">CANTIDAD</th>
                                                                <th style="width: 40px">PESO</th>
                                                                <th style="width: 40px">PREC_UNID</th>
                                                                <th style="width: 40px">MONTO</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Desc. Rojo</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Segundas</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Bebe</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Bebe/dsct</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Segundas Fea</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Faeneo</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Transporte</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Alq. Jaulas</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Total descuentos</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Líquido pagable</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>



                                        </div>
                                    </div>
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