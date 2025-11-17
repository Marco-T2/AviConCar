<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

// ENTREGA DE CAJAS (form) - similar a recojo pero sin campos de verificación
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-md-8">
          <h4 style="margin:0">ENTREGA DE CAJAS</h4>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-body">
          <form id="entregaForm">
            <div class="form-grid">
              <div class="form-item"><label>Fecha: <input type="date" id="f_fecha" name="fecha" required></label></div>
              <div class="form-item"><label>Encargado: <input type="text" id="f_chofer" name="chofer" class="input-wide"></label></div>
            </div>
            <div class="form-actions"><button type="submit" class="btn btn-success">Guardar (console.log)</button></div>

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
              <div></div>
              <div><button type="button" id="addRowBtn" class="btn btn-primary">+ Agregar fila</button></div>
            </div>

            <?php
            // obtener tipos de caja activos para mostrar columnas dinámicas
            $tipos_caja = [];
            try {
              $stmt = $pdo->query("SELECT codigo, descripcion FROM tipo_caja WHERE activo=1 ORDER BY id ASC");
              $tipos_caja = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
              $tipos_caja = [];
            }
            $tipos_caja_safe = [];
            foreach ($tipos_caja as $t) {
              $raw = $t['codigo'];
              $safe = preg_replace('/[^a-zA-Z0-9_]/', '_', $raw);
              $tipos_caja_safe[] = ['raw'=>$raw, 'code'=>$safe, 'label'=> ($t['descripcion'] ?: $raw) ];
            }
            ?>

            <div class="table-container">
              <div class="entrega-summary" style="margin-bottom:10px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-start">
                <div style="flex:1;min-width:240px;border:1px solid #e3e3e3;padding:8px;border-radius:4px;background:#fbfbfd">
                  <strong>CAJAS TOTALES PARA ENTREGAR</strong>
                  <div id="para-entregar-list" style="margin-top:8px"></div>
                  <div style="margin-top:8px;text-align:right"><strong>Total: <span id="para-entregar-total">0</span></strong></div>
                </div>
                <div style="flex:1;min-width:240px;border:1px solid #e3e3e3;padding:8px;border-radius:4px;background:#fbfbfd">
                  <strong>CAJAS TOTALES ENTREGADOS</strong>
                  <div id="entregados-list" style="margin-top:8px"></div>
                  <div style="margin-top:8px;text-align:right"><strong>Total: <span id="entregados-total">0</span></strong></div>
                </div>
                <div style="width:160px;border:1px solid #e3e3e3;padding:8px;border-radius:4px;background:#fff;display:flex;flex-direction:column;justify-content:center;align-items:center">
                  <strong>TOTAL GENERAL</strong>
                  <div style="margin-top:8px;font-size:18px" id="total-general">0</div>
                </div>
              </div>
              <table id="entrega-table" class="table-excel">
                <thead>
                  <tr>
                    <th>Nro</th>
                    <th>NroDespacho</th>
                    <th class="client-cell">Cliente</th>
                    <?php foreach($tipos_caja_safe as $t): ?>
                      <th><?php echo htmlspecialchars($t['raw']); ?></th>
                    <?php endforeach; ?>
                    <th class="obs-cell">Observación</th>
                    <th>Acción</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- filas dinámicas (inician con 2 filas especiales) -->
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="3" style="text-align:right;font-weight:bold">TOTAL</td>
                    <?php foreach($tipos_caja_safe as $t): ?>
                      <td><input readonly class="total" id="total-<?php echo $t['code']; ?>" value="" placeholder="0"></td>
                    <?php endforeach; ?>
                    <td colspan="2"></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include('../layout/parte2.php');
include('../layout/mensajes.php');
?>

