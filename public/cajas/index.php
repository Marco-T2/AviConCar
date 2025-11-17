<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-md-8">
          <h4 style="margin:0">CAJAS — Menú</h4>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
          <div class="small-box bg-info">
            <div class="inner">
              <h5>Registro de recojo</h5>
              <p>Crear y registrar operaciones de recojo de cajas</p>
            </div>
            <div class="icon"><i class="fas fa-box"></i></div>
            <a href="./recojo.php" class="small-box-footer">Abrir <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-md-4">
          <div class="small-box bg-success">
            <div class="inner">
              <h5>Registro de entrega</h5>
              <p>Registrar entregas de cajas a clientes</p>
            </div>
            <div class="icon"><i class="fas fa-truck-loading"></i></div>
            <a href="./entrega.php" class="small-box-footer">Abrir <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-md-4">
          <div class="small-box bg-warning">
            <div class="inner">
              <h5>Administración de cajas</h5>
              <p>Crear y gestionar tipos de caja (NEG, VER, AZU, etc.)</p>
            </div>
            <div class="icon"><i class="fas fa-cubes"></i></div>
            <a href="./ajustes.php" class="small-box-footer">Abrir <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><h5 style="margin:0">Registros por Fecha</h5></div>
        <div class="card-body">
            <?php
            // Mostrar movimientos agrupados por fecha, pero listando cada movimiento individual
            try{
              $movs = $pdo->query("SELECT id, fecha, tipo, usuario_id, observacion, totales_json, estado, created_at FROM movimiento_caja ORDER BY fecha DESC, id DESC LIMIT 1000")->fetchAll(PDO::FETCH_ASSOC);
            }catch(Exception $e){ $movs = []; }

            // agrupar por fecha
            $byDate = [];
            foreach($movs as $m){ $byDate[$m['fecha']][] = $m; }
            ?>

            <div class="table-container">
              <table class="table-excel" id="registros-fecha-table">
                <thead>
                  <tr>
                    <th>Fecha</th>
                    <th style="min-width:260px">Entregas</th>
                    <th style="min-width:260px">Recojos</th>
                    <th style="text-align:right">Total</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($byDate as $fecha => $list): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($fecha); ?></td>
                      <td>
                        <?php
                        $i = 1;
                        $hasEntrega = false;
                        foreach($list as $m){ if($m['tipo'] === 'ENTREGA'){ $hasEntrega = true; ?>
                          <span style="display:inline-block;margin-right:6px;margin-bottom:6px">
                            <a class="btn btn-sm btn-warning" href="./entrega.php?id=<?php echo $m['id']; ?>">Entrega<?php echo date('dmy', strtotime($fecha)).'-'.$i; ?></a>
                            <button class="btn btn-sm btn-danger btn-delete-mov" data-id="<?php echo $m['id']; ?>">Del</button>
                          </span>
                        <?php $i++; } } if(!$hasEntrega) echo 'NoSeTieneRegistro'; ?>
                      </td>
                      <td>
                        <?php
                        $j = 1;
                        $hasRecojo = false;
                        foreach($list as $m){ if($m['tipo'] === 'RECOJO'){ $hasRecojo = true; ?>
                          <span style="display:inline-block;margin-right:6px;margin-bottom:6px">
                            <a class="btn btn-sm btn-info" href="./recojo.php?id=<?php echo $m['id']; ?>">Recojo<?php echo date('dmy', strtotime($fecha)).'-'.$j; ?></a>
                            <button class="btn btn-sm btn-danger btn-delete-mov" data-id="<?php echo $m['id']; ?>">Del</button>
                          </span>
                        <?php $j++; } } if(!$hasRecojo) echo 'NoSeTieneRegistro'; ?>
                      </td>
                      <td style="text-align:right"><?php echo count($list); ?></td>
                      <td>
                        <button class="btn btn-sm btn-primary btn-view" data-fecha="<?php echo $fecha; ?>">Ver</button>
                        <button class="btn btn-sm btn-secondary btn-print" data-fecha="<?php echo $fecha; ?>">Imprimir</button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- modal simple para mostrar movimientos del día -->
        <div id="movModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:9999">
          <div style="background:#fff; width:90%; max-width:900px; max-height:80vh; overflow:auto; border-radius:6px; padding:12px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
              <h5 id="movModalTitle">Movimientos</h5>
              <div>
                <button id="movModalClose" class="btn btn-sm">Cerrar</button>
              </div>
            </div>
            <div id="movModalBody">Cargando...</div>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>

<?php
include('../layout/parte2.php');
include('../layout/mensajes.php');
?>

