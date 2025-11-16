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

            <?php
            // obtener tipos de caja activos para mostrar columnas dinámicas
            $tipos_caja = [];
            try {
              $stmt = $pdo->query("SELECT codigo, descripcion FROM tipo_caja WHERE activo=1 ORDER BY id ASC");
              $tipos_caja = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
              $tipos_caja = [];
            }
            // normalizar códigos para usarlos como identificadores seguros en HTML/JS
            $tipos_caja_safe = [];
            foreach ($tipos_caja as $t) {
              $raw = $t['codigo'];
              $safe = preg_replace('/[^a-zA-Z0-9_]/', '_', $raw);
              $tipos_caja_safe[] = ['raw'=>$raw, 'code'=>$safe, 'label'=> ($t['descripcion'] ?: $raw) ];
            }
            ?>

            <div class="table-container">
              <table id="cajas-table" class="table-excel">
                <thead>
                  <tr>
                    <th class="nro-cell">Nro</th>
                    <th class="client-cell">Cliente</th>
                    <?php foreach($tipos_caja_safe as $t): ?>
                      <th><?php echo htmlspecialchars($t['raw']); ?></th>
                    <?php endforeach; ?>
                    <th class="obs-cell">Observación</th>
                    <th>NotaD</th>
                    <th>Foto</th>
                    <th>RecCans</th>
                    <th>Acción</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- filas dinámicas (iniciar con 1) -->
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="2" style="text-align:right;font-weight:bold">TOTAL</td>
                    <?php foreach($tipos_caja_safe as $t): ?>
                      <td><input readonly class="total" id="total-<?php echo $t['code']; ?>" value="" placeholder="0"></td>
                    <?php endforeach; ?>
                    <td colspan="5"></td>
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
  /* ensure numeric inputs have consistent small width regardless of column positions */
  .table-excel .qty { width:44px; text-align:right; }
  .table-excel th.nro-cell, .table-excel td.cell-nro { width:48px; max-width:48px; text-align:center; }
  /* client column fixed, observation flexible */
  .table-excel td.client-cell { width:180px; max-width:180px; }
  .table-excel .cliente-input { width:100%; box-sizing:border-box; }
  .table-excel td.obs-cell { width: auto; }
  .table-excel .obs-input { width:100%; box-sizing:border-box; }

  @media (max-width: 768px) {
    .table-excel td.client-cell { width:140px; max-width:140px; }
    .table-excel .obs-input { width:100%; }
  }
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

  .btn-sm { padding:4px 6px; font-size:12px; }
  .icon-btn { width:28px; height:28px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:4px; }
  .icon-btn svg { display:block; color:#fff; }

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

<script>
// tipos de caja disponibles (desde PHP)
const TIPOS_CAJA = <?php echo json_encode($tipos_caja_safe, JSON_HEX_TAG|JSON_HEX_AMP); ?>;
</script>

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
    // build innerHTML dynamically according to TIPOS_CAJA
    let html = '';
    html += `<td class="cell-nro">${rowCount}</td>`;
    html += `<td class="client-cell"><input list="clientes-list" type="text" name="cliente[]" class="cliente-input" value="${escapeHtml(data.cliente || '')}"></td>`;
    // columnas por cada tipo
    TIPOS_CAJA.forEach(t => {
      const code = t.code;
      const val = (data[code] !== undefined) ? data[code] : '';
      html += `<td><input type="number" min="0" name="qty[${code}][]" class="qty qty-${code}" placeholder="0" value="${val}"></td>`;
    });
    html += `<td class="obs-cell"><input type="text" class="obs-input" name="obs[]" value="${escapeHtml(data.obs || '')}"></td>`;
    html += `<td style="text-align:center"><input type="checkbox" name="notad[]" ${data.notad ? 'checked' : ''}></td>`;
    html += `<td style="text-align:center"><input type="checkbox" name="foto[]" ${data.foto ? 'checked' : ''}></td>`;
    html += `<td style="text-align:center"><input type="checkbox" name="reccans[]" ${data.reccans ? 'checked' : ''}></td>`;
    html += `<td style="text-align:center"><button type="button" class="btn btn-sm btn-danger delete-row icon-btn" title="Eliminar fila" aria-label="Eliminar fila">`;
    html += `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 6v14a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 6l1-2h4l1 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
    html += `</button></td>`;
    tr.innerHTML = html;
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

    // delete handler
    const delBtn = tr.querySelector('.delete-row');
    if (delBtn) delBtn.addEventListener('click', () => { tr.remove(); renumberRows(); recalcTotals(); });
  }

  function renumberRows(){
    let i = 1;
    Array.from(tbody.querySelectorAll('tr')).forEach(tr => {
      const cell = tr.querySelector('.cell-nro');
      if (cell) cell.textContent = i++;
    });
    rowCount = i-1;
  }

  function escapeHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }

  function recalcTotals(){
    // build totals object keyed by tipo code
    const totals = {};
    TIPOS_CAJA.forEach(t => totals[t.code] = 0);
    Array.from(tbody.querySelectorAll('tr')).forEach(tr => {
      TIPOS_CAJA.forEach(t => {
        const sel = tr.querySelector(`input[name="qty[${t.code}][]"]`);
        const n = sel ? parseFloat(sel.value || 0) || 0 : 0;
        totals[t.code] += n;
      });
    });
    // update tfoot inputs
    TIPOS_CAJA.forEach(t => {
      const el = document.getElementById('total-' + t.code);
      if (el) el.value = totals[t.code] > 0 ? totals[t.code].toFixed(0) : '';
    });
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
      const fila = { nro: idx+1, cliente: tr.querySelector('input[name="cliente[]"]').value, obs: tr.querySelector('input[name="obs[]"]').value, notad: tr.querySelector('input[name="notad[]"]').checked, foto: tr.querySelector('input[name="foto[]"]').checked, reccans: tr.querySelector('input[name="reccans[]"]').checked, cantidades: {} };
      TIPOS_CAJA.forEach(t => {
        const sel = tr.querySelector(`input[name="qty[${t.code}][]"]`);
        fila.cantidades[t.code] = sel ? (parseInt(sel.value) || 0) : 0;
      });
      data.filas.push(fila);
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
