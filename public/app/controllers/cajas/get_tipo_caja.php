<?php
include_once(__DIR__ . '/../../config.php');
header('Content-Type: application/json; charset=utf-8');
try {
  $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if (!$id) throw new Exception('ID inválido');
  $sql = "SELECT id, codigo, descripcion, color_referencia, activo FROM tipo_caja WHERE id = :id LIMIT 1";
  $st = $pdo->prepare($sql);
  $st->execute([':id'=>$id]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  if (!$row) throw new Exception('No encontrado');
  echo json_encode(['success'=>true, 'data'=>$row]);
} catch (Exception $e) {
  http_response_code(404);
  echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
