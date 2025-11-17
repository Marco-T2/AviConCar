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
$tipo = $_GET['tipo'] ?? null;

$where = [];
$params = [];
if($start){ $where[] = 'fecha >= ?'; $params[] = $start; }
if($end){ $where[] = 'fecha <= ?'; $params[] = $end; }
if($tipo){ $where[] = 'tipo = ?'; $params[] = $tipo; }
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$sql = "SELECT id, fecha, tipo, usuario_id, observacion, totales_json, estado, created_at, updated_at FROM movimiento_caja $where_sql ORDER BY fecha DESC, id DESC LIMIT 200";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// simple list: decode totales_json to object
foreach($rows as &$r){
  $r['totales'] = $r['totales_json'] ? json_decode($r['totales_json'], true) : [];
  unset($r['totales_json']);
}

echo json_encode(['ok'=>true,'rows'=>$rows]);

?>