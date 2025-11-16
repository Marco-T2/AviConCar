<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

// CUADRE DE CAJAS RECOGIDAS (form)
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-md-8">
          <h4 style="margin:0">CUADRE DE CAJAS RECOGIDOS</h4>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-body">
          <form id="recojoForm">
            <div class="form-grid">
              <div class="form-item"><label>Fecha: <input type="date" id="f_fecha" name="fecha" required></label></div>
              <div class="form-item"><label>Encargado: <input type="text" id="f_chofer" name="chofer" class="input-wide"></label></div>
            </div>
            <div class="form-actions"><button type="submit" class="btn btn-success">Guardar (console.log)</button></div>
            <p class="note">El cuadre de pollo solo se puede registrar o editar en el día.</p>

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
              <div></div>
              <div><button type="button" id="addRowBtn" class="btn btn-primary">+ Agregar fila</button></div>
            </div>

            <div class="table-container">
              <table id="cajas-table" class="table-excel">
                <thead>
                  <tr>
                    <th>Nro</th>
                    <th>Cliente</th>
                    <th>NEG</th>
                    <th>VER</th>
                    <th>VER-OR</th>
                    <th>AZU</th>
                    <th>ROJ</th>
                    <th>Observación</th>
                    <th>NotaD</th>
                    <th>Foto</th>
                    <th>RecCans</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- filas dinámicas (iniciar con 1) -->
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="2" style="text-align:right;font-weight:bold">TOTAL</td>
                    <td><input readonly class="total" id="total-neg" value="" placeholder="0"></td>
                    <td><input readonly class="total" id="total-ver" value="" placeholder="0"></td>
                    <td><input readonly class="total" id="total-veror" value="" placeholder="0"></td>
                    <td><input readonly class="total" id="total-azu" value="" placeholder="0"></td>
                    <td><input readonly class="total" id="total-roj" value="" placeholder="0"></td>
                    <td colspan="4"></td>
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
  /* Simple styles to look like a spreadsheet */
  .table-excel { width:100%; border-collapse: collapse; font-size:12px; }
  .table-excel thead th, .table-excel tfoot td, .table-excel td { border: 1px solid #ddd; padding: 6px; }
  .table-excel thead th { background:#f3f3f3; text-align:left; }
  .table-excel tbody td input[type="text"], .table-excel tbody td input[type="number"] { width:100%; box-sizing:border-box; border: none; padding:4px; font-size:12px; }
  .table-excel tbody td input[type="number"] { text-align:right; }
  .table-excel tbody td input[type="checkbox"] { transform:scale(1.1); }
  .table-excel tbody td input:focus { outline: 1px solid #6ea8fe; }
  .table-excel tfoot input.total { width:100%; border:none; background:transparent; font-weight:bold; text-align:right; font-size:12px; }
  /* Buttons small */
  .btn { padding:6px 10px; border-radius:4px; border:1px solid #2f6f9f; background:#2f6f9f; color:#fff; cursor:pointer }
  .btn-primary { background:#007bff; border-color:#007bff }
  .btn-success { background:#28a745; border-color:#28a745 }

  /* container to center the table and limit width */
  .table-container { max-width: 920px; margin: 0 auto; overflow:auto; }

  /* narrow numeric columns (3..7) to fit 3-digit numbers */
  .table-excel tbody td:nth-child(3),
  .table-excel tbody td:nth-child(4),
  .table-excel tbody td:nth-child(5),
  .table-excel tbody td:nth-child(6),
  .table-excel tbody td:nth-child(7) { width: 48px; }

  .table-excel tbody td:nth-child(3) input,
  .table-excel tbody td:nth-child(4) input,
  .table-excel tbody td:nth-child(5) input,
  .table-excel tbody td:nth-child(6) input,
  .table-excel tbody td:nth-child(7) input { width:44px; text-align:right; font-size:12px; }

  /* make observation field a bit wider */
  .table-excel tbody td:nth-child(8) input { min-width:220px; }

</style>

<datalist id="clientes-list">
  <option value="Ajuste-Cajas"></option>
  <?php
  // cargar clientes desde la base de datos usando el listado existente
  include_once('../app/controllers/personas/listado_personas.php');
  if (!empty($personas_datos)){
    foreach($personas_datos as $p){
      $nombre = htmlspecialchars($p['name_persona']);
      echo "  <option value=\"{$nombre}\"></option>\n";
    }
  }
  ?>
</datalist>

<style>
  /* form grid for top inputs: center Fecha y Encargado */
  .form-grid { display:flex; justify-content:center; gap:12px; align-items:center; margin-bottom:8px; flex-wrap:wrap; }
  .form-grid .form-item { display:flex; align-items:center; }
  .form-actions { width:100%; display:flex; justify-content:flex-end; margin-bottom:8px; }
  .form-grid input.input-wide { width:320px; box-sizing:border-box; }
  .form-grid label { font-weight:600; margin-right:6px; }
  .note { margin:6px 0 12px 0; color:#856404; background:#fff3cd; padding:8px 10px; border-radius:4px; }

  /* make Cliente column similar width as Observación */
  .table-excel tbody td:nth-child(2) input { min-width:180px; }
</style>

<style>
  /* highlight row when it's an Ajuste */
  .row-ajuste { background: #fff3cd; }
</style>

<style>
  /* Responsive tweaks for mobile devices */
  @media (max-width: 768px) {
    .table-container { max-width: 100%; padding: 0 8px; }
    .table-excel { font-size:11px; display:block; overflow-x:auto; white-space:nowrap; }
    .table-excel thead th, .table-excel td { padding:4px; }
    .table-excel tbody td input[type="text"], .table-excel tbody td input[type="number"] { font-size:11px; padding:6px 4px; }
    .table-excel tfoot input.total { font-size:12px; }

    /* Stack header inputs vertically and center */
    .form-grid { flex-direction:column; align-items:center; gap:6px; }
    .form-grid .form-item { width:100%; display:flex; justify-content:center; }
    .form-grid input.input-wide { width:100%; max-width:360px; }
    .form-actions { justify-content:center; padding:0 8px; }

    /* Buttons full width for easier tapping */
    #addRowBtn, .btn-success { width:100%; box-sizing:border-box; }

    /* Numeric columns slightly narrower on small screens */
    .table-excel tbody td:nth-child(3),
    .table-excel tbody td:nth-child(4),
    .table-excel tbody td:nth-child(5),
    .table-excel tbody td:nth-child(6),
    .table-excel tbody td:nth-child(7) { width: 40px; }
    .table-excel tbody td:nth-child(3) input,
    .table-excel tbody td:nth-child(4) input,
    .table-excel tbody td:nth-child(5) input,
    .table-excel tbody td:nth-child(6) input,
    .table-excel tbody td:nth-child(7) input { width:36px; }

    /* Cliente and Observación comfortable widths */
    .table-excel tbody td:nth-child(2) input { min-width:140px; }
    .table-excel tbody td:nth-child(8) input { min-width:160px; }
  }
  @media (max-width: 420px) {
    .table-excel { font-size:10.5px; }
    .table-excel tbody td input { font-size:10.5px; }
    .form-grid input.input-wide { max-width:280px; }
  }
</style>

<script>
// Encapsular comportamiento
(() => {
  const tbody = document.querySelector('#cajas-table tbody');
  const addRowBtn = document.getElementById('addRowBtn');
  const form = document.getElementById('recojoForm');

  let rowCount = 0;

  function createRow(data = {}){
    rowCount++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="cell-nro">${rowCount}</td>
      <td><input list="clientes-list" type="text" name="cliente[]" class="cliente-input" value="${escapeHtml(data.cliente || '')}"></td>
      <td><input type="number" min="0" name="neg[]" class="qty" placeholder="0" value="${data.neg || ''}"></td>
      <td><input type="number" min="0" name="ver[]" class="qty" placeholder="0" value="${data.ver || ''}"></td>
      <td><input type="number" min="0" name="veror[]" class="qty" placeholder="0" value="${data.veror || ''}"></td>
      <td><input type="number" min="0" name="azu[]" class="qty" placeholder="0" value="${data.azu || ''}"></td>
      <td><input type="number" min="0" name="roj[]" class="qty" placeholder="0" value="${data.roj || ''}"></td>
      <td><input type="text" name="obs[]" value="${escapeHtml(data.obs || '')}"></td>
      <td style="text-align:center"><input type="checkbox" name="notad[]" ${data.notad ? 'checked' : ''}></td>
      <td style="text-align:center"><input type="checkbox" name="foto[]" ${data.foto ? 'checked' : ''}></td>
      <td style="text-align:center"><input type="checkbox" name="reccans[]" ${data.reccans ? 'checked' : ''}></td>
    `;
    tbody.appendChild(tr);

    // attach listener to qty inputs
    tr.querySelectorAll('.qty').forEach(el => el.addEventListener('input', recalcTotals));

    // cliente input: toggle ajuste styling
    const clienteInput = tr.querySelector('.cliente-input');
    const checkAjuste = () => {
      const v = (clienteInput.value || '').trim();
      tr.classList.toggle('row-ajuste', v === 'Ajuste-Cajas');
    };
    clienteInput.addEventListener('input', checkAjuste);
    // run once for prefilled data
    checkAjuste();
  }

  function escapeHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }

  function recalcTotals(){
    const totals = { neg:0, ver:0, veror:0, azu:0, roj:0 };
    Array.from(tbody.querySelectorAll('tr')).forEach(tr => {
      const nneg = parseFloat(tr.querySelector('input[name="neg[]"]').value || 0) || 0;
      const nver = parseFloat(tr.querySelector('input[name="ver[]"]').value || 0) || 0;
      const nveror = parseFloat(tr.querySelector('input[name="veror[]"]').value || 0) || 0;
      const nazu = parseFloat(tr.querySelector('input[name="azu[]"]').value || 0) || 0;
      const nroj = parseFloat(tr.querySelector('input[name="roj[]"]').value || 0) || 0;
      totals.neg += nneg; totals.ver += nver; totals.veror += nveror; totals.azu += nazu; totals.roj += nroj;
    });
    document.getElementById('total-neg').value = totals.neg > 0 ? totals.neg.toFixed(0) : '';
    document.getElementById('total-ver').value = totals.ver > 0 ? totals.ver.toFixed(0) : '';
    document.getElementById('total-veror').value = totals.veror > 0 ? totals.veror.toFixed(0) : '';
    document.getElementById('total-azu').value = totals.azu > 0 ? totals.azu.toFixed(0) : '';
    document.getElementById('total-roj').value = totals.roj > 0 ? totals.roj.toFixed(0) : '';
  }

  function addEmptyRow(){ createRow(); recalcTotals(); }

  if (addRowBtn) addRowBtn.addEventListener('click', () => { addEmptyRow(); window.scrollTo(0, document.body.scrollHeight); });

  // inicializar 1 fila
  createRow();
  recalcTotals();

  // submit: recoger datos y enviar (ahora console.log)
  form.addEventListener('submit', (ev)=>{
    ev.preventDefault();
    const data = {
      fecha: document.getElementById('f_fecha').value,
      chofer: document.getElementById('f_chofer').value,
      tipo: 'RECOJO',
      filas: []
    };
    Array.from(tbody.querySelectorAll('tr')).forEach((tr, idx)=>{
      data.filas.push({
        nro: idx+1,
        cliente: tr.querySelector('input[name="cliente[]"]').value,
        neg: parseInt(tr.querySelector('input[name="neg[]"]').value) || 0,
        ver: parseInt(tr.querySelector('input[name="ver[]"]').value) || 0,
        veror: parseInt(tr.querySelector('input[name="veror[]"]').value) || 0,
        azu: parseInt(tr.querySelector('input[name="azu[]"]').value) || 0,
        roj: parseInt(tr.querySelector('input[name="roj[]"]').value) || 0,
        obs: tr.querySelector('input[name="obs[]"]').value,
        notad: tr.querySelector('input[name="notad[]"]').checked,
        foto: tr.querySelector('input[name="foto[]"]').checked,
        reccans: tr.querySelector('input[name="reccans[]"]').checked
      });
    });

    console.log('RECOJO DE CAJAS form data:', data);

    // Ejemplo de envío con fetch (API REST). Por ahora lo dejamos comentado.
    /*
    fetch('/api/cajas/recojo', {
      method: 'POST', headers: {'Content-Type':'application/json'},
      body: JSON.stringify(data)
    }).then(r=>r.json()).then(resp=>console.log(resp)).catch(err=>console.error(err));
    */
  });

})();
</script>