<style>
  /* reuse table styles from recojo but keep local overrides */
  .table-excel { width:100%; border-collapse: collapse; font-size:12px; }
  .table-excel thead th, .table-excel tfoot td, .table-excel td { border: 1px solid #ddd; padding: 6px; }
  .table-excel thead th { background:#f3f3f3; text-align:left; }
  .table-excel tbody td input[type="text"], .table-excel tbody td input[type="number"] { width:100%; box-sizing:border-box; border: none; padding:4px; font-size:12px; }
  .table-excel tbody td input[type="number"] { text-align:right; }
  .table-excel tbody td input:focus { outline: 1px solid #6ea8fe; }
  .table-excel tfoot input.total { width:100%; border:none; background:transparent; font-weight:bold; text-align:right; font-size:12px; }

  /* consistent numeric input sizing for dynamic columns */
  .table-excel .qty { width:44px; text-align:right; }
  .table-excel th.nro-cell, .table-excel td.cell-nro { width:48px; max-width:48px; text-align:center; }
  .btn-sm { padding:4px 6px; font-size:12px; }
  .icon-btn { width:28px; height:28px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:4px; }
  .icon-btn svg { display:block; color:#fff; }
  /* client column fixed, observation flexible */
  .table-excel td.client-cell { width:180px; max-width:180px; }
  .table-excel .cliente-input { width:100%; box-sizing:border-box; }
  .table-excel td.obs-cell { width: auto; }
  .table-excel .obs-input { width:100%; box-sizing:border-box; }

  @media (max-width: 768px) {
    .table-excel td.client-cell { width:140px; max-width:140px; }
    .table-excel .obs-input { width:100%; }
  }

  .btn { padding:6px 10px; border-radius:4px; border:1px solid #2f6f9f; background:#2f6f9f; color:#fff; cursor:pointer }
  .btn-primary { background:#007bff; border-color:#007bff }
  .btn-success { background:#28a745; border-color:#28a745 }

  .table-container { max-width: 920px; margin: 0 auto; overflow:auto; }
  .table-excel tbody td:nth-child(2) { width:80px; }
  .table-excel tbody td:nth-child(3) input { min-width:150px; }
  .table-excel tbody td:nth-child(4), .table-excel tbody td:nth-child(5), .table-excel tbody td:nth-child(6), .table-excel tbody td:nth-child(7), .table-excel tbody td:nth-child(8) { width:48px }
  .table-excel tbody td:nth-child(4) input, .table-excel tbody td:nth-child(5) input, .table-excel tbody td:nth-child(6) input, .table-excel tbody td:nth-child(7) input, .table-excel tbody td:nth-child(8) input { width:44px; text-align:right; }
  .table-excel td.obs-cell .obs-input { min-width:260px; }

  /* special row styles */
  .row-ajuste { background: #fff3cd; }
  .row-despacho { background: #cfe8ff; }
  .row-diahoy { background: #e9d7ff; }
  .fixed-source td { font-weight:700; }
  tfoot tr.balanced { background: #e6ffed; }

  /* form grid */
  .form-grid { display:flex; justify-content:center; gap:12px; align-items:center; margin-bottom:8px; flex-wrap:wrap; }
  .form-grid .form-item { display:flex; align-items:center; }
  .form-actions { width:100%; display:flex; justify-content:flex-end; margin-bottom:8px; }
  .form-grid input.input-wide { width:320px; box-sizing:border-box; }
  .form-grid label { font-weight:600; margin-right:6px; }

  /* responsive (basic) */
  @media (max-width: 768px) {
    .table-container { max-width: 100%; padding: 0 8px; }
    .table-excel { font-size:11px; display:block; overflow-x:auto; white-space:nowrap; }
    .form-grid { flex-direction:column; }
    #addRowBtn, .btn-success { width:100%; }
  }
</style>

<datalist id="clientes-list">
  <option value="OtrosTraspasos"></option>
  <option value="DespachoMatadero"></option>
  <option value="SaldoDeposito"></option>
  <option value="SaldoDeposito-DiaAnt"></option>
  <option value="SaldoDeposito-DiaHoy"></option>
  <option value="Ajuste-Cajas"></option>
  <?php
  include_once('../app/controllers/personas/listado_personas.php');
  if (!empty($personas_datos)){
    foreach($personas_datos as $p){
      $nombre = htmlspecialchars($p['name_persona']);
      echo "  <option value=\"{$nombre}\"></option>\n";
    }
  }
  ?>
</datalist>

<script>
const TIPOS_CAJA = <?php echo json_encode($tipos_caja_safe, JSON_HEX_TAG|JSON_HEX_AMP); ?>;
</script>

<script>
(() => {
  const tbody = document.querySelector('#entrega-table tbody');
  const addRowBtn = document.getElementById('addRowBtn');
  const form = document.getElementById('entregaForm');

  let rowCount = 0;

  function renumberRows(){
    let i = 1;
    Array.from(tbody.querySelectorAll('tr')).forEach(tr => {
      const cell = tr.querySelector('.cell-nro');
      if (cell) cell.textContent = i++;
    });
    rowCount = i-1;
  }

  function createRow(data = {}, fixed = false){
    const tr = document.createElement('tr');
    let html = '';
    html += `<td class="cell-nro"></td>`;
    html += `<td><input type="text" name="nroDesp[]" value="${escapeHtml(data.nroDesp || '')}"></td>`;
    html += `<td class="client-cell"><input list="clientes-list" type="text" name="cliente[]" class="cliente-input" value="${escapeHtml(data.cliente || '')}"></td>`;
    // columnas por tipo
    TIPOS_CAJA.forEach(t => {
      const val = (data[t.code] !== undefined) ? data[t.code] : '';
      html += `<td><input type="number" min="0" name="qty[${t.code}][]" class="qty qty-${t.code}" placeholder="0" value="${val}"></td>`;
    });

    html += `<td class="obs-cell"><input type="text" class="obs-input" name="obs[]" value="${escapeHtml(data.obs || '')}"></td>`;
    // delete column: if fixed keep empty placeholder, else add delete button (icon)
    if (fixed) html += `<td></td>`; else {
      html += `<td style="text-align:center"><button type="button" class="btn btn-sm btn-danger delete-row icon-btn" title="Eliminar fila" aria-label="Eliminar fila">`;
      html += `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 6v14a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 6l1-2h4l1 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
      html += `</button></td>`;
    }

    if (fixed) tr.classList.add('fixed-source');

    tbody.appendChild(tr);
    tr.innerHTML = html;

    // listeners
    tr.querySelectorAll('.qty').forEach(el => el.addEventListener('input', recalcTotals));
    const clienteInput = tr.querySelector('.cliente-input');
    const checkAjuste = () => {
      const v = (clienteInput.value || '').trim();
      // only Ajuste-Cajas uses the yellow ajuste style
      tr.classList.toggle('row-ajuste', v === 'Ajuste-Cajas');
      // these are considered source/despacho rows (blue background) — SaldoDeposito-DiaHoy is visual-only
      tr.classList.toggle('row-despacho', v === 'DespachoMatadero' || v === 'SaldoDeposito' || v === 'SaldoDeposito-DiaAnt' || v === 'OtrosTraspasos');
      // SaldoDeposito-DiaHoy gets a visual lilac class but is NOT a source for validation
      tr.classList.toggle('row-diahoy', v === 'SaldoDeposito-DiaHoy');
    };
    clienteInput.addEventListener('input', checkAjuste);
    checkAjuste();

    // delete handler (only exists on non-fixed rows)
    const delBtn = tr.querySelector('.delete-row');
    if (delBtn) delBtn.addEventListener('click', () => { tr.remove(); renumberRows(); recalcTotals(); });

    renumberRows();
  }

  function escapeHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }

  function recalcTotals(){
    // compute totals per tipo for sources (fixed-source rows) and entregas (other rows)
    const sourceTotals = {};
    const entregaTotals = {};
    TIPOS_CAJA.forEach(t => { sourceTotals[t.code] = 0; entregaTotals[t.code] = 0; });

    Array.from(tbody.querySelectorAll('tr')).forEach(tr => {
      const isSource = tr.classList.contains('fixed-source') || ['DespachoMatadero','SaldoDeposito','SaldoDeposito-DiaAnt','OtrosTraspasos'].includes((tr.querySelector('.cliente-input')||{value:''}).value);
      TIPOS_CAJA.forEach(t => {
        const sel = tr.querySelector(`input[name="qty[${t.code}][]"]`);
        const n = sel ? (parseFloat(sel.value || 0) || 0) : 0;
        if (isSource) sourceTotals[t.code] += n; else entregaTotals[t.code] += n;
      });
    });

    // update footer with diff per tipo (sources - entregas)
    const diff = {};
    let sourcesSum = 0, entregasSum = 0;
    TIPOS_CAJA.forEach(t => {
      diff[t.code] = (sourceTotals[t.code] || 0) - (entregaTotals[t.code] || 0);
      const el = document.getElementById('total-' + t.code);
      if (el) el.value = diff[t.code] !== 0 ? diff[t.code].toFixed(0) : '';
      sourcesSum += (sourceTotals[t.code] || 0);
      entregasSum += (entregaTotals[t.code] || 0);
    });

    // render summary lists
    const paraList = document.getElementById('para-entregar-list');
    const entregadosList = document.getElementById('entregados-list');
    if(paraList && entregadosList){
      paraList.innerHTML = '';
      entregadosList.innerHTML = '';
      TIPOS_CAJA.forEach(t => {
        const s = sourceTotals[t.code] || 0;
        const e = entregaTotals[t.code] || 0;
        const rowS = document.createElement('div'); rowS.textContent = t.raw + ': ' + (s ? s : 0); paraList.appendChild(rowS);
        const rowE = document.createElement('div'); rowE.textContent = t.raw + ': ' + (e ? e : 0); entregadosList.appendChild(rowE);
      });
    }

    // totals
    const elParaTotal = document.getElementById('para-entregar-total');
    const elEntTotal = document.getElementById('entregados-total');
    const elTotalGeneral = document.getElementById('total-general');
    if(elParaTotal) elParaTotal.textContent = sourcesSum.toFixed(0);
    if(elEntTotal) elEntTotal.textContent = entregasSum.toFixed(0);
    if(elTotalGeneral) {
      const tg = sourcesSum - entregasSum;
      elTotalGeneral.textContent = tg.toFixed(0);
      elTotalGeneral.style.color = (Math.abs(tg) < 0.001) ? 'green' : 'red';
    }

    // mark balanced styles on tfoot
    const tfootRow = document.querySelector('#entrega-table tfoot tr');
    const balanced = TIPOS_CAJA.every(t => Math.abs(diff[t.code]) < 0.001);
    if (balanced) tfootRow.classList.add('balanced'); else tfootRow.classList.remove('balanced');
  }

  function addEmptyRow(){ createRow(); recalcTotals(); }

  if (addRowBtn) addRowBtn.addEventListener('click', () => { addEmptyRow(); window.scrollTo(0, document.body.scrollHeight); });

  // initial three special rows: DespachoMatadero, SaldoDeposito-DiaAnt, OtrosTraspasos
  // provide empty values for each tipo so the inputs are created
  function emptyTipoObj(){ const o = {}; TIPOS_CAJA.forEach(t => o[t.code] = ''); return o; }

  // if URL has movimiento id param, load that movement; else if fecha param, load movimientos for that fecha and type ENTREGA
  const params = new URLSearchParams(window.location.search);
  const movimientoId = params.get('id') || params.get('movimiento_id');
  const cargaFecha = params.get('fecha');
  if(movimientoId){
    fetch('../app/controllers/cajas/get_movimiento.php?id='+encodeURIComponent(movimientoId)).then(r=>r.json()).then(resp=>{
      tbody.innerHTML = '';
      if(resp && resp.ok && resp.movimiento){
        const mov = resp.movimiento;
        document.getElementById('f_fecha').value = mov.fecha;
        mov.filas.forEach(f => {
          const data = {};
          // parse nroDesp from obs if present
          let nroDespVal = '';
          let clienteVal = '';
          let obsVal = '';
          if(f.obs){
            const parts = f.obs.split(' - ');
            if(parts[0].startsWith('NroDesp:')){ nroDespVal = parts[0].split(':')[1] || ''; parts.shift(); }
            if(parts.length>0){ clienteVal = parts[0]; parts.shift(); }
            obsVal = parts.join(' - ');
          }
          data.nroDesp = nroDespVal;
          data.cliente = clienteVal;
          data.obs = obsVal;
          data.notad = f.notad;
          data.foto = f.foto;
          data.reccans = f.reccans;
          Object.keys(f.cantidades || {}).forEach(code => data[code] = f.cantidades[code]);
          createRow(data, false);
        });
      }
      if(tbody.querySelectorAll('tr').length === 0){
        createRow(Object.assign({ cliente: 'DespachoMatadero', obs: '' }, emptyTipoObj()), true);
        createRow(Object.assign({ cliente: 'SaldoDeposito-DiaAnt', obs: '' }, emptyTipoObj()), true);
        createRow(Object.assign({ cliente: 'OtrosTraspasos', obs: '' }, emptyTipoObj()), true);
      }
      recalcTotals();
    }).catch(err=>{ console.error(err); /* fallback to defaults below */
      createRow(Object.assign({ cliente: 'DespachoMatadero', obs: '' }, emptyTipoObj()), true);
      createRow(Object.assign({ cliente: 'SaldoDeposito-DiaAnt', obs: '' }, emptyTipoObj()), true);
      createRow(Object.assign({ cliente: 'OtrosTraspasos', obs: '' }, emptyTipoObj()), true);
      recalcTotals();
    });
  } else if(cargaFecha){
    document.getElementById('f_fecha').value = cargaFecha;
    // fetch movimientos ENTREGA
    fetch('../app/controllers/cajas/get_movimientos_por_fecha.php?start='+encodeURIComponent(cargaFecha)+'&end='+encodeURIComponent(cargaFecha)+'&tipo=ENTREGA')
      .then(r=>r.json()).then(resp=>{
        tbody.innerHTML = '';
        if(resp && resp.ok){
          const movs = resp.movimientos || [];
          movs.forEach(mov => {
            mov.filas.forEach(f => {
              const data = {};
              data.nroDesp = '';
              data.cliente = f.obs || '';
              data.obs = '';
              data.notad = f.notad;
              data.foto = f.foto;
              data.reccans = f.reccans;
              Object.keys(f.cantidades || {}).forEach(code => data[code] = f.cantidades[code]);
              createRow(data, false);
            });
          });
        }
        if(tbody.querySelectorAll('tr').length === 0){
          createRow(Object.assign({ cliente: 'DespachoMatadero', obs: '' }, emptyTipoObj()), true);
          createRow(Object.assign({ cliente: 'SaldoDeposito-DiaAnt', obs: '' }, emptyTipoObj()), true);
          createRow(Object.assign({ cliente: 'OtrosTraspasos', obs: '' }, emptyTipoObj()), true);
        }
        recalcTotals();
      }).catch(err=>{
        console.error(err);
          createRow(Object.assign({ cliente: 'DespachoMatadero', obs: '' }, emptyTipoObj()), true);
          createRow(Object.assign({ cliente: 'SaldoDeposito-DiaAnt', obs: '' }, emptyTipoObj()), true);
          createRow(Object.assign({ cliente: 'OtrosTraspasos', obs: '' }, emptyTipoObj()), true);
        recalcTotals();
      });
  }else{
    createRow(Object.assign({ cliente: 'DespachoMatadero', obs: '' }, emptyTipoObj()), true);
    createRow(Object.assign({ cliente: 'SaldoDeposito-DiaAnt', obs: '' }, emptyTipoObj()), true);
    createRow(Object.assign({ cliente: 'OtrosTraspasos', obs: '' }, emptyTipoObj()), true);
    recalcTotals();
  }

  form.addEventListener('submit', (ev)=>{
    ev.preventDefault();
    const submitBtn = form.querySelector('button[type="submit"]');
    if(submitBtn) submitBtn.disabled = true;
    const data = { fecha: document.getElementById('f_fecha').value, chofer: document.getElementById('f_chofer').value, tipo: 'ENTREGA', filas: [] };
    Array.from(tbody.querySelectorAll('tr')).forEach((tr, idx)=>{
      const fila = { nro: idx+1, nroDesp: tr.querySelector('input[name="nroDesp[]"]').value, cliente: tr.querySelector('input[name="cliente[]"]').value, obs: tr.querySelector('input[name="obs[]"]') .value, cantidades: {} };
      TIPOS_CAJA.forEach(t => {
        const sel = tr.querySelector(`input[name="qty[${t.code}][]"]`);
        fila.cantidades[t.code] = sel ? (parseInt(sel.value) || 0) : 0;
      });
      data.filas.push(fila);
    });

    // if cargaFecha is present we want to replace existing registros for that fecha
    if(cargaFecha) data.replace_fecha = true;

    // if we loaded a movimiento by id, include it so the backend updates instead of creating a new one
    if(movimientoId) data.id = movimientoId;

    fetch('../app/controllers/cajas/save_movimiento.php', {
      method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(data)
    }).then(r=>r.json()).then(resp=>{
      if(submitBtn) submitBtn.disabled = false;
      if(resp && resp.ok){
        alert('Movimiento guardado. ID: ' + resp.id);
      }else{
        console.error(resp);
        alert('Error guardando movimiento: ' + (resp.error || 'unknown'));
      }
    }).catch(err=>{
      if(submitBtn) submitBtn.disabled = false;
      console.error(err);
      alert('Error de red al guardar movimiento');
    });
  });

})();
</script>
