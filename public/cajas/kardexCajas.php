<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

// Build per-client aggregate of cajas: entregadas vs recogidas
// We'll aggregate across all movimientos (no date filter).
try{
  $sql = "
    SELECT m.tipo, COALESCE(ml.persona_id, 0) AS persona_id, p.name_persona, ml.obs, SUM(mc.cantidad) AS cantidad
    FROM movimiento_caja m
    JOIN movimiento_caja_linea ml ON ml.movimiento_id = m.id
    JOIN movimiento_caja_cantidad mc ON mc.linea_id = ml.id
    LEFT JOIN tb_personas p ON p.id_persona = ml.persona_id
    GROUP BY m.tipo, persona_id, ml.obs
  ";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $e){
  $rows = [];
}

// aggregate per client key (persona_id if present else obs first token)
$map = [];
foreach($rows as $r){
  $tipo = strtoupper(trim($r['tipo'] ?? ''));
  $pid = (int)($r['persona_id'] ?? 0);
  $obs = trim($r['obs'] ?? '');
  // remove possible NroDesp: prefix
  if(strpos($obs, 'NroDesp:') === 0){
    $parts = explode(' - ', $obs);
    array_shift($parts);
    $obs = trim($parts[0] ?? '');
  }
  $key = $pid > 0 ? 'P'.$pid : ('O'.($obs !== '' ? $obs : 'SIN_CLIENTE'));
  if(!isset($map[$key])){ $map[$key] = ['persona_id'=>$pid, 'nombre'=> $pid>0 ? ($r['name_persona'] ?: 'Cliente '.$pid) : ($obs ?: 'Sin cliente'), 'entregadas'=>0, 'recogidas'=>0]; }
  $cantidad = (int)($r['cantidad'] ?? 0);
  if($tipo === 'ENTREGA') $map[$key]['entregadas'] += $cantidad;
  else if($tipo === 'RECOJO') $map[$key]['recogidas'] += $cantidad;
}

// compute saldo and sort by saldo desc
$list = [];
foreach($map as $k => $v){
  $v['saldo'] = $v['entregadas'] - $v['recogidas'];
  $list[] = $v;
}
usort($list, function($a,$b){ return $b['saldo'] <=> $a['saldo']; });
?>

<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Kardex Cajas — Saldo por cliente (Entregadas - Recogidas)</h3>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped">
                  <thead>
                    <tr>
                      <th style="width:40px">#</th>
                      <th>Cliente</th>
                      <th style="text-align:right;width:120px">Entregadas</th>
                      <th style="text-align:right;width:120px">Recogidas</th>
                      <th style="text-align:right;width:120px">Saldo</th>
                      <th style="width:90px;text-align:center">Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(empty($list)): ?>
                      <tr><td colspan="6" class="text-center text-muted">Sin datos para mostrar.</td></tr>
                    <?php else: $i=0; foreach($list as $row): $i++; $neg = $row['saldo'] < 0; ?>
                      <tr>
                        <td><?= $i ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td style="text-align:right"><?= number_format($row['entregadas'],0) ?></td>
                        <td style="text-align:right"><?= number_format($row['recogidas'],0) ?></td>
                        <td style="text-align:right" class="<?= $neg ? 'text-danger font-weight-bold' : '' ?>"><?= number_format($row['saldo'],0) ?></td>
                        <td style="text-align:center">
                          <?php if($row['persona_id'] && $row['persona_id']>0): ?>
                            <a class="btn btn-sm btn-primary" href="<?= $URL ?>/kardex/kardex.php?id=<?= $row['persona_id'] ?>">Ver</a>
                          <?php else: ?>
                            <button class="btn btn-sm btn-secondary" disabled>--</button>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include('../layout/parte2.php'); include('../layout/mensajes.php'); ?>

