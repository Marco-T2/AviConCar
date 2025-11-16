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

            <div class="table-container">
              <table id="entrega-table" class="table-excel">
                <thead>
                  <tr>
                    <th>Nro</th>
                    <th>NroDespacho</th>
                    <th>Cliente</th>
                    <th>NEG</th>
                    <th>VER</th>
                    <th>VER-OR</th>
                    <th>AZU</th>
                    <th>ROJ</th>
                    <th>Observación</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- filas dinámicas (inician con 2 filas especiales) -->
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="3" style="text-align:right;font-weight:bold">TOTAL</td>
                    <td><input readonly class="total" id="total-neg" value="" placeholder="0"></td>
                    <td><input readonly class="total" id="total-ver" value="" placeholder="0"></td>
                    <td><input readonly class="total" id="total-veror" value="" placeholder="0"></td>
                    <td><input readonly class="total" id="total-azu" value="" placeholder="0"></td>
                    <td><input readonly class="total" id="total-roj" value="" placeholder="0"></td>
                    <td></td>
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

  .btn { padding:6px 10px; border-radius:4px; border:1px solid #2f6f9f; background:#2f6f9f; color:#fff; cursor:pointer }
  .btn-primary { background:#007bff; border-color:#007bff }
  .btn-success { background:#28a745; border-color:#28a745 }

  .table-container { max-width: 920px; margin: 0 auto; overflow:auto; }
  .table-excel tbody td:nth-child(2) { width:80px; }
  .table-excel tbody td:nth-child(3) input { min-width:150px; }
  .table-excel tbody td:nth-child(4), .table-excel tbody td:nth-child(5), .table-excel tbody td:nth-child(6), .table-excel tbody td:nth-child(7), .table-excel tbody td:nth-child(8) { width:48px }
  .table-excel tbody td:nth-child(4) input, .table-excel tbody td:nth-child(5) input, .table-excel tbody td:nth-child(6) input, .table-excel tbody td:nth-child(7) input, .table-excel tbody td:nth-child(8) input { width:44px; text-align:right; }
  .table-excel tbody td:nth-child(9) input { min-width:260px; }

  /* special row styles */
  .row-ajuste { background: #fff3cd; }
  .row-despacho { background: #cfe8ff; }
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
  <option value="Ajuste-Cajas"></option>
  <option value="DespachoMatadero"></option>
  <option value="SaldoDeposito"></option>
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
    tr.innerHTML = `
      <td class="cell-nro"></td>
      <td><input type="text" name="nroDesp[]" value="${escapeHtml(data.nroDesp || '')}"></td>
      <td><input list="clientes-list" type="text" name="cliente[]" class="cliente-input" value="${escapeHtml(data.cliente || '')}"></td>
      <td><input type="number" min="0" name="neg[]" class="qty" placeholder="0" value="${data.neg || ''}"></td>
      <td><input type="number" min="0" name="ver[]" class="qty" placeholder="0" value="${data.ver || ''}"></td>
      <td><input type="number" min="0" name="veror[]" class="qty" placeholder="0" value="${data.veror || ''}"></td>
      <td><input type="number" min="0" name="azu[]" class="qty" placeholder="0" value="${data.azu || ''}"></td>
      <td><input type="number" min="0" name="roj[]" class="qty" placeholder="0" value="${data.roj || ''}"></td>
      <td><input type="text" name="obs[]" value="${escapeHtml(data.obs || '')}"></td>
    `;

    if (fixed) tr.classList.add('fixed-source');

    // append at end (initial fixed rows are created first so they remain on top)
    tbody.appendChild(tr);

    // listeners
    tr.querySelectorAll('.qty').forEach(el => el.addEventListener('input', recalcTotals));
    const clienteInput = tr.querySelector('.cliente-input');
    const checkAjuste = () => {
      const v = (clienteInput.value || '').trim();
      tr.classList.toggle('row-ajuste', v === 'Ajuste-Cajas');
      tr.classList.toggle('row-despacho', v === 'DespachoMatadero' || v === 'SaldoDeposito');
    };
    clienteInput.addEventListener('input', checkAjuste);
    checkAjuste();

    renumberRows();
  }

  function escapeHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }

  function recalcTotals(){
    const sourceTotals = { neg:0, ver:0, veror:0, azu:0, roj:0 };
    const entregaTotals = { neg:0, ver:0, veror:0, azu:0, roj:0 };

    Array.from(tbody.querySelectorAll('tr')).forEach(tr => {
      const isSource = tr.classList.contains('fixed-source');
      const nneg = parseFloat(tr.querySelector('input[name="neg[]"]').value || 0) || 0;
      const nver = parseFloat(tr.querySelector('input[name="ver[]"]').value || 0) || 0;
      const nveror = parseFloat(tr.querySelector('input[name="veror[]"]').value || 0) || 0;
      const nazu = parseFloat(tr.querySelector('input[name="azu[]"]').value || 0) || 0;
      const nroj = parseFloat(tr.querySelector('input[name="roj[]"]').value || 0) || 0;
      if (isSource) {
        sourceTotals.neg += nneg; sourceTotals.ver += nver; sourceTotals.veror += nveror; sourceTotals.azu += nazu; sourceTotals.roj += nroj;
      } else {
        entregaTotals.neg += nneg; entregaTotals.ver += nver; entregaTotals.veror += nveror; entregaTotals.azu += nazu; entregaTotals.roj += nroj;
      }
    });

    // difference: source - entrega (should be zero when balanced)
    const diff = {
      neg: sourceTotals.neg - entregaTotals.neg,
      ver: sourceTotals.ver - entregaTotals.ver,
      veror: sourceTotals.veror - entregaTotals.veror,
      azu: sourceTotals.azu - entregaTotals.azu,
      roj: sourceTotals.roj - entregaTotals.roj
    };

    document.getElementById('total-neg').value = diff.neg !== 0 ? diff.neg.toFixed(0) : '';
    document.getElementById('total-ver').value = diff.ver !== 0 ? diff.ver.toFixed(0) : '';
    document.getElementById('total-veror').value = diff.veror !== 0 ? diff.veror.toFixed(0) : '';
    document.getElementById('total-azu').value = diff.azu !== 0 ? diff.azu.toFixed(0) : '';
    document.getElementById('total-roj').value = diff.roj !== 0 ? diff.roj.toFixed(0) : '';

    // visual cue when all diffs are zero
    const tfootRow = document.querySelector('#entrega-table tfoot tr');
    const balanced = Object.values(diff).every(v => Math.abs(v) < 0.001);
    if (balanced) tfootRow.classList.add('balanced'); else tfootRow.classList.remove('balanced');
  }

  function addEmptyRow(){ createRow(); recalcTotals(); }

  if (addRowBtn) addRowBtn.addEventListener('click', () => { addEmptyRow(); window.scrollTo(0, document.body.scrollHeight); });

  // initial three special rows: DespachoMatadero, SaldoDeposito, Ajuste-Cajas
  createRow({ cliente: 'DespachoMatadero', obs: '' }, true);
  createRow({ cliente: 'SaldoDeposito', obs: '' }, true);
  createRow({ cliente: 'Ajuste-Cajas', obs: '' }, true);
  recalcTotals();

  form.addEventListener('submit', (ev)=>{
    ev.preventDefault();
    const data = { fecha: document.getElementById('f_fecha').value, chofer: document.getElementById('f_chofer').value, filas: [] };
    Array.from(tbody.querySelectorAll('tr')).forEach((tr, idx)=>{
      data.filas.push({
        nro: idx+1,
        nroDesp: tr.querySelector('input[name="nroDesp[]"]').value,
        cliente: tr.querySelector('input[name="cliente[]"]').value,
        neg: parseInt(tr.querySelector('input[name="neg[]"]').value) || 0,
        ver: parseInt(tr.querySelector('input[name="ver[]"]').value) || 0,
        veror: parseInt(tr.querySelector('input[name="veror[]"]').value) || 0,
        azu: parseInt(tr.querySelector('input[name="azu[]"]').value) || 0,
        roj: parseInt(tr.querySelector('input[name="roj[]"]').value) || 0,
        obs: tr.querySelector('input[name="obs[]"]').value
      });
    });
    console.log('ENTREGA DE CAJAS form data:', data);
  });

})();
</script>
