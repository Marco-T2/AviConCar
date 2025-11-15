<?php
// C:\web\stack\ant\public\despachos\update.php  (VISTA)
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/tipocomprobantes/listado_tipocomprobantes.php');
include('../app/controllers/subCuentas/listado_subCuentas.php');
include('../app/controllers/personas/listado_personas.php');
include('../app/controllers/tipoproducto/listado_tipoproducto.php');

// Loader que expone: $detalle_rows, $id_comprobante, $id_tipocomprobante, $num_comprobante,
// $fecha_comprobante, $hora_comprobante, $id_persona, $descripcionC,
// $transacccionescuentas_datos, $totalInicial, $cxc_inicial
include('../app/controllers/despachos/update_detalleList.php');

if (!isset($fecha_comprobante) || !$fecha_comprobante) $fecha_comprobante = date('Y-m-d');
if (!isset($hora_comprobante)  || !$hora_comprobante)  $hora_comprobante  = date('H:i');

$totalInicial  = (float)($totalInicial ?? 0);
$cxc_inicial   = (float)($cxc_inicial ?? 0);
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.4/dist/select2-bootstrap4.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mathjs/9.5.0/math.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
  .select2-container .select2-dropdown{ z-index:2000; }
  .select2-selection__rendered{ font-size:12px; line-height:30px }
  .select2-selection--single{ height:30px }
  .select2-selection__arrow{ height:30px }
  .select2-results__option{ font-size:12px }
  .select2-container .select2-results__options { max-height: 220px; overflow-y: auto; }

  /* Diferenciación visual para filas de ajuste (tipo 20) */
  tr.row-ajuste td {
    background: #fff7e6 !important;   /* naranja muy suave */
    border-top: 1px solid #ffd89a !important;
    border-bottom: 1px solid #ffd89a !important;
  }
  .badge-ajuste {
    display:inline-block;
    font-size: .65rem;
    padding: .15rem .4rem;
    border-radius: .35rem;
    background: #ffcc80;
    color:#7a4b00;
    margin-left:.25rem;
    vertical-align: middle;
  }

  /* Resalte de error en filas sin tipo */
  tr.row-error td { background: #ffe6e6 !important; }
  .is-invalid + .select2 .select2-selection { border-color: #dc3545 !important; }
</style>

<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row"><div class="col-md-12">
        <div class="card card-outline card-success">
          <div class="card-header"><h3 class="card-title card-title-sm" style="font-size:.85rem">EDITAR DESPACHO</h3></div>

          <div class="card-body" style="display:block;">
            <form action="#" method="post" onsubmit="return false;">
              <input type="hidden" id="id_comprobante" value="<?php echo (int)$id_comprobante; ?>">

              <div class="row mb-2">
                <div class="col-md-3" hidden>
                  <label class="form-label text-sm">Tipo despacho</label>
                  <select id="id_tipocomprobante" class="js-select2 form-control form-control-sm" name="id_tipocomprobante" style="width:100%;">
                    <?php foreach (($tipocomprobantesVentas_datos ?? []) as $t): ?>
                      <option value="<?= (int)$t['id_tipocomprobante'] ?>" <?= ((int)$t['id_tipocomprobante']===(int)$id_tipocomprobante?'selected':'') ?>>
                        <?= htmlspecialchars($t['name_tipocomprobante'],ENT_QUOTES,'UTF-8') ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label text-sm">CLIENTE</label>
                  <select id="id_persona" name="id_persona" class="js-select2 form-control form-control-sm" style="width:100%;">
                    <option value="" disabled>Seleccionar</option>
                    <?php foreach (($personas_datos ?? []) as $p): ?>
                      <option value="<?= (int)$p['id_persona'] ?>" <?= ((int)$p['id_persona']===(int)$id_persona?'selected':'') ?>>
                        <?= (int)$p['id_persona'].' - '.htmlspecialchars($p['name_persona'],ENT_QUOTES,'UTF-8') ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-2">
                  <label class="form-label text-sm">Nro DESPACHO</label>
                  <input type="number" id="num_comprobante" name="num_comprobante" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($num_comprobante,ENT_QUOTES,'UTF-8') ?>" placeholder="#" required>
                </div>

                <div class="col-md-3">
                  <label class="form-label text-sm">FECHA</label>
                  <input type="date" id="fecha_comprobante" name="fecha_comprobante" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($fecha_comprobante,ENT_QUOTES,'UTF-8') ?>" required>
                </div>

                <div class="col-md-3">
                  <label class="form-label text-sm">HORA</label>
                  <input type="time" id="hora_comprobante" name="hora_comprobante" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($hora_comprobante,ENT_QUOTES,'UTF-8') ?>" required>
                </div>
              </div>

              <!-- Subcuentas ocultas (igual que en create) -->
              <div class="row mb-2" hidden>
                <div class="col-md-6">
                  <label class="form-label text-sm">Cuenta</label>
                  <select class="js-select2 form-control form-control-sm" id="id_subcuenta1" style="width:100%;">
                    <option value="" disabled selected>Seleccionar</option>
                    <?php
                      $t1 = $transacccionescuentas_datos[0] ?? null;
                      foreach (($subcuentasActivoCorriente_datos ?? []) as $s):
                    ?>
                      <option value="<?= (int)$s['id_subCuenta'] ?>" <?= ($t1 && (int)$s['id_subCuenta']===(int)$t1['id_subCuenta']?'selected':'') ?>>
                        <?= (int)$s['id_subCuenta'].' - '.htmlspecialchars($s['name_subCuenta'],ENT_QUOTES,'UTF-8') ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label text-sm">Venta</label>
                  <select class="js-select2 form-control form-control-sm" id="id_subcuenta2" style="width:100%;">
                    <option value="" disabled selected>Seleccionar</option>
                    <?php
                      $t2 = $transacccionescuentas_datos[1] ?? null;
                      foreach (($subcuentasVentas_datos ?? []) as $s):
                    ?>
                      <option value="<?= (int)$s['id_subCuenta'] ?>" <?= ($t2 && (int)$s['id_subCuenta']===(int)$t2['id_subCuenta']?'selected':'') ?>>
                        <?= (int)$s['id_subCuenta'].' - '.htmlspecialchars($s['name_subCuenta'],ENT_QUOTES,'UTF-8') ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <div class="d-flex justify-content-between align-items-end">
                  <div style="flex:1">
                    <label class="form-label text-sm">DETALLE DE LA VENTA</label>
                    <textarea id="descripcion" name="descripcion" class="form-control form-control-sm" rows="3"
                      placeholder="Ej: 1º Pechuga 18kg (17) | 2º Alas 56kg (16)"><?= htmlspecialchars($descripcionC,ENT_QUOTES,'UTF-8') ?></textarea>
                  </div>
                  <div class="pl-2">
                    <button type="button" class="btn btn-success btn-sm" id="btn_actualizar_detalle">Actualizar detalle</button>
                  </div>
                </div>
              </div>

              <hr>

              <div class="table-responsive">
                <table id="invoice_item_table" class="table table-bordered table-striped table-sm" style="font-size:.85rem;vertical-align:middle;">
                  <thead>
                    <tr>
                      <th style="width:2%;text-align:center;"><i class="fa fa-list-ol"></i></th>
                      <th style="width:12%;text-align:center;">Tipo</th>
                      <th style="width:18%;text-align:center;">Descripción</th>
                      <th style="width:6%;text-align:center;">NroCajas</th>
                      <th style="width:8%;text-align:center;">Peso/Bruto</th>
                      <th style="width:8%;text-align:center;">Peso/Neto</th>
                      <th style="width:8%;text-align:center;">Precio</th>
                      <th style="width:8%;text-align:center;">SubTotal</th>
                      <th style="width:4%;text-align:center;"><i class="fa fa-cog"></i></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $count = 0;
                      foreach (($detalle_rows ?? []) as $d):
                        $count++;
                        $isAj = ((int)($d['id_tipoProducto'] ?? 0) === 20); // solo visual
                        $cjs  = (float)($d['cantidadCajas'] ?? 0);
                        $pb   = (float)($d['pesoB_kg'] ?? 0);
                        $pr   = (float)($d['precio'] ?? 0);
                        // manual SOLO si no hay datos de cálculo
                        $manual = (($cjs==0 && $pb==0 && $pr==0) ? 1 : 0);
                    ?>
                    <tr class="data-row <?= $isAj ? 'row-ajuste':'' ?>" id="row_id_<?php echo $count; ?>">
                      <td class="text-center align-middle"><span class="sr_no"><?php echo $count; ?></span></td>

                      <td class="align-middle">
                        <select class="js-select2 form-control form-control-sm sel-tipo" style="width:100%;font-size:10px"
                                name="item_tipoproducto[<?php echo $count; ?>]" id="item_tipoproducto<?php echo $count; ?>">
                          <option value="" disabled>Seleccionar</option>
                          <?php foreach (($tipoproductos_datos ?? []) as $t): ?>
                            <option value="<?= (int)$t['id_tipoProducto'] ?>" <?= ((int)$t['id_tipoProducto']===(int)$d['id_tipoProducto']?'selected':'') ?>>
                              <?= htmlspecialchars($t['name_tipoProducto'],ENT_QUOTES,'UTF-8') ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                        <?php if ($isAj): ?><span class="badge-ajuste">AJUSTE</span><?php endif; ?>
                      </td>

                      <td class="align-middle">
                        <input type="text" name="item_descripcion[<?php echo $count; ?>]" id="item_descripcion<?php echo $count; ?>"
                               class="form-control form-control-sm" value="<?= htmlspecialchars($d['descripcion']??'',ENT_QUOTES,'UTF-8') ?>">
                      </td>

                      <td class="align-middle">
                        <input type="text" name="item_cantidadCajas[<?php echo $count; ?>]" id="item_cantidadCajas<?php echo $count; ?>"
                               class="form-control form-control-sm text-center" value="<?= htmlspecialchars($d['cantidadCajas']??'0',ENT_QUOTES,'UTF-8') ?>" data-decimals="0">
                      </td>

                      <td class="align-middle">
                        <input type="text" name="item_pesoB_kg[<?php echo $count; ?>]" id="item_pesoB_kg<?php echo $count; ?>"
                               class="form-control form-control-sm text-center" value="<?= htmlspecialchars($d['pesoB_kg']??'0',ENT_QUOTES,'UTF-8') ?>" data-decimals="2">
                      </td>

                      <td class="align-middle">
                        <input type="text" name="item_pesoN_kg[<?php echo $count; ?>]" id="item_pesoN_kg<?php echo $count; ?>"
                               class="form-control form-control-sm text-center" value="<?= htmlspecialchars($d['pesoN_kg']??'0',ENT_QUOTES,'UTF-8') ?>" readonly>
                      </td>

                      <td class="align-middle">
                        <input type="text" name="item_precio[<?php echo $count; ?>]" id="item_precio<?php echo $count; ?>"
                               class="form-control form-control-sm text-right" value="<?= htmlspecialchars($d['precio']??'0',ENT_QUOTES,'UTF-8') ?>" data-decimals="2">
                      </td>

                      <td class="align-middle">
                        <input type="text" name="item_subtotal[<?php echo $count; ?>]" id="item_subtotal<?php echo $count; ?>"
                               class="form-control form-control-sm text-right"
                               value="<?= htmlspecialchars($d['subTotal']??'0',ENT_QUOTES,'UTF-8') ?>"
                               data-decimals="2" data-manual="<?= (int)$manual ?>">
                      </td>

                      <td class="text-center align-middle">
                        <button type="button" class="btn btn-danger btn-xs remove_row">-</button>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>

                  <tfoot>
                    <tr id="row_total">
                      <td colspan="7" class="text-right"><strong>TOTAL (Bs)</strong></td>
                      <th class="text-right" id="total"><?php echo number_format($totalInicial,2,'.',''); ?></th>
                      <td></td>
                    </tr>
                    <tr id="row_cxc">
                      <td colspan="7" class="text-right"><strong>Cuentas x Cobrar</strong></td>
                      <th class="text-right" id="lbl_cxc_resumen"><?php echo number_format($cxc_inicial,2,'.',''); ?></th>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>

                <div class="text-center">
                  <button type="button" name="add_row" id="add_row" class="btn btn-success btn-xs">+</button>
                </div>
              </div>

              <br>
              <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-success btn-sm" id="btn_actualizar_despacho">Guardar</button>
                <a href="index.php" class="btn btn-secondary btn-sm">Cancelar</a>
              </div>
            </form>

<script>
$(function(){
  const TARA_POR_CAJA = 2;
  let count = <?php echo max( (int)($count ?? 0), 0 ); ?>;

  function initSelect2In(ctx=document){
    $(ctx).find('.js-select2').each(function(){
      const $sel = $(this);
      const $parent = $sel.closest('#invoice_item_table').length
        ? $sel.closest('.table-responsive')
        : $('.content-wrapper');
      $sel.select2({placeholder:'Seleccionar', width:'100%', theme:'bootstrap4', dropdownParent:$parent, minimumResultsForSearch:5});
    });
  }
  initSelect2In();

  function calcPesoNeto(pb,cj){ return (pb - (cj * TARA_POR_CAJA)); }
  function calcSubtotal(pn,pr){ return pn * pr; }

  function normaliza(txt){
    if (txt === undefined || txt === null) return 0;
    const s = String(txt).trim();
    if (!s) return 0;
    try { return Number(math.evaluate(s)); } catch(e){ return parseFloat(s) || 0; }
  }

  // ==== Redondeos solicitados ====
  function toOneDecimalFloor(x){
    // trunca al primer decimal: 1886.65 => 1886.60
    return Math.floor((x + Number.EPSILON) * 10) / 10;
  }
  function redondeoCxC_porPrimerDecimal(total1d){
    // si primer decimal >= 0.7 => entero superior, si no inferior
    const frac = total1d - Math.floor(total1d); // [0.0..0.9]
    return (frac >= 0.7) ? Math.ceil(total1d) : Math.floor(total1d);
  }

  // ¿Es fila de ajuste? (id_tipoProducto == 20) -> solo estilo
  function esAjusteFila($row){
    const val = parseInt($row.find('select[name^="item_tipoproducto"]').val() || '0', 10);
    return val === 20;
  }
  function marcarFilaAjuste($row){
    const $sel = $row.find('select[name^="item_tipoproducto"]');
    const isAj = parseInt($sel.val() || '0',10) === 20;
    $row.toggleClass('row-ajuste', isAj);
    const $cell = $sel.closest('td');
    const hasBadge = $cell.find('.badge-ajuste').length > 0;
    if (isAj && !hasBadge) $cell.append('<span class="badge-ajuste">AJUSTE</span>');
    if (!isAj && hasBadge) $cell.find('.badge-ajuste').remove();
  }

  function calcularTotalTabla(){
    let total = 0;
    $('#invoice_item_table tbody tr').each(function(){
      const v = normaliza($(this).find('input[name^="item_subtotal"]').val());
      total += v;
    });
    return total;
  }

  // === Recalculo por fila (respeta modo manual/auto)
  function actualizarFila(id){
    const pb = normaliza($('#item_pesoB_kg'+id).val());
    const cj = normaliza($('#item_cantidadCajas'+id).val());
    const pr = normaliza($('#item_precio'+id).val());
    const pn = calcPesoNeto(pb,cj);
    $('#item_pesoN_kg'+id).val(pn.toFixed(2));

    const $st = $('#item_subtotal'+id);
    const isManual = String($st.data('manual')||'0') === '1';
    if (!isManual) {
      const st = calcSubtotal(pn,pr);
      $st.val(Number(st).toFixed(2));
    }
    actualizarTotales();
  }

  function actualizarTotales(){
    const total = calcularTotalTabla();               // suma con todos los decimales
    const total1d = toOneDecimalFloor(total);         // trunca al primer decimal
    const cxc     = redondeoCxC_porPrimerDecimal(total1d);

    // Mostrar total truncado al primer decimal, siempre con 2 decimales (x.y0)
    const totalMostrar = (Math.round(total1d * 10) / 10).toFixed(1) + '0';
    $('#total').text(totalMostrar);

    // CxC entero según regla
    $('#lbl_cxc_resumen').text(parseFloat(cxc).toFixed(2));
  }

  function normalizaYRecalcula($input){
    const id = $input.closest('tr').attr('id').split('_').pop();
    const dec = parseInt($input.data('decimals') ?? 2, 10);
    let n = normaliza($input.val());
    $input.val(Number(n).toFixed(dec));
    actualizarFila(id);
  }

  // ===== Eventos =====
  // SubTotal editado -> modo MANUAL
  $(document).on('input keydown blur','input[name^="item_subtotal"]', function(){
    $(this).data('manual', 1);
    actualizarTotales();
  });

  // Cajas/PesoB/Precio editados -> modo AUTO (y recalcular)
  $(document).on('input','input[name^="item_cantidadCajas"], input[name^="item_pesoB_kg"], input[name^="item_precio"]',function(){
    const $row = $(this).closest('tr');
    const id = $row.attr('id').split('_').pop();
    $('#item_subtotal'+id).data('manual', 0);
    actualizarFila(id);
  });

  // Normalización en blur/enter/tab
  $(document).on('keydown blur',
    'input[name^="item_cantidadCajas"], input[name^="item_pesoB_kg"], input[name^="item_precio"], input[name^="item_subtotal"]',
    function(e){ if (e.type==='blur' || e.key==='Enter' || e.key==='Tab'){ normalizaYRecalcula($(this)); } }
  );

  // Cambio de tipo: solo estilos + recalcular fila (sin tocar manual)
  $(document).on('change','select[name^="item_tipoproducto"]', function(){
    const $row = $(this).closest('tr');
    marcarFilaAjuste($row);
    const id = $row.attr('id').split('_').pop();
    actualizarFila(id);
  });

  // Generar detalle (normales primero, ajustes al final como AJUS(desc ±monto))
  function etiquetaOrdinal(idTipo){
    const n = parseInt(idTipo,10);
    switch(n){case 1:return '1º';case 2:return '2º';case 3:return 'BBº';case 4:return 'DCº';case 5:return 'TRº';default:return (isNaN(n)? '' : (n+'º')); }
  }
  function formatoAjuste(desc, monto){
    const sign = (monto >= 0) ? '+' : '-';
    const abs  = Math.abs(monto).toFixed(2);
    const dsc  = (desc && desc.trim()!=='') ? desc.trim()+' ' : '';
    return `AJUS (${dsc}${sign}${abs})`;
  }
  function generarDetalle(){
    const partesNormales = [];
    const partesAjuste   = [];

    $('#invoice_item_table tbody tr.data-row').each(function(){
      const $row = $(this);
      const idRow = $row.attr('id').split('_').pop();
      const tipo  = $row.find('select[name^="item_tipoproducto"]').val();
      if (!tipo) return;

      if (esAjusteFila($row)) {
        const desc = ($('#item_descripcion'+idRow).val()||'').trim();
        const monto = normaliza($('#item_subtotal'+idRow).val()); // usa subtotal
        partesAjuste.push(formatoAjuste(desc, monto));
      } else {
        const desc  = ($('#item_descripcion'+idRow).val()||'').trim();
        const pn    = normaliza($('#item_pesoN_kg'+idRow).val());
        const pr    = normaliza($('#item_precio'+idRow).val());
        const etq   = etiquetaOrdinal(tipo);
        const kg    = Number.isInteger(pn) ? pn.toFixed(0) : pn.toFixed(1);
        const ptxt  = (pr%1===0 ? pr.toFixed(0) : pr.toString());
        const descTxt = desc ? ` ${desc}` : '';
        partesNormales.push(`${etq}${descTxt} ${kg}kg (${ptxt})`);
      }
    });

    let texto = partesNormales.join(' | ');
    if (partesAjuste.length > 0) {
      texto = texto ? `${texto} || ${partesAjuste.join(' | ')}` : partesAjuste.join(' | ');
    }
    $('#descripcion').val(texto);
  }
  $('#btn_actualizar_detalle').on('click', generarDetalle);

  // Quitar fila
  $(document).on('click','.remove_row', function(){ $(this).closest('tr').remove(); actualizarTotales(); });

  // Agregar fila
  $('#add_row').on('click', function(){
    count++;
    const html = `
      <tr class="data-row" id="row_id_${count}">
        <td class="text-center align-middle"><span class="sr_no">${count}</span></td>
        <td class="align-middle">
          <select class="js-select2 form-control form-control-sm sel-tipo" style="width:100%;font-size:10px"
                  name="item_tipoproducto[${count}]" id="item_tipoproducto${count}">
            <option value="" disabled selected>Seleccionar</option>
            <?php foreach (($tipoproductos_datos ?? []) as $t): ?>
              <option value="<?= (int)$t['id_tipoProducto'] ?>"><?= htmlspecialchars($t['name_tipoProducto'],ENT_QUOTES,'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
        </td>
        <td class="align-middle"><input type="text" name="item_descripcion[${count}]" id="item_descripcion${count}" class="form-control form-control-sm"></td>
        <td class="align-middle"><input type="text" name="item_cantidadCajas[${count}]" id="item_cantidadCajas${count}" class="form-control form-control-sm text-center" placeholder="0" data-decimals="0"></td>
        <td class="align-middle"><input type="text" name="item_pesoB_kg[${count}]" id="item_pesoB_kg${count}" class="form-control form-control-sm text-center" placeholder="0.00" data-decimals="2"></td>
        <td class="align-middle"><input type="text" name="item_pesoN_kg[${count}]" id="item_pesoN_kg${count}" class="form-control form-control-sm text-center" placeholder="0.00" readonly></td>
        <td class="align-middle"><input type="text" name="item_precio[${count}]" id="item_precio${count}" class="form-control form-control-sm text-right" placeholder="0.00" data-decimals="2"></td>
        <td class="align-middle"><input type="text" name="item_subtotal[${count}]" id="item_subtotal${count}" class="form-control form-control-sm text-right" placeholder="0.00" data-decimals="2" data-manual="1"></td>
        <td class="text-center align-middle"><button type="button" class="btn btn-danger btn-xs remove_row">-</button></td>
      </tr>`;
    $('#invoice_item_table tbody').append(html);
    initSelect2In($('#row_id_'+count));
  });

  // ===== Inicialización al cargar: NO recalcular SubTotal, solo Peso Neto y totales =====
  $('#invoice_item_table tbody tr.data-row').each(function(){
    marcarFilaAjuste($(this));
    const id = $(this).attr('id').split('_').pop();
    const pb = normaliza($('#item_pesoB_kg'+id).val());
    const cj = normaliza($('#item_cantidadCajas'+id).val());
    const pn = calcPesoNeto(pb,cj);
    $('#item_pesoN_kg'+id).val(pn.toFixed(2));
  });
  actualizarTotales();

  // ===== Validación de filas antes de guardar =====
  function filaTieneDatos($row){
    const idRow = $row.attr('id').split('_').pop();
    const desc  = ($('#item_descripcion'+idRow).val()||'').trim();
    const cj    = normaliza($('#item_cantidadCajas'+idRow).val());
    const pb    = normaliza($('#item_pesoB_kg'+idRow).val());
    const pn    = normaliza($('#item_pesoN_kg'+idRow).val());
    const pr    = normaliza($('#item_precio'+idRow).val());
    const st    = normaliza($('#item_subtotal'+idRow).val());
    return (desc || cj || pb || pn || pr || st); // cualquiera con contenido
  }

  function validarFilasTipos(){
    let ok = true;
    $('#invoice_item_table tbody tr.data-row').each(function(){
      const $row = $(this);
      const tipo = $row.find('select[name^="item_tipoproducto"]').val();
      const tieneAlgo = filaTieneDatos($row);

      // limpiar estado previo
      $row.removeClass('row-error');
      const $sel = $row.find('select[name^="item_tipoproducto"]');
      $sel.removeClass('is-invalid');

      if (tieneAlgo && (!tipo || tipo === '')) {
        ok = false;
        $row.addClass('row-error');
        $sel.addClass('is-invalid');
      }
    });
    return ok;
  }

  // Guardar
  $('#btn_actualizar_despacho').on('click', function(){
    // Validación de tipos
    if (!validarFilasTipos()){
      Swal.fire({icon:'warning', title:'Faltan tipos', text:'Selecciona el Tipo en las filas con datos.', timer:1500, showConfirmButton:false});
      return;
    }

    const detalles = [];
    $('#invoice_item_table tbody tr.data-row').each(function(){
      const rowId = $(this).attr('id').split('_').pop();
      const prod  = $(this).find('select[name^="item_tipoproducto["]').val();
      if (!prod) return; // filas vacías sin tipo no viajan

      detalles.push({
        id_tipoProducto: prod,
        descripcionD:   $('#item_descripcion'+rowId).val(),
        cantidadCajas:  $('#item_cantidadCajas'+rowId).val(),
        pesoB_kg:       $('#item_pesoB_kg'+rowId).val(),
        pesoN_kg:       $('#item_pesoN_kg'+rowId).val(),
        precio:         $('#item_precio'+rowId).val(),
        subTotal:       $('#item_subtotal'+rowId).val()
      });
    });

    const payload = {
      id_comprobante:     $('#id_comprobante').val(),
      id_tipocomprobante: $('#id_tipocomprobante').val(),
      num_comprobante:    $('#num_comprobante').val(),
      fecha_comprobante:  $('#fecha_comprobante').val(),
      hora_comprobante:   $('#hora_comprobante').val(),
      id_persona:         $('#id_persona').val(),
      descripcionC:       $('#descripcion').val(),
      descripcionT:       $('#descripcion').val(),
      id_subcuenta1:      $('#id_subcuenta1').val() || '<?php echo isset($t1['id_subCuenta']) ? (int)$t1['id_subCuenta'] : '';?>',
      id_subcuenta2:      $('#id_subcuenta2').val() || '<?php echo isset($t2['id_subCuenta']) ? (int)$t2['id_subCuenta'] : '';?>',
      total:              parseFloat($('#total').text()) || 0,
      id_usuario:         '<?php echo (int)$id_usuario; ?>',
      detalles_transacciones: detalles
    };

    axios.post("../app/controllers/despachos/update.php", payload, {headers:{'Content-Type':'application/json'}})
      .then(r=>{
        if (r.data && r.data.success){
          Swal.fire({icon:'success',title:'¡Actualizado!',text:'Nota de despacho actualizada',timer:1000,showConfirmButton:false})
            .then(()=>{ window.location.href = "<?php echo $URL ?>/despachos"; });
        } else {
          const msg = (r.data && r.data.error) ? r.data.error : 'No se pudo actualizar.';
          Swal.fire({icon:'error',title:'Error',text:msg});
        }
      })
      .catch(err=>{
        console.error(err);
        Swal.fire({icon:'error',title:'Error',text:'Error de comunicación con el servidor'});
      });
  });
});
</script>

          </div>
        </div>
      </div></div>
    </div>
  </div>
</div>

<script>
if (sessionStorage.getItem('mensaje') && sessionStorage.getItem('icono')) {
  Swal.fire({icon:sessionStorage.getItem('icono'), title:sessionStorage.getItem('mensaje'), showConfirmButton:false, timer:1500});
  sessionStorage.removeItem('mensaje'); sessionStorage.removeItem('icono');
}
</script>

<?php
include('../layout/mensajes.php');
include('../layout/parte2.php');
?>
