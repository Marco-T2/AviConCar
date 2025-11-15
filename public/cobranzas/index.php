<?php
include('../app/config.php');
//include('../layout/sesion.php');
include('../layout/parte11.php');
include('../app/controllers/informes/listarInformes.php');
include('../app/controllers/usuarios/listado_usuarios.php');
include('../app/controllers/informes/saldoclientestipo.php')


?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- /.content -->
    <div class="content">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">CAJA GENERAL</span>
                        <a href="../cobranzas/mayorsubcuenta.php?id_subcuenta=1" class="ml-3" style="font-weight: bold;">
                            <?php echo htmlspecialchars(number_format($sumaCajaGeneral_datos['saldo_total'], 2, '.', ',')); ?>
                        </a>
                    </div>
                </div>

            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title card-title-sm" style="display: flex; justify-content: space-between; font-size: 0.85rem; width: 100%;">
                                <span>CLIENTES CIUDAD</span>
                                <span>Bs.<?php echo number_format($saldos_por_tipo[1], 2); ?></span>
                            </h3>
                        </div>
                        <div class="card-body" style="display: block;">
                            <table id="example1" class="table table-bordered table-striped table-sm" style="font-size: 0.85rem; vertical-align: middle;">
                                <thead>
                                    <tr>
                                        <th style="width: 2%;"><i class="fa fa-list-ol" aria-hidden="true"></i></th>
                                        <th>Nombre cliente</th>
                                        <th>Fecha</th>
                                        <th>Cuenta Bs</th>
                                        <th style="width: 2%;">
                                            <center><i class="fa fa-filter" aria-hidden="true"></i></center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0;
                                    foreach ($listarSaldoClientes_datos as $listarSaldoClientes_dato) {
                                        $id_persona = $listarSaldoClientes_dato['id_persona'];
                                    ?>
                                        <tr>
                                            <td style="vertical-align: middle;"><?php echo $contador = $contador + 1; ?></td>
                                            <td style="vertical-align: middle;"><?php echo $listarSaldoClientes_dato['name_persona']; ?></td>
                                            <td style="vertical-align: middle;"><?php echo date('d/m/Y', strtotime($listarSaldoClientes_dato['fecha_comprobante'])); ?></td>
                                            <td style="vertical-align: middle; text-align: right;"><?php echo number_format($listarSaldoClientes_dato['saldo'], 2); ?></td>
                                            <td style="vertical-align: middle;">
                                                <center>
                                                    <div class="btn-group ">
                                                        <a href="<?php echo $URL; ?>/cobranzas/kardex1.php?id=<?php echo $id_persona; ?>" type="button" class="btn btn-primary btn-sm"><i class="fa fa-eye fa-sm"></i></a>
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
                <div class="col-md-6">
                    <div class="card card-primary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title card-title-sm" style="display: flex; justify-content: space-between; font-size: 0.85rem; width: 100%;">
                                <span>CLIENTES EL ALTO</span>
                                <span>Bs.<?php echo number_format($saldos_por_tipo[2], 2); ?></span>
                            </h3>
                        </div>
                        <div class="card-body" style="display: block;">
                            <table id="example1" class="table table-bordered table-striped table-sm" style="font-size: 0.85rem; vertical-align: middle;">
                                <thead>
                                    <tr>
                                        <th style="width: 2%;"><i class="fa fa-list-ol" aria-hidden="true"></i></th>
                                        <th>Nombre cliente</th>
                                        <th>Fecha</th>
                                        <th>Cuenta Bs</th>
                                        <th style="width: 2%;">
                                            <center><i class="fa fa-filter" aria-hidden="true"></i></center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0;
                                    foreach ($listarSaldoClientesElAlto_datos as $listarSaldoClientesElAlto_dato) {
                                        $id_persona = $listarSaldoClientesElAlto_dato['id_persona'];
                                    ?>
                                        <tr>
                                            <td style="vertical-align: middle;"><?php echo $contador = $contador + 1; ?></td>
                                            <td style="vertical-align: middle;"><?php echo $listarSaldoClientesElAlto_dato['name_persona']; ?></td>
                                            <td style="vertical-align: middle;"><?php echo date('d/m/Y', strtotime($listarSaldoClientesElAlto_dato['fecha_comprobante'])); ?></td>
                                            <td style="vertical-align: middle; text-align: right;"><?php echo number_format($listarSaldoClientesElAlto_dato['saldo'], 2); ?></td>
                                            <td style="vertical-align: middle;">
                                                <center>
                                                    <div class="btn-group ">
                                                        <a href="<?php echo $URL; ?>/cobranzas/kardex1.php?id=<?php echo $id_persona; ?>" type="button" class="btn btn-primary btn-sm"><i class="fa fa-eye fa-sm"></i></a>
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


            <!-- OTROS CLIENTES -->
            <div class="row">
                <!--<div class="col-md-6">
                    <div class="card card-primary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title card-title-sm" style="display: flex; justify-content: space-between; font-size: 0.85rem; width: 100%;">
                                <span>CLIENTES PASIVOS</span>
                                <span>Bs.<?php echo number_format($saldos_por_tipo[3], 2); ?></span>
                            </h3>
                        </div>
                        <div class="card-body" style="display: block;">
                            <table id="example1" class="table table-bordered table-striped table-sm" style="font-size: 0.85rem; vertical-align: middle;">
                                <thead>
                                    <tr>
                                        <th style="width: 2%;"><i class="fa fa-list-ol" aria-hidden="true"></i></th>
                                        <th>Nombre cliente</th>
                                        <th>Fecha</th>
                                        <th>Cuenta Bs</th>
                                        <th style="width: 2%;">
                                            <center><i class="fa fa-filter" aria-hidden="true"></i></center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0;
                                    foreach ($listarSaldoClientesPasivos_datos as $listarSaldoClientesPasivos_dato) {
                                        $id_persona = $listarSaldoClientesPasivos_dato['id_persona'];
                                    ?>
                                        <tr>
                                            <td style="vertical-align: middle;"><?php echo $contador = $contador + 1; ?></td>
                                            <td style="vertical-align: middle;"><?php echo $listarSaldoClientesPasivos_dato['name_persona']; ?></td>
                                            <td style="vertical-align: middle;"><?php echo date('d/m/Y', strtotime($listarSaldoClientesPasivos_dato['fecha_comprobante'])); ?></td>
                                            <td style="vertical-align: middle; text-align: right;"><?php echo number_format($listarSaldoClientesPasivos_dato['saldo'], 2); ?></td>
                                            <td style="vertical-align: middle;">
                                                <center>
                                                    <div class="btn-group ">
                                                        <a href="<?php echo $URL; ?>/kardex/kardex.php?id=<?php echo $id_persona; ?>" type="button" class="btn btn-primary btn-sm"><i class="fa fa-eye fa-sm"></i></a>
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
                </div>-->
                <div class="col-md-6">
                    <div class="card card-primary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title card-title-sm" style="display: flex; justify-content: space-between; font-size: 0.85rem; width: 100%;">
                                <span>CLIENTES PERSONAL</span>
                                <span>Bs.<?php echo number_format($saldos_por_tipo[4], 2); ?></span>
                            </h3>
                        </div>
                        <div class="card-body" style="display: block;">
                            <table id="example1" class="table table-bordered table-striped table-sm" style="font-size: 0.85rem; vertical-align: middle;">
                                <thead>
                                    <tr>
                                        <th style="width: 2%;"><i class="fa fa-list-ol" aria-hidden="true"></i></th>
                                        <th>Nombre cliente</th>
                                        <th>Fecha</th>
                                        <th>Cuenta Bs</th>
                                        <th style="width: 2%;">
                                            <center><i class="fa fa-filter" aria-hidden="true"></i></center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0;
                                    foreach ($listarSaldoClientesPersonal_datos as $listarSaldoClientesPersonal_dato) {
                                        $id_persona = $listarSaldoClientesPersonal_dato['id_persona'];
                                    ?>
                                        <tr>
                                            <td style="vertical-align: middle;"><?php echo $contador = $contador + 1; ?></td>
                                            <td style="vertical-align: middle;"><?php echo $listarSaldoClientesPersonal_dato['name_persona']; ?></td>
                                            <td style="vertical-align: middle;"><?php echo date('d/m/Y', strtotime($listarSaldoClientesPersonal_dato['fecha_comprobante'])); ?></td>
                                            <td style="vertical-align: middle; text-align: right;"><?php echo number_format($listarSaldoClientesPersonal_dato['saldo'], 2); ?></td>
                                            <td style="vertical-align: middle;">
                                                <center>
                                                    <div class="btn-group ">
                                                        <a href="<?php echo $URL; ?>/cobranzas/kardex1.php?id=<?php echo $id_persona; ?>" type="button" class="btn btn-primary btn-sm"><i class="fa fa-eye fa-sm"></i></a>
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
                <!-- CUENTAS POR RECUPERAR -->
                <div class="col-md-6">
                    <div class="card card-primary collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title card-title-sm" style="display: flex; justify-content: space-between; font-size: 0.85rem; width: 100%;">
                                <span>CUENTAS POR RECUPERAR</span>
                                <span>Bs.<?php echo number_format($saldos_por_tipo[4], 2); ?></span>
                            </h3>
                        </div>
                        <div class="card-body" style="display: block;">
                            <table id="example1" class="table table-bordered table-striped table-sm" style="font-size: 0.85rem; vertical-align: middle;">
                                <thead>
                                    <tr>
                                        <th style="width: 2%;"><i class="fa fa-list-ol" aria-hidden="true"></i></th>
                                        <th>Nombre cliente</th>
                                        <th>Fecha</th>
                                        <th>Cuenta Bs</th>
                                        <th style="width: 2%;">
                                            <center><i class="fa fa-filter" aria-hidden="true"></i></center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 0;
                                    foreach ($listarSaldoClientesRecuperarSaldos_datos as $listarSaldoClientesRecuperarSaldos_dato) {
                                        $id_persona = $listarSaldoClientesRecuperarSaldos_dato['id_persona'];
                                    ?>
                                        <tr>
                                            <td style="vertical-align: middle;"><?php echo $contador = $contador + 1; ?></td>
                                            <td style="vertical-align: middle;"><?php echo $listarSaldoClientesRecuperarSaldos_dato['name_persona']; ?></td>
                                            <td style="vertical-align: middle;"><?php echo date('d/m/Y', strtotime($listarSaldoClientesRecuperarSaldos_dato['fecha_comprobante'])); ?></td>
                                            <td style="vertical-align: middle; text-align: right;"><?php echo number_format($listarSaldoClientesRecuperarSaldos_dato['saldo'], 2); ?></td>
                                            <td style="vertical-align: middle;">
                                                <center>
                                                    <div class="btn-group ">
                                                        <a href="<?php echo $URL; ?>/cobranzas/kardex1.php?id=<?php echo $id_persona; ?>" type="button" class="btn btn-primary btn-sm"><i class="fa fa-eye fa-sm"></i></a>
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
                <!-- CUENTAS POR RECUPERAR -->
            </div>
            <!-- OTROS CLIENTES -->

        </div><!-- /.container-fluid -->
    </div>
</div>
<!-- /.content-wrapper -->

<?php
include('../layout/parte22.php');
include('../layout/mensajes.php')
?>