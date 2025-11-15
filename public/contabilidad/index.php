<?php
// C:\web\stack\mp\public\contabilidad\index.php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

// Solo necesitas el plan de cuentas (el loader ya arma todo en memoria)
include('../app/controllers/contabilidad/plandecuentas.php');

$currentFile = basename($_SERVER['PHP_SELF']);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <?php include('../layout/plancontable.php'); ?>
  <div style="margin-bottom: 5px;"></div>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row"><div class="col-md-12">
        <div class="card card-primary collapsed-card">
          <div style="margin-bottom:10px;"></div>

          <div class="text-center" style="gap:0.10rem;">
            <h4 style="font-size:.9rem;margin-bottom:0;"><strong>AVÍCOLA EL CARMEN</strong></h4>
            <span class="text-sm" style="font-size:.85rem;margin-bottom:0;">Plan contable</span>
          </div>

          <style>
            .grupo:hover, .subgrupo:hover, .cuenta:hover, .subcuenta:hover { background-color:#f0f0f0; }
            .d-flex.justify-content-between > span:last-child { min-width: 90px; text-align: right; }
            .ml-gap { margin-right: 100px; }
            .ml-gap-lg { margin-right: 200px; }
            .ml-gap-xl { margin-right: 300px; }
          </style>

          <div class="container-fluid">
            <ul id="listaGrupos" class="list-unstyled">

              <?php foreach (obtenerGrupos() as $grupo): ?>
                <li class="mb-1">
                  <a class="grupo d-flex justify-content-between py-1 px-2" data-toggle="collapse"
                     href="#subgrupo-<?php echo (int)$grupo['id_grupo']; ?>" role="button" aria-expanded="false">
                    <span><?php echo htmlspecialchars($grupo['path'].' '.$grupo['name_grupo'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span><?php echo number_format((float)($grupo['total'] ?? 0), 2, '.', ','); ?></span>
                  </a>

                  <div id="subgrupo-<?php echo (int)$grupo['id_grupo']; ?>" class="collapse pl-3">
                    <ul class="list-unstyled">

                      <?php foreach (obtenerSubgruposPorGrupo($grupo['id_grupo']) as $subgrupo): ?>
                        <li class="mb-1">
                          <a class="subgrupo d-flex justify-content-between py-1 px-2" data-toggle="collapse"
                             href="#cuenta-<?php echo (int)$subgrupo['id_subgrupo']; ?>" role="button" aria-expanded="false">
                            <span class="mr-auto">
                              <?php echo htmlspecialchars($subgrupo['path'].' '.$subgrupo['name_subgrupo'], ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <span class="ml-gap">
                              <?php echo number_format((float)($subgrupo['total'] ?? 0), 2, '.', ','); ?>
                            </span>
                          </a>

                          <div id="cuenta-<?php echo (int)$subgrupo['id_subgrupo']; ?>" class="collapse pl-3">
                            <ul class="list-unstyled">

                              <?php foreach (obtenerCuentasPorSubgrupo($subgrupo['id_subgrupo']) as $cuenta): ?>
                                <li class="mb-1">
                                  <a class="cuenta d-flex justify-content-between py-1 px-2" data-toggle="collapse"
                                     href="#subcuenta-<?php echo (int)$cuenta['id_cuenta']; ?>" role="button" aria-expanded="false">
                                    <span class="mr-auto">
                                      <?php echo htmlspecialchars($cuenta['path'].' '.$cuenta['name_cuenta'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                    <span class="ml-gap-lg">
                                      <?php echo number_format((float)($cuenta['total'] ?? 0), 2, '.', ','); ?>
                                    </span>
                                  </a>

                                  <div id="subcuenta-<?php echo (int)$cuenta['id_cuenta']; ?>" class="collapse pl-3">
                                    <ul class="list-unstyled">

                                      <?php foreach (obtenerSubcuentasPorCuenta($cuenta['id_cuenta']) as $subcuenta): ?>
                                        <li class="mb-1">
                                          <span class="subcuenta d-flex justify-content-between py-1 px-2">
                                            <span class="mr-auto">
                                              <?php echo htmlspecialchars($subcuenta['path'].' '.$subcuenta['name_subCuenta'], ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                            <a class="ml-gap-xl" href="mayorsubcuenta.php?id_subcuenta=<?php echo (int)$subcuenta['id_subCuenta']; ?>">
                                              <?php echo number_format((float)($subcuenta['total'] ?? 0), 2, '.', ','); ?>
                                            </a>
                                          </span>
                                        </li>
                                      <?php endforeach; ?>

                                    </ul>
                                  </div>
                                </li>
                              <?php endforeach; ?>

                            </ul>
                          </div>
                        </li>
                      <?php endforeach; ?>

                    </ul>
                  </div>
                </li>
              <?php endforeach; ?>

            </ul>
          </div>

        </div>
      </div></div>
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
include('../layout/parte2.php');
include('../layout/mensajes.php');
?>
