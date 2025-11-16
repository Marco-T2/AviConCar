<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/informes/listarInformes.php');
include('../app/controllers/usuarios/listado_usuarios.php');
include('../app/controllers/informes/saldoclientestipo.php');
include('../app/controllers/tipopersonas/listado_tipopersonas.php');

$tipopersonas_map = array_column($tipopersonas_datos, 'name_tipoPersona', 'id_tipoPersona');


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
                        <a href="../contabilidad/mayorsubcuenta.php?id_subcuenta=1" class="ml-3" style="font-weight: bold;">
                            <?php echo htmlspecialchars(number_format($sumaCajaGeneral_datos['saldo_total'], 2, '.', ',')); ?>
                        </a>
                    </div>
                </div>

            </div>



            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">BANCO BETO</span>
                        <?php
                        // Obtener saldo específico para la subcuenta id_subcuenta = 75
                        try {
                            $stmtBanco = $pdo->prepare(
                                "SELECT COALESCE(SUM(COALESCE(t.debe,0) - COALESCE(t.haber,0)),0) AS saldo_total
                                 FROM tb_transacciones t
                                 LEFT JOIN tb_comprobantes c ON c.id_comprobante = t.id_comprobante
                                 WHERE t.id_subcuenta = :id_subcuenta AND c.id_gestion = :g"
                            );
                            $stmtBanco->execute([':id_subcuenta' => 75, ':g' => (int)GESTION_ACTIVA]);
                            $sumaBancoBeto = $stmtBanco->fetch(PDO::FETCH_ASSOC);
                            $saldoBancoBeto = (float)($sumaBancoBeto['saldo_total'] ?? 0);
                        } catch (Exception $e) {
                            $saldoBancoBeto = 0.00;
                        }
                        ?>
                        <a href="../contabilidad/mayorsubcuenta.php?id_subcuenta=75" class="ml-3" style="font-weight: bold; color: #663f00;">
                            <?php echo htmlspecialchars(number_format($saldoBancoBeto, 2, '.', ',')); ?>
                        </a>
                    </div>
                </div>

            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="far fa-flag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">RESUMEN DE VENTAS</span>
                        <?php
                        $fecha_hoy = date('Y-m-d');  // Genera la fecha de hoy en formato 'año-mes-día'
                        ?>
                        <a href="../kardex/resumenVentas.php?fecha_inicio=<?php echo $fecha_hoy; ?>&fecha_fin=<?php echo $fecha_hoy; ?>" class="ml-3" style="font-weight: bold;">Ver resumen</a>
                    </div>


                </div>

            </div>


            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">OTROS</span>
                        <span class="info-box-number">No disponible</span>
                    </div>

                </div>

            </div>

        </div>
        <div class="container-fluid">

            <?php
            $tipos = [1,2,3,4]; // ← edita aquí los tipos que quieres mostrar
            ?>

            <div class="row">
            <?php foreach ($tipos as $tipo_en_card): 
            $totalTipo  = (float)($saldos_por_tipo[$tipo_en_card] ?? 0);
            $cardClass  = $totalTipo < 0 ? 'card-danger' : 'card-primary';
            $textClass  = $totalTipo < 0 ? 'text-danger' : '';
            $listarSaldoClientes_datos = listarSaldoClientesPorTipo($pdo, GESTION_ACTIVA, $tipo_en_card);
            ?>
            <div class="col-md-6">
                <div class="card <?= $cardClass ?> collapsed-card">
                <div class="card-header">
                    <h3 class="card-title card-title-sm" style="display:flex;justify-content:space-between;font-size:.85rem;width:100%;">
                    <span><?= htmlspecialchars($tipopersonas_map[$tipo_en_card] ?? 'Tipo') ?></span>
                    <span class="<?= $textClass ?>">Bs.<?= number_format($totalTipo, 2) ?></span>
                    </h3>
                </div>

                <div class="card-body" style="display:block;">
                    <?php if ($totalTipo < 0): ?>
                    <div class="alert alert-danger py-1 mb-2" style="font-size:.8rem;">
                        <i class="fa fa-exclamation-triangle"></i>
                        Saldo total negativo para este grupo. <strong>Necesita revisión</strong>.
                    </div>
                    <?php endif; ?>

                    <table id="tabla-tipo-<?= (int)$tipo_en_card ?>" class="table table-bordered table-striped table-sm" style="font-size:.85rem;vertical-align:middle;">
                    <thead>
                        <tr>
                        <th style="width:2%;"><i class="fa fa-list-ol"></i></th>
                        <th>Nombre cliente</th>
                        <th>Fecha</th>
                        <th>Saldo (Bs)</th>
                        <th style="width:2%;text-align:center;"><i class="fa fa-filter"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $contador = 0;
                        foreach ($listarSaldoClientes_datos as $fila):
                        $contador++;
                        $id_persona = (int)$fila['id_persona'];
                        $nombre     = htmlspecialchars($fila['name_persona'] ?? '');
                        $fechaRaw   = $fila['fecha_comprobante'] ?? null;
                        $fechaTxt   = $fechaRaw ? date('d/m/Y', strtotime($fechaRaw)) : '—';
                        $saldo      = (float)($fila['saldo'] ?? 0);
                        $neg        = $saldo < 0;
                        ?>
                        <tr>
                        <td style="vertical-align:middle;"><?= $contador ?></td>
                        <td style="vertical-align:middle;"><?= $nombre ?></td>
                        <td style="vertical-align:middle;"><?= $fechaTxt ?></td>
                        <td style="vertical-align:middle; text-align:right;" class="<?= $neg ? 'text-danger font-weight-bold' : '' ?>">
                            <?= number_format($saldo, 2) ?>
                            <?php if ($neg): ?>
                            <span class="badge badge-danger ml-2">Revisión</span>
                            <?php endif; ?>
                        </td>
                        <td style="vertical-align:middle;">
                            <center>
                            <div class="btn-group">
                                <a href="<?= $URL ?>/kardex/kardex.php?id=<?= $id_persona ?>" class="btn btn-primary btn-sm" title="Ver kardex">
                                <i class="fa fa-eye fa-sm"></i>
                                </a>
                            </div>
                            </center>
                        </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if (empty($listarSaldoClientes_datos)): ?>
                        <tr>
                        <td colspan="5" class="text-center text-muted">Sin datos para mostrar.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    </table>
                </div>
                </div>
            </div>
            <?php endforeach; ?>
            </div>



        </div><!-- /.container-fluid -->
    </div>
</div>
<!-- /.content-wrapper -->

<?php
include('../layout/parte2.php');
include('../layout/mensajes.php')
?>