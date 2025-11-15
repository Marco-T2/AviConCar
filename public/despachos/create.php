<?php
// C:\web\stack\ant\public\despachos\create.php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/tipocomprobantes/listado_tipocomprobantes.php');
include('../app/controllers/subCuentas/listado_subCuentas.php');
include('../app/controllers/tipoproducto/listado_tipoproducto.php');
include('../app/controllers/personas/listado_personas.php'); // $personas_datos

if (!isset($fecha) || !$fecha) $fecha = date('Y-m-d');
if (!isset($hora)  || !$hora)  $hora  = date('H:i');
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
  .select2-selection.border-danger { border: 1px solid #dc3545 !important; }
  .is-invalid + .select2 .select2-selection { border-color: #dc3545 !important; }
</style>

<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row"><div class="col-md-12">
        <div class="card card-outline card-success">
          <div class="card-header"><h3 class="card-title" style="font-size:.95rem">NUEVO DESPACHO</h3></div>

          <div class="card-body">
            <form action="#" method="post" onsubmit="return false;">
              <div class="row mb-2">
                <div class="col-md-3" hidden>
                  <label class="form-label text-sm">Tipo despacho</label>
                  <select id="id_tipocomprobante" class="js-select2 form-control form-control-sm" name="id_tipocomprobante" style="width:100%;">
                    <?php foreach (($tipocomprobantesVentas_datos ?? []) as $t): ?>
                      <option value="<?= (int)$t['id_tipocomprobante'] ?>" <?= ((int)$t['id_tipocomprobante']===7?'selected':'') ?>>
                        <?= htmlspecialchars($t['name_tipocomprobante'],ENT_QUOTES,'UTF-8') ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label text-sm">CLIENTE</label>
                  <select id="id_persona" name="id_persona"
                          class="js-select2 form-control form-control-sm"
                          style="width:100%;" autocomplete="off">
                    <option value="" disabled selected>Seleccionar</option>
                    <?php foreach (($personas_datos ?? []) as $p): ?>
                      <option value="<?= (int)$p['id_persona'] ?>">
                        <?= (int)$p['id_persona'].' - '.htmlspecialchars($p['name_persona'],ENT_QUOTES,'UTF-8') ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-2">
                  <label class="form-label text-sm">Nro DESPACHO</label>
                  <input type="number" id="num_comprobante" name="num_comprobante" class="form-control form-control-sm" placeholder="#" required>
                </div>

                <div class="col-md-3">
                  <label class="form-label text-sm">FECHA</label>
                  <input type="date" id="fecha_comprobante" name="fecha_comprobante" class="form-control form-control-sm" value="<?= $fecha ?>" required>
                </div>

                <div class="col-md-3">
                  <label class="form-label text-sm">HORA</label>
                  <input type="time" id="hora_comprobante" name="hora_comprobante" class="form-control form-control-sm" value="<?= $hora ?>" required>
                </div>
              </div>

              <div class="form-group">
                <div class="d-flex justify-content-between align-items-end">
                  <div style="flex:1">
                    <label class="form-label text-sm">DETALLE DE LA VENTA</label>
                    <textarea id="descripcion" name="descripcion" class="form-control form-control-sm" rows="3"
                      placeholder="Ej: 1º Pechuga 18kg (17) | 2º Alas 56kg (16)"></textarea>
                  </div>
                  <div class="pl-2">
                    <button type="button" class="btn btn-success btn-sm" id="btn_actualizar_detalle">Actualizar detalle</button>
                  </div>
                </div>
              </div>

              <div class="form-group" hidden>
                <input type="text" id="id_usuario" name="id_usuario" class="form-control" value="<?= isset($id_usuario)? (int)$id_usuario : 0; ?>">
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

                  <tbody></tbody>

                  <tfoot>
                    <tr id="row_total">
                      <td colspan="7" class="text-right"><strong>TOTAL (Bs)</strong></td>
                      <th class="text-right" id="total">0.00</th>
                      <td></td>
                    </tr>
                    <tr id="row_cxc">
                      <td colspan="7" class="text-right"><strong>Cuentas x Cobrar</strong></td>
                      <th class="text-right" id="lbl_cxc_resumen">0.00</th>
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
                <button type="button" class="btn btn-success btn-sm" id="btn_crear_despacho">Guardar</button>
                <a href="index.php" class="btn btn-secondary btn-sm">Cancelar</a>
              </div>
            </form>

<script>
$(function(){
  const TARA_POR_CAJA  = 2;
  let count = 0;

  // Select2 (mismo init que en update; sin allowClear)
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

  // Forzar que CLIENTE arranque vacío (evita autofill)
  $('#id_persona').val(null).trigger('change');

  function etiquetaOrdinal(idTipo){
    const n = parseInt(idTipo,10);
    switch(n){
      case 1: return '1º';
      case 2: return '2º';
      case 3: return 'BBº';
      case 4: return 'DCº';
      case 5: return 'TRº';
      default: return (isNaN(n)? '' : (n+'º'));
    }
  }

  function calcPesoNeto(pb,cj){ return (pb - (cj * TARA_POR_CAJA)); }
  function calcSubtotal(pn,pr){ return pn * pr; }

  // === Redondeos solicitados ===
  function toOneDecimalFloor(x){
    // trunca al primer decimal: 1886.65 => 1886.60
    return Math.floor((x + Number.EPSILON) * 10) / 10;
  }
  function redondeoCxC_porPrimerDecimal(total1d){
    // si primer decimal >= 0.7 => entero superior; si no, inferior
    const frac = total1d - Math.floor(total1d);
    return (frac >= 0.7) ? Math.ceil(total1d) : Math.floor(total1d);
  }

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

  function actualizarFila(id){
    const pb = parseFloat($('#item_pesoB_kg'+id).val()) || 0;
    const cj = parseFloat($('#item_cantidadCajas'+id).val()) || 0;
    const pr = parseFloat($('#item_precio'+id).val()) || 0;
    const pn = calcPesoNeto(pb,cj);
    const st = calcSubtotal(pn,pr);
    $('#item_pesoN_kg'+id).val(pn.toFixed(2));
    if (!$('#item_subtotal'+id).is(':focus')) {
      $('#item_subtotal'+id).val(st.toFixed(2));
    }
    actualizarTotales();
  }

  function calcularTotalTabla(){
    let total = 0;
    $('#invoice_item_table tbody tr').each(function(){
      const v = parseFloat($(this).find('input[name^="item_subtotal"]').val()) || 0;
      total += v;
    });
    return total;
  }

  function actualizarTotales(){
    const total = calcularTotalTabla();        // suma con todos los decimales
    const total1d = toOneDecimalFloor(total);  // trunca al primer decimal
    const cxc     = redondeoCxC_porPrimerDecimal(total1d);

    // Mostrar total truncado a 1 decimal con 2 decimales (x.y0)
    const totalMostrar = (Math.round(total1d * 10) / 10).toFixed(1) + '0';
    $('#total').text(totalMostrar);

    // CxC entero según regla (mostrar 2 decimales por estética)
    $('#lbl_cxc_resumen').text(parseFloat(cxc).toFixed(2));
  }

  // Permite expresiones y normaliza (Cajas/PesoB/Precio/Subtotal)
  function normalizaYRecalcula($input){
    const id  = $input.closest('tr').attr('id').split('_').pop();
    const isSubtotal = $input.is('[name^="item_subtotal"]');
    const dec = parseInt($input.data('decimals') ?? 2, 10);
    let txt = ($input.val()||'').trim();
    let n = 0;
    if (txt !== '') {
      try { n = math.evaluate(txt); }
      catch(e){ n = parseFloat(txt)||0; }
    }
    $input.val(Number(n).toFixed(isSubtotal ? 2 : dec));
    if (isSubtotal) {
      actualizarTotales();
    } else {
      actualizarFila(id);
    }
  }

  // Eventos de edición
  $(document).on('keydown blur',
    'input[name^="item_cantidadCajas"], input[name^="item_pesoB_kg"], input[name^="item_precio"], input[name^="item_subtotal"]',
    function(e){ if (e.type==='blur' || e.key==='Enter' || e.key==='Tab'){ normalizaYRecalcula($(this)); } }
  );
  $(document).on('input','input[name^="item_cantidadCajas"], input[name^="item_pesoB_kg"], input[name^="item_precio"]',function(){
    const id = $(this).closest('tr').attr('id').split('_').pop(); actualizarFila(id);
  });
  $(document).on('input','input[name^="item_subtotal"]', actualizarTotales);

  // Cambio de tipo: estilos + recalcular
  $(document).on('change','select[name^="item_tipoproducto"]', function(){
    const $row = $(this).closest('tr');
    marcarFilaAjuste($row);
    const id = $row.attr('id').split('_').pop();
    actualizarFila(id);
  });

  // Generar detalle (normales primero, ajustes al final como AJUS(desc ±monto))
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
        const desc  = ($('#item_descripcion'+idRow).val()||'').trim();
        const monto = parseFloat($('#item_subtotal'+idRow).val()) || 0;
        partesAjuste.push(formatoAjuste(desc, monto));
      } else {
        const desc  = ($('#item_descripcion'+idRow).val()||'').trim();
        const pn    = parseFloat($('#item_pesoN_kg'+idRow).val()) || 0;
        const pr    = parseFloat($('#item_precio'+idRow).val()) || 0;
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

  // Agregar fila de detalle
  function addRow(){
    count++;
    const html = `
      <tr class="data-row" id="row_id_${count}">
        <td class="text-center align-middle"><span class="sr_no">${count}</span></td>
        <td class="align-middle">
          <select class="js-select2 form-control form-control-sm sel-tipo" style="width:100%;font-size:10px"
                  name="item_tipoproducto[${count}]" id="item_tipoproducto${count}">
            <option value="" disabled selected>Seleccionar</option>
            <?php foreach ($tipoproductos_datos as $t): ?>
              <option value="<?= (int)$t['id_tipoProducto'] ?>"><?= htmlspecialchars($t['name_tipoProducto'],ENT_QUOTES,'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
        </td>
        <td class="align-middle"><input type="text" name="item_descripcion[${count}]" id="item_descripcion${count}" class="form-control form-control-sm"></td>
        <td class="align-middle"><input type="text" name="item_cantidadCajas[${count}]" id="item_cantidadCajas${count}" class="form-control form-control-sm text-center" placeholder="0" data-decimals="0"></td>
        <td class="align-middle"><input type="text" name="item_pesoB_kg[${count}]" id="item_pesoB_kg${count}" class="form-control form-control-sm text-center" placeholder="0.00" data-decimals="2"></td>
        <td class="align-middle"><input type="text" name="item_pesoN_kg[${count}]" id="item_pesoN_kg${count}" class="form-control form-control-sm text-center" placeholder="0.00" readonly></td>
        <td class="align-middle"><input type="text" name="item_precio[${count}]" id="item_precio${count}" class="form-control form-control-sm text-right" placeholder="0.00" data-decimals="2"></td>
        <td class="align-middle"><input type="text" name="item_subtotal[${count}]" id="item_subtotal${count}" class="form-control form-control-sm text-right" placeholder="0.00" data-decimals="2"></td>
        <td class="text-center align-middle"><button type="button" class="btn btn-danger btn-xs remove_row">-</button></td>
      </tr>`;
    $('#invoice_item_table tbody').append(html);
    initSelect2In($('#row_id_'+count));
  }

  // Botón agregar fila
  $('#add_row').on('click', addRow);

  // Quitar fila
  $(document).on('click','.remove_row', function(){
    $(this).closest('tr').remove();
    actualizarTotales();
  });

  // Fila inicial
  addRow();
  actualizarTotales();

  // ===== Validación de filas antes de guardar =====
  function filaTieneDatos($row){
    const idRow = $row.attr('id').split('_').pop();
    const descT = ($('#item_descripcion'+idRow).val() || '').trim();
    const cajT  = ($('#item_cantidadCajas'+idRow).val() || '').trim();
    const pbT   = ($('#item_pesoB_kg'+idRow).val() || '').trim();
    const pnT   = ($('#item_pesoN_kg'+idRow).val() || '').trim();
    const prT   = ($('#item_precio'+idRow).val() || '').trim();
    const stT   = ($('#item_subtotal'+idRow).val() || '').trim();
    return !!(descT !== '' || cajT !== '' || pbT !== '' || pnT !== '' || prT !== '' || stT !== '');
  }

  function validarFilasTipos(){
    let ok = true;
    $('#invoice_item_table tbody tr.data-row').each(function(){
      const $row = $(this);
      const $sel = $row.find('select[name^="item_tipoproducto"]');
      const tipo = $sel.val();
      const tieneAlgo = filaTieneDatos($row);

      // limpiar estado previo
      $row.removeClass('row-error');
      $sel.removeClass('is-invalid');
      $sel.next('.select2').find('.select2-selection').removeClass('border-danger');

      if (tieneAlgo && (!tipo || tipo === '')) {
        ok = false;
        $row.addClass('row-error');
        $sel.addClass('is-invalid');
        $sel.next('.select2').find('.select2-selection').addClass('border-danger');
      }

      // marcar/estilo de ajuste si corresponde
      marcarFilaAjuste($row);
    });
    return ok;
  }

  // Guardar
  $('#btn_crear_despacho').on('click', async function(){
    if (!validarFilasTipos()){
      Swal.fire({icon:'warning', title:'Faltan tipos', text:'Selecciona el Tipo en las filas con datos.', timer:1500, showConfirmButton:false});
      return;
    }

    const detalles = [];
    $('#invoice_item_table tbody tr.data-row').each(function(){
      const rowId = $(this).attr('id').split('_').pop();
      const prod   = $(this).find('select[name^="item_tipoproducto["]').val();
      if (!prod) return;
      const dsc    = $('#item_descripcion'+rowId).val();
      const caj    = $('#item_cantidadCajas'+rowId).val();
      const pb     = $('#item_pesoB_kg'+rowId).val();
      const pn     = $('#item_pesoN_kg'+rowId).val();
      const pre    = $('#item_precio'+rowId).val();
      const sub    = $('#item_subtotal'+rowId).val();

      detalles.push({
        id_tipoProducto: prod,
        descripcionD:   dsc,
        cantidadCajas:  caj,
        pesoB_kg:       pb,
        pesoN_kg:       pn,
        precio:         pre,
        subTotal:       sub
      });
    });

    // Total desde subtotales (ajustes incluidos si están como tipo=20)
    const totalTabla = detalles.reduce((a,d)=>a + (parseFloat(d.subTotal)||0), 0);
    // CxC según regla: truncar 1 decimal y luego >=0.7 -> arriba
    const total1d = Math.floor((totalTabla + Number.EPSILON) * 10) / 10;
    const frac = total1d - Math.floor(total1d);
    const cxcFinal = (frac >= 0.7) ? Math.ceil(total1d) : Math.floor(total1d);

    const payload = {
      id_tipocomprobante: $('#id_tipocomprobante').val(),
      num_comprobante: $('#num_comprobante').val(),
      fecha_comprobante: $('#fecha_comprobante').val(),
      hora_comprobante: $('#hora_comprobante').val(),
      id_persona: $('#id_persona').val(),
      descripcionC: $('#descripcion').val(),
      descripcionT: $('#descripcion').val(),
      id_usuario: $('#id_usuario').val(),
      // subcuentas: si no las envías, el backend pone defaults
      detalles_transacciones: detalles
    };

    try{
      const res = await axios.post("../app/controllers/despachos/create.php", payload, {headers:{'Content-Type':'application/json'}});
      if(res.data && res.data.success){
        await Swal.fire({
          icon:'success',
          title:'¡Éxito!',
          html:`Nota de despacho creada.<br>
                <small>Total: <b>${total1d.toFixed(1)}0</b> · CxC: <b>${parseFloat((res.data.cxc_final ?? cxcFinal)).toFixed(2)}</b></small>`,
          timer:1200, showConfirmButton:false
        });
        window.location.href = "<?php echo $URL ?>/despachos";
      }else{
        Swal.fire({icon:'error',title:'Error',text: res.data?.error || 'No se pudo crear la nota de despacho'});
      }
    }catch(err){
      console.error(err);
      Swal.fire({icon:'error',title:'Error',text:'No se pudo comunicar con el servidor'});
    }
  });
});
</script>

          </div>
        </div>
      </div></div>
    </div>
  </div>
</div>

<?php
include('../layout/mensajes.php');
include('../layout/parte2.php');
?>
