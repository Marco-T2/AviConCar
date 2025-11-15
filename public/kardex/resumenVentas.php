<?php
// C:\web\stack\mp\public\kardex\resumenVentas.php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

// Defaults de fechas (últimos 14 días)
$hoy            = new DateTime('today');
$fecha          = $hoy->format('Y-m-d');
$fechaQuincenal = (clone $hoy)->modify('-14 days')->format('Y-m-d');

// Fechas desde GET o defaults
$fecha_inicio = (isset($_GET['fecha_inicio']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha_inicio'])) ? $_GET['fecha_inicio'] : $fechaQuincenal;
$fecha_fin    = (isset($_GET['fecha_fin'])    && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha_fin']))     ? $_GET['fecha_fin']     : $fecha;

// Normaliza si vienen invertidas
if ($fecha_inicio > $fecha_fin) { $tmp = $fecha_inicio; $fecha_inicio = $fecha_fin; $fecha_fin = $tmp; }

// Alimenta a los controladores que leen GET
$_GET['fecha_inicio'] = $fecha_inicio;
$_GET['fecha_fin']    = $fecha_fin;

// Catálogo de periodos para el combo
include('../app/controllers/gestion/listarperiodo.php');
// Datos iniciales del resumen (rango por defecto)
include('../app/controllers/informes/resumenVentas.php');

// Helper: rango amigable sin strftime (PHP 8.1+)
function renderRangoLindo($fi, $ff) {
  $d1 = DateTime::createFromFormat('Y-m-d', $fi);
  $d2 = DateTime::createFromFormat('Y-m-d', $ff);
  if (!$d1 || !$d2) return 'Fechas no definidas';

  if (class_exists('IntlDateFormatter')) {
    $fmt1 = new IntlDateFormatter('es_BO', IntlDateFormatter::NONE, IntlDateFormatter::NONE); $fmt1->setPattern('d MMMM');
    $fmt2 = new IntlDateFormatter('es_BO', IntlDateFormatter::NONE, IntlDateFormatter::NONE); $fmt2->setPattern('d MMMM, y');
    return $fmt1->format($d1) . ' - ' . $fmt2->format($d2);
  } else {
    $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $txt1 = (int)$d1->format('d').' '.$meses[(int)$d1->format('n')-1];
    $txt2 = (int)$d2->format('d').' '.$meses[(int)$d2->format('n')-1].', '.$d2->format('Y');
    return $txt1.' - '.$txt2;
  }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row"><div class="col-md-12">
        <div class="card card-primary collapsed-card">

          <div class="container mt-3">
            <div class="row g-2">

              <!-- Periodo + fechas -->
              <div class="col-12 col-md-6 d-flex flex-wrap align-items-center">
                <div class="me-2 mb-2 mb-md-0">
                  <label class="form-label text-sm">Reporte:</label>
                  <select id="id_periodo" class="form-control form-control-sm btn-primary" name="id_periodo">
                    <option value="" disabled selected>Seleccionar</option>
                    <?php foreach ($periodos_datos as $periodo): ?>
                      <option value="<?php echo (int)$periodo['id_periodo']; ?>">
                        <?php echo htmlspecialchars($periodo['name_periodo'], ENT_QUOTES, 'UTF-8'); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <span class="text-sm align-self-center mx-2"></span>

                <div class="me-2 mb-2 mb-md-0">
                  <label for="fecha_inicio" class="form-label text-sm">Fecha Inicio:</label>
                  <input type="date" class="form-control form-control-sm" id="fecha_inicio" name="fecha_inicio"
                         value="<?php echo htmlspecialchars($fecha_inicio, ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="mb-2 mb-md-0">
                  <label for="fecha_fin" class="form-label text-sm">Fecha Fin:</label>
                  <input type="date" class="form-control form-control-sm" id="fecha_fin" name="fecha_fin"
                         value="<?php echo htmlspecialchars($fecha_fin, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
              </div>

              <!-- Botones -->
              <div class="col-12 col-md-6 d-flex justify-content-md-end align-items-center flex-wrap">
                <button type="button" id="aplicarFiltros" class="btn btn-success btn-sm me-2 mb-2 mb-md-0">Aplicar</button>
                <span class="text-sm align-self-center mx-2"></span>
                <button type="button" id="imprimir" class="btn btn-warning btn-sm me-2 mb-2 mb-md-0">
                  <i class="fa fa-print fa-sm"></i> Imprimir
                </button>
                <span class="text-sm align-self-center mx-2"></span>
                <button type="button" id="exportar" class="btn btn-info btn-sm mb-2 mb-md-0">
                  <i class="fa fa-file-excel"></i> Exportar
                </button>
                <span class="text-sm align-self-center mx-2"></span>
                <a href="<?php echo $URL; ?>/kardex" class="btn btn-secondary btn-sm mb-2 mb-md-0">Regresar</a>
              </div>
            </div>
          </div>

          <script>
          $(function(){
            // Cambios rápidos por período
            $('#id_periodo').on('change', function(){
              const val = String($(this).val()||'');
              const today = new Date();
              function ymd(d){ return d.toISOString().slice(0,10); }
              function startOfWeek(d){ const nd=new Date(d); const day=nd.getDay(); const diff=nd.getDate()-day+(day===0? -6:1); nd.setDate(diff); return nd; }
              function endOfWeek(d){ const s=startOfWeek(d); const e=new Date(s); e.setDate(s.getDate()+6); return e; }
              function startOfMonth(d){ return new Date(d.getFullYear(), d.getMonth(), 1); }
              function endOfMonth(d){ return new Date(d.getFullYear(), d.getMonth()+1, 0); }
              function startOfYear(d){ return new Date(d.getFullYear(), 0, 1); }
              function endOfYear(d){ return new Date(d.getFullYear(), 11, 31); }

              let fi = $('#fecha_inicio').val(), ff = $('#fecha_fin').val();
              switch (val) {
                case '1': fi=ymd(today); ff=ymd(today); break;                             // Hoy
                case '2': fi=ymd(startOfWeek(today)); ff=ymd(endOfWeek(today)); break;     // Esta semana
                case '3': fi=ymd(startOfWeek(today)); ff=ymd(today); break;                // En lo que va de semana
                case '4': fi=ymd(startOfMonth(today)); ff=ymd(endOfMonth(today)); break;   // Este mes
                case '5': fi=ymd(startOfMonth(today)); ff=ymd(today); break;               // En lo que va de mes
                case '6': fi=ymd(startOfYear(today)); ff=ymd(endOfYear(today)); break;     // Este año
                case '7': fi=ymd(startOfYear(today)); ff=ymd(today); break;                // En lo que va de año
              }
              $('#fecha_inicio').val(fi);
              $('#fecha_fin').val(ff);
            });

            // --- Función global robusta para recalcular totales (post-AJAX/DataTables) ---
            window.actualizarTotal = function actualizarTotal() {
              let totC = 0, totPB = 0, totPN = 0, totM = 0;

              $("#example1 tbody tr").each(function(){
                const $tr = $(this);
                // Evita fila vacía de DataTables si existiese
                if ($tr.hasClass('odd') && $tr.find('td.dataTables_empty').length) return;

                function getNum(idx){
                  let txt = $tr.find("td:eq("+idx+")").text() || '';
                  txt = txt.replace(/\u00A0/g, ' ').trim(); // NBSP
                  txt = txt.replace(/,/g, '');              // separador de miles ES
                  const n = parseFloat(txt);
                  return isNaN(n) ? 0 : n;
                }

                totC  += getNum(4); // Cajas
                totPB += getNum(5); // Peso B (kg)
                totPN += getNum(6); // Peso N (kg)
                totM  += getNum(7); // Monto (Bs)
              });

              $('#totalCajas').text(totC.toFixed(2));
              $('#totalPesoB').text(totPB.toFixed(2));
              $('#totalPesoN').text(totPN.toFixed(2));
              $('#totalMonto').text(totM.toFixed(2));
            };

            // Aplicar (AJAX)
            $('#aplicarFiltros').on('click', function(){
              const fi = $('#fecha_inicio').val();
              const ff = $('#fecha_fin').val();

              // span de rango
              $('#dateRangeSpan').text((function(fi,ff){
                const meses=['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
                const d1 = new Date(fi+'T00:00:00'), d2 = new Date(ff+'T00:00:00');
                const left = `${d1.getDate()} ${meses[d1.getMonth()]}`;
                const right= `${d2.getDate()} ${meses[d2.getMonth()]}, ${d2.getFullYear()}`;
                return `${left} - ${right}`;
              })(fi, ff));

              $.ajax({
                url: '../app/controllers/informes/resumenVentasfiltro.php',
                type: 'POST',
                data: { fecha_inicio: fi, fecha_fin: ff },
                success: function(html){
                  $('#example1 tbody').html(html);
                  window.actualizarTotal();
                },
                error: function(xhr, status, err){
                  console.error('AJAX error:', status, err);
                  Swal.fire({icon:'error', title:'Error', text:'No se pudo cargar el resumen.'});
                }
              });
            });

            const baseURL = '<?php echo $URL; ?>';
            $('#imprimir').on('click', function(){
              const fi = $('#fecha_inicio').val(), ff = $('#fecha_fin').val();
              window.open(baseURL + '/kardex/kardexprint.php?fecha_inicio='+fi+'&fecha_fin='+ff, '_blank');
            });
            $('#exportar').on('click', function(){
              const fi = $('#fecha_inicio').val(), ff = $('#fecha_fin').val();
              window.open(baseURL + '/kardex/export_resumen_ventas.php?fecha_inicio='+fi+'&fecha_fin='+ff, '_blank');
            });

            // Hook opcional si usas DataTables sobre #example1
            $('#example1').on('draw.dt', function(){ window.actualizarTotal(); });

            // Totales al cargar (datos del include inicial)
            window.actualizarTotal();
          });
          </script>

          <hr>
          <div class="d-flex flex-column justify-content-center align-items-center text-center" style="gap:.10rem;">
            <h4 style="font-size:.9rem;margin-bottom:0;"><strong>RESUMEN DE VENTAS</strong></h4>
            <span id="dateRangeSpan" style="font-size:.85rem;"><?php echo renderRangoLindo($fecha_inicio, $fecha_fin); ?></span>
          </div>

          <div class="card-body" style="display:block;">
            <div class="row">
              <div class="col-md-2"></div>
              <div class="col-md-8">
                <div class="table-responsive">
                  <table id="example1" class="table table-bordered table-striped table-sm" style="font-size:.85rem; vertical-align:middle;">
                    <thead>
                      <tr>
                        <th style="width:3%;  text-align:center;">Fecha</th>
                        <th style="width:10%; text-align:center;">Tipo</th>
                        <th style="width:3%;  text-align:center;">Nro</th>
                        <th style="width:15%; text-align:center;">Cliente</th>
                        <th style="width:6%;  text-align:right;">Cajas</th>
                        <th style="width:6%;  text-align:right;">Peso B (kg)</th>
                        <th style="width:6%;  text-align:right;">Peso N (kg)</th>
                        <th style="width:6%;  text-align:right;">Monto (Bs)</th>
                        <th style="width:5%;  text-align:center;"><i class="fa fa-filter"></i></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      // ===== Totales del lado servidor (inicial) =====
                      $totC = 0.0; $totPB = 0.0; $totPN = 0.0; $totM = 0.0;
                      ?>
                      <?php if (!empty($transacciones_datos)): ?>
                        <?php foreach ($transacciones_datos as $r): ?>
                          <?php
                            $cajas = (float)($r['cantidadCajas'] ?? 0);
                            $pb    = (float)($r['pesoB_kg'] ?? 0);
                            $pn    = (float)($r['pesoN_kg'] ?? 0);
                            $monto = (float)($r['monto_bs'] ?? 0);

                            $totC += $cajas;
                            $totPB+= $pb;
                            $totPN+= $pn;
                            $totM += $monto;
                          ?>
                          <tr>
                            <td style="text-align:center;"><?php echo htmlspecialchars(date('d/m/Y', strtotime($r['fecha_comprobante']))); ?></td>
                            <td style="text-align:center;"><?php echo htmlspecialchars($r['name_tipocomprobante'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td style="text-align:center;"><?php echo (int)($r['num_comprobante'] ?? 0); ?></td>
                            <td><?php echo htmlspecialchars($r['name_persona'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>

                            <td style="text-align:right;"><?php echo number_format($cajas, 2, '.', ','); ?></td>
                            <td style="text-align:right;"><?php echo number_format($pb,    2, '.', ','); ?></td>
                            <td style="text-align:right;"><?php echo number_format($pn,    2, '.', ','); ?></td>
                            <td style="text-align:right;"><?php echo number_format($monto, 2, '.', ','); ?></td>
                            <td>
                            <center>
                                <div class="btn-group">
                                <a href="<?php echo $URL; ?>/despachos/show.php?id=<?php echo (int)$r['id_comprobante']; ?>" class="btn btn-success btn-sm" title="Ver">
                                    <i class="fa fa-eye fa-sm"></i>
                                </a>
                                </div>
                            </center>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="4" style="text-align:right; font-weight:bold;">Total:</td>
                        <td id="totalCajas" style="text-align:right; font-weight:bold;"><?php echo number_format($totC, 2, '.', ','); ?></td>
                        <td id="totalPesoB" style="text-align:right; font-weight:bold;"><?php echo number_format($totPB, 2, '.', ','); ?></td>
                        <td id="totalPesoN" style="text-align:right; font-weight:bold;"><?php echo number_format($totPN, 2, '.', ','); ?></td>
                        <td id="totalMonto" style="text-align:right; font-weight:bold;"><?php echo number_format($totM, 2, '.', ','); ?></td>
                        <td></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="col-md-2"></div>
            </div>
          </div>

        </div>
      </div></div>
    </div><!-- /.container-fluid -->
  </div>
</div>
<!-- /.content-wrapper -->

<?php
include('../layout/parte2.php');
include('../layout/mensajes.php');
?>
