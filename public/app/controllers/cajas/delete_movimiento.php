<?php
header('Content-Type: application/json; charset=utf-8');
try{
  include_once __DIR__ . '/../../config.php';
}catch(Exception $e){
  echo json_encode(['ok'=>false,'error'=>'config not found']);
  exit;
}

$id = $_POST['id'] ?? $_GET['id'] ?? null;
if(!$id){ echo json_encode(['ok'=>false,'error'=>'missing_id']); exit; }
$id = (int)$id;
try{
  $stmt = $pdo->prepare('DELETE FROM movimiento_caja WHERE id = ?');
  $stmt->execute([$id]);
  echo json_encode(['ok'=>true,'deleted'=>$stmt->rowCount()]);
}catch(Exception $e){
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
?>