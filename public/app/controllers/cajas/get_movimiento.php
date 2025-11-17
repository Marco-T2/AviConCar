<?php
header('Content-Type: application/json; charset=utf-8');

try{
  include_once __DIR__ . '/../../config.php';
}catch(Exception $e){
  echo json_encode(['ok'=>false,'error'=>'config not found']);
  exit;
}

if(!isset($_GET['id'])){
  echo json_encode(['ok'=>false,'error'=>'missing_id']);
  exit;
}

$id = (int)$_GET['id'];

// get header
$stmt = $pdo->prepare("SELECT id, fecha, tipo, usuario_id, observacion, totales_json, estado, created_at, updated_at FROM movimiento_caja WHERE id = ?");
$stmt->execute([$id]);
$mov = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$mov){
  echo json_encode(['ok'=>false,'error'=>'not_found']);
  exit;
}

// get lines
$linesStmt = $pdo->prepare("SELECT id, nro_linea, persona_id, obs, foto_flag, notad_flag, reccans_flag FROM movimiento_caja_linea WHERE movimiento_id = ? ORDER BY nro_linea ASC,id ASC");
$linesStmt->execute([$id]);
$lines = $linesStmt->fetchAll(PDO::FETCH_ASSOC);

// for each line get cantidades joined with tipo_caja to return codigo
$cantStmt = $pdo->prepare("SELECT mc.cantidad, tc.codigo FROM movimiento_caja_cantidad mc JOIN tipo_caja tc ON mc.tipo_caja_id = tc.id WHERE mc.linea_id = ?");

$filas = [];
foreach($lines as $ln){
  $cantidades = [];
  $cantStmt->execute([$ln['id']]);
  foreach($cantStmt->fetchAll(PDO::FETCH_ASSOC) as $c){
    $cantidades[$c['codigo']] = (int)$c['cantidad'];
  }
  $filas[] = [
    'id' => (int)$ln['id'],
    'nro' => (int)$ln['nro_linea'],
    'persona_id' => $ln['persona_id'] ? (int)$ln['persona_id'] : null,
    'obs' => $ln['obs'],
    'foto' => (int)$ln['foto_flag'],
    'notad' => (int)$ln['notad_flag'],
    'reccans' => (int)$ln['reccans_flag'],
    'cantidades' => $cantidades
  ];
}

// decode totales_json if present
$totales = [];
if(!empty($mov['totales_json'])){
  $dec = json_decode($mov['totales_json'], true);
  if(is_array($dec)) $totales = $dec;
}

echo json_encode(['ok'=>true,'movimiento'=>[
  'id'=>(int)$mov['id'],
  'fecha'=>$mov['fecha'],
  'tipo'=>$mov['tipo'],
  'usuario_id'=> $mov['usuario_id'] ? (int)$mov['usuario_id'] : null,
  'observacion'=>$mov['observacion'],
  'totales'=>$totales,
  'estado'=>$mov['estado'],
  'created_at'=>$mov['created_at'],
  'updated_at'=>$mov['updated_at'],
  'filas'=>$filas
]]);

?>