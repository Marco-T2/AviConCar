<?php
header('Content-Type: application/json; charset=utf-8');
try{
  include_once __DIR__ . '/../../config.php';
}catch(Exception $e){
  echo json_encode(['ok'=>false,'error'=>'config not found']);
  exit;
}

// accept POST or GET
$fecha = $_POST['fecha'] ?? $_GET['fecha'] ?? null;
if(!$fecha){ echo json_encode(['ok'=>false,'error'=>'missing_fecha']); exit; }

try{
  $pdo->beginTransaction();
  // delete movimientos for that date (cascade should remove lines and cantidades)
  $del = $pdo->prepare("DELETE FROM movimiento_caja WHERE fecha = ?");
  $del->execute([$fecha]);
  $pdo->commit();
  echo json_encode(['ok'=>true,'deleted'=> $del->rowCount() ]);
}catch(Exception $e){
  if($pdo && $pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
?>