<?php
include_once(__DIR__ . '/../../config.php');
header('Content-Type: application/json; charset=utf-8');
try {
  // read POST
  $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
  $codigo = isset($_POST['codigo']) ? trim($_POST['codigo']) : '';
  $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
  $color = isset($_POST['color_referencia']) ? trim($_POST['color_referencia']) : '';
  $activo = isset($_POST['activo']) ? 1 : 0;

  if ($codigo === '') throw new Exception('Código requerido');

  if ($id) {
    $sql = "UPDATE tipo_caja SET codigo = :codigo, descripcion = :descripcion, color_referencia = :color, activo = :activo WHERE id = :id";
    $st = $pdo->prepare($sql);
    $st->execute([':codigo'=>$codigo, ':descripcion'=>$descripcion, ':color'=>$color, ':activo'=>$activo, ':id'=>$id]);
  } else {
    $sql = "INSERT INTO tipo_caja (codigo, descripcion, color_referencia, activo) VALUES (:codigo, :descripcion, :color, :activo)";
    $st = $pdo->prepare($sql);
    $st->execute([':codigo'=>$codigo, ':descripcion'=>$descripcion, ':color'=>$color, ':activo'=>$activo]);
    $id = $pdo->lastInsertId();
  }

  echo json_encode(['success'=>true, 'id'=>$id]);
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
