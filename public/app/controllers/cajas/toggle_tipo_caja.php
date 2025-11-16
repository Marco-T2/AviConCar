<?php
include_once(__DIR__ . '/../../config.php');
header('Content-Type: application/json; charset=utf-8');
try {
  $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  if (!$id) throw new Exception('ID inválido');
  $sql = "SELECT activo FROM tipo_caja WHERE id = :id";
  $st = $pdo->prepare($sql);
  $st->execute([':id'=>$id]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  if (!$row) throw new Exception('No encontrado');
  $new = $row['activo'] ? 0 : 1;
  $sql = "UPDATE tipo_caja SET activo = :activo WHERE id = :id";
  $st = $pdo->prepare($sql);
  $st->execute([':activo'=>$new, ':id'=>$id]);
  echo json_encode(['success'=>true, 'activo'=>$new]);
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
