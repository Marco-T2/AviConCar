<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config.php';
session_start();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  $_SESSION['mensaje']='Método no permitido.'; $_SESSION['icono']='warning';
  header('Location: ' . $URL . '/personas/index.php'); exit;
}
if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], (string)$_POST['csrf'])) {
  $_SESSION['mensaje']='Token CSRF inválido.'; $_SESSION['icono']='error';
  header('Location: ' . $URL . '/personas/index.php'); exit;
}

$ids = $_POST['ids'] ?? [];
if (!is_array($ids) || count($ids) === 0) {
  $_SESSION['mensaje']='No seleccionaste personas.'; $_SESSION['icono']='warning';
  header('Location: ' . $URL . '/personas/index.php'); exit;
}

$ids = array_filter(array_map('intval', $ids));
try {
  $pdo->beginTransaction();
  $deleted = 0; $blocked = [];
  $check = $pdo->prepare("SELECT COUNT(*) FROM tb_transacciones WHERE id_persona = ?");
  $del = $pdo->prepare("DELETE FROM tb_personas WHERE id_persona = ? LIMIT 1");
  foreach ($ids as $id) {
    $check->execute([$id]);
    if ((int)$check->fetchColumn() > 0) { $blocked[] = $id; continue; }
    $del->execute([$id]); $deleted++;
  }
  $pdo->commit();
  // Build session message safely (avoid undefined index warnings)
  $parts = [];
  $icon = 'success';
  if ($deleted > 0) $parts[] = "Eliminadas: $deleted.";
  if (count($blocked) > 0) {
    $parts[] = 'No se eliminaron (tienen movimientos): ' . implode(',', $blocked);
    $icon = 'warning';
  }
  if (empty($parts)) {
    $message = 'No se eliminaron personas.';
    $icon = 'warning';
  } else {
    $message = implode(' ', $parts);
  }
  $_SESSION['mensaje'] = $message;
  $_SESSION['icono'] = $icon;
  header('Location: ' . $URL . '/personas/index.php'); exit;
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  $_SESSION['mensaje'] = 'Error al eliminar: ' . $e->getMessage(); $_SESSION['icono']='error';
  header('Location: ' . $URL . '/personas/index.php'); exit;
}
