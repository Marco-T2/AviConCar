<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config.php';
session_start();

// Solo POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  $_SESSION['mensaje']='Método no permitido.'; $_SESSION['icono']='warning';
  header('Location: ' . $URL . '/personas/index.php'); exit;
}

// CSRF
if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], (string)$_POST['csrf'])) {
  $_SESSION['mensaje']='Token CSRF inválido.'; $_SESSION['icono']='error';
  header('Location: ' . $URL . '/personas/index.php'); exit;
}

// ID
$id = $_POST['id'] ?? '';
if (!preg_match('/^\d+$/', (string)$id)) {
  $_SESSION['mensaje']='ID inválido.'; $_SESSION['icono']='warning';
  header('Location: ' . $URL . '/personas/index.php'); exit;
}
$id = (int)$id;

try {
  // Existe
  $st = $pdo->prepare("SELECT id_persona FROM tb_personas WHERE id_persona=:id LIMIT 1");
  $st->execute([':id'=>$id]);
  if (!$st->fetch()) {
    $_SESSION['mensaje']='La persona no existe.'; $_SESSION['icono']='warning';
    header('Location: ' . $URL . '/personas/index.php'); exit;
  }

  // Bloquear si tiene movimientos
  $st = $pdo->prepare("SELECT COUNT(*) FROM tb_transacciones WHERE id_persona=:id");
  $st->execute([':id'=>$id]);
  if ((int)$st->fetchColumn() > 0) {
    $_SESSION['mensaje']='No se puede eliminar: tiene movimientos registrados.'; $_SESSION['icono']='error';
    header('Location: ' . $URL . '/personas/index.php'); exit;
  }

  // Eliminar
  $pdo->beginTransaction();
  $del = $pdo->prepare("DELETE FROM tb_personas WHERE id_persona=:id LIMIT 1");
  $del->execute([':id'=>$id]);
  $pdo->commit();

  $_SESSION['mensaje']='Persona eliminada con éxito.'; $_SESSION['icono']='success';
  header('Location: ' . $URL . '/personas/index.php'); exit;

} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  $_SESSION['mensaje'] = 'Error al eliminar: ' . $e->getMessage();
  $_SESSION['icono']   = 'error';
  header('Location: ' . $URL . '/personas/index.php'); exit;
}