<script>
// handlers para Ver / Imprimir
document.addEventListener('DOMContentLoaded', function(){
  function fetchMovimientosPorFecha(fecha){
    return fetch('../app/controllers/cajas/list_movimientos.php?start='+encodeURIComponent(fecha)+'&end='+encodeURIComponent(fecha))
      .then(r=>r.json());
  }

  function renderMovimientosList(data){
    if(!data || !data.ok) return '<div>Error al obtener movimientos</div>';
    const rows = data.rows || [];
    if(rows.length === 0) return '<div>No hay movimientos para la fecha.</div>';
    let html = '<table style="width:100%;border-collapse:collapse">';
    html += '<thead><tr><th style="border:1px solid #ddd;padding:6px">ID</th><th style="border:1px solid #ddd;padding:6px">Tipo</th><th style="border:1px solid #ddd;padding:6px">Usuario</th><th style="border:1px solid #ddd;padding:6px">Observación</th><th style="border:1px solid #ddd;padding:6px">Totales</th></tr></thead>';
    html += '<tbody>';
    rows.forEach(r => {
      const tot = r.totales ? Object.entries(r.totales).map(e=>e[0]+':'+e[1]).join(' ') : '';
      html += `<tr><td style="border:1px solid #ddd;padding:6px">${r.id}</td><td style="border:1px solid #ddd;padding:6px">${r.tipo}</td><td style="border:1px solid #ddd;padding:6px">${r.usuario_id||''}</td><td style="border:1px solid #ddd;padding:6px">${(r.observacion||'')}</td><td style="border:1px solid #ddd;padding:6px">${tot}</td></tr>`;
    });
    html += '</tbody></table>';
    return html;
  }

  function openPrintWindow(title, bodyHtml){
    const w = window.open('', '_blank');
    if(!w) { alert('Popup bloqueado. Permite popups para imprimir.'); return; }
    w.document.open();
    w.document.write('<!doctype html><html><head><meta charset="utf-8"><title>'+title+'</title>');
    w.document.write('<style>body{font-family:Arial,Helvetica,sans-serif;font-size:12px}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ddd;padding:6px;text-align:left}</style>');
    w.document.write('</head><body>');
    w.document.write('<h3>'+title+'</h3>');
    w.document.write(bodyHtml);
    w.document.write('</body></html>');
    w.document.close();
    w.focus();
    // give browser a moment to render
    setTimeout(()=>{ w.print(); }, 300);
  }

  document.querySelectorAll('.btn-view').forEach(btn => {
    btn.addEventListener('click', function(){
      const fecha = this.getAttribute('data-fecha');
      const modal = document.getElementById('movModal');
      const body = document.getElementById('movModalBody');
      const title = document.getElementById('movModalTitle');
      title.textContent = 'Movimientos — ' + fecha;
      modal.style.display = 'flex';
      body.innerHTML = 'Cargando...';
      fetchMovimientosPorFecha(fecha).then(resp => {
        body.innerHTML = renderMovimientosList(resp);
      }).catch(err => { body.innerHTML = '<div>Error de red</div>'; });
    });
  });

  document.querySelectorAll('.btn-print').forEach(btn => {
    btn.addEventListener('click', function(){
      const fecha = this.getAttribute('data-fecha');
      fetchMovimientosPorFecha(fecha).then(resp => {
        const html = renderMovimientosList(resp);
        openPrintWindow('Movimientos - ' + fecha, html);
      }).catch(err => { alert('Error al obtener movimientos'); });
    });
  });

  // delete by fecha
  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function(){
      const fecha = this.getAttribute('data-fecha');
      if(!confirm('Borrar todos los movimientos de la fecha ' + fecha + '? Esta acción no se puede deshacer.')) return;
      fetch('../app/controllers/cajas/delete_movimientos_by_fecha.php', {
        method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: 'fecha=' + encodeURIComponent(fecha)
      }).then(r=>r.json()).then(resp=>{
        if(resp && resp.ok){
          alert('Eliminados: ' + resp.deleted);
          location.reload();
        }else{
          alert('Error al eliminar: ' + (resp.error || 'unknown'));
        }
      }).catch(err=>{ console.error(err); alert('Error de red al eliminar'); });
    });
  });

  // delete individual movimiento
  document.querySelectorAll('.btn-delete-mov').forEach(btn => {
    btn.addEventListener('click', function(){
      const id = this.getAttribute('data-id');
      if(!confirm('Borrar movimiento ID ' + id + '?')) return;
      fetch('../app/controllers/cajas/delete_movimiento.php', {
        method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: 'id=' + encodeURIComponent(id)
      }).then(r=>r.json()).then(resp=>{
        if(resp && resp.ok){
          alert('Movimiento eliminado');
          location.reload();
        }else{
          alert('Error al eliminar: ' + (resp.error || 'unknown'));
        }
      }).catch(err=>{ console.error(err); alert('Error de red al eliminar'); });
    });
  });

  const modalClose = document.getElementById('movModalClose');
  if(modalClose) modalClose.addEventListener('click', ()=>{ document.getElementById('movModal').style.display = 'none'; });
  // click outside modal to close
  const movModal = document.getElementById('movModal');
  if(movModal) movModal.addEventListener('click', (ev)=>{ if(ev.target === movModal) movModal.style.display = 'none'; });

});
</script>

<style>
  /* Improve spacing and readability for the Registros por Fecha table */
  .table-container { padding: 12px 6px; }
  #registros-fecha-table { width:100%; border-collapse:collapse; }
  #registros-fecha-table th, #registros-fecha-table td { border:1px solid #e3e3e3; padding:8px 10px; }
  #registros-fecha-table thead th { background:#f8f9fb; }
  #registros-fecha-table tbody tr:nth-child(odd){ background:#fbfcfe; }
  #registros-fecha-table tbody tr:hover { background:#f1f7ff; }
  .small-box { min-height:110px; display:flex; align-items:stretch; }
  .small-box .inner { padding:12px; }
  .small-box .icon { font-size:32px; padding:12px; opacity:0.9; }
  .small-box-row { margin-bottom:12px; }
  .btn-sm { margin-right:6px; }
</style>

