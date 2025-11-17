<?php
header('Content-Type: application/json; charset=utf-8');
try{
  include_once __DIR__ . '/../../config.php';
}catch(Exception $e){
  echo json_encode(['ok'=>false,'error'=>'config not found']);
  exit;
}

$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;
$tipo = $_GET['tipo'] ?? null; // optional: RECOJO or ENTREGA
if(!$start) { echo json_encode(['ok'=>false,'error'=>'missing_start']); exit; }
if(!$end) $end = $start;

$params = [$start, $end];
$where = 'fecha BETWEEN ? AND ?';
if($tipo){ $where .= ' AND tipo = ?'; $params[] = $tipo; }

// get movimientos in date range
$sql = "SELECT id, fecha, tipo, usuario_id, observacion, totales_json, estado, created_at, updated_at FROM movimiento_caja WHERE $where ORDER BY fecha ASC, id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// prepare statement to get lines and cantidades
$linesStmt = $pdo->prepare("SELECT id, nro_linea, persona_id, obs, foto_flag, notad_flag, reccans_flag FROM movimiento_caja_linea WHERE movimiento_id = ? ORDER BY nro_linea ASC, id ASC");
$cantStmt = $pdo->prepare("SELECT mc.cantidad, tc.codigo FROM movimiento_caja_cantidad mc JOIN tipo_caja tc ON mc.tipo_caja_id = tc.id WHERE mc.linea_id = ?");

$result = [];
foreach($movs as $m){
  $linesStmt->execute([$m['id']]);
  $lines = $linesStmt->fetchAll(PDO::FETCH_ASSOC);
  $filas = [];
  foreach($lines as $ln){
    $cantStmt->execute([$ln['id']]);
    $cants = [];
    foreach($cantStmt->fetchAll(PDO::FETCH_ASSOC) as $c){
      $cants[$c['codigo']] = (int)$c['cantidad'];
    }
    $filas[] = [
      'id' => (int)$ln['id'],
      'nro' => (int)$ln['nro_linea'],
      'persona_id' => $ln['persona_id'] ? (int)$ln['persona_id'] : null,
      'obs' => $ln['obs'],
      'foto' => (int)$ln['foto_flag'],
      'notad' => (int)$ln['notad_flag'],
      'reccans' => (int)$ln['reccans_flag'],
      'cantidades' => $cants
    ];
  }
  $totales = $m['totales_json'] ? json_decode($m['totales_json'], true) : [];
  $result[] = [
    'id' => (int)$m['id'],
    'fecha' => $m['fecha'],
    'tipo' => $m['tipo'],
    'usuario_id' => $m['usuario_id'] ? (int)$m['usuario_id'] : null,
    'observacion' => $m['observacion'],
    'totales' => $totales,
    'estado' => $m['estado'],
    'filas' => $filas
  ];
}

echo json_encode(['ok'=>true,'movimientos'=>$result]);

?>