<?php
declare(strict_types=1);

// Carga robusta del config (sin depender del cwd)
if (!isset($pdo)) {
    require_once __DIR__ . '/../../config.php'; // => /public/app/config.php
}

$id_subcuenta   = isset($_GET['id_subcuenta']) ? (int)$_GET['id_subcuenta'] : 0;
$gestion_activa = GESTION_ACTIVA;

if ($id_subcuenta <= 0) {
    $listartransaccionessubcuentas = [];
    $name_subCuenta = 'Subcuenta no válida';
    $fecha_inicio = $fecha_fin = date('Y-m-d');
    return;
}

/* -------- Rango de fechas (GET) con fallback a la gestión activa -------- */
$fecha_inicio = $_GET['fecha_inicio'] ?? null;
$fecha_fin    = $_GET['fecha_fin']    ?? null;

if (!$fecha_inicio || !$fecha_fin) {
    $stmtG = $pdo->prepare("SELECT fecha_inicio, fecha_fin FROM tb_gestion WHERE id_gestion = :g LIMIT 1");
    $stmtG->execute([':g' => $gestion_activa]);
    $g = $stmtG->fetch(PDO::FETCH_ASSOC);
    $fecha_inicio = $fecha_inicio ?: ($g['fecha_inicio'] ?? date('Y-01-01'));
    $fecha_fin    = $fecha_fin    ?: ($g['fecha_fin']    ?? date('Y-12-31'));
}

/* ----------------- Nombre de la subcuenta ----------------- */
$stmtS = $pdo->prepare("SELECT name_subCuenta FROM tb_subcuentas WHERE id_subCuenta = :id LIMIT 1");
$stmtS->execute([':id' => $id_subcuenta]);
$name_subCuenta = $stmtS->fetchColumn() ?: 'Subcuenta no encontrada';

/* ----------------- Query del mayor con filtro y saldo corrido ----------------- */
$sql = "
SELECT
  c.id_comprobante,
  c.fecha_comprobante,
  c.hora_comprobante,
  tc.name_tipocomprobante,
  cat.nombre_categoria,
  c.num_comprobante,
  COALESCE(t.descripcion, c.descripcion) AS descripcion,
  t.debe,
  t.haber,
  sc.name_subCuenta,
  /* saldo corrido sin depender de t.id_transaccion */
  SUM(t.debe - t.haber) OVER (
    ORDER BY c.fecha_comprobante, c.hora_comprobante, c.id_comprobante, c.num_comprobante
    ROWS UNBOUNDED PRECEDING
  ) AS saldo_corriente
FROM tb_comprobantes c
JOIN tb_transacciones t
  ON c.id_comprobante = t.id_comprobante
JOIN tb_tipocomprobante tc
  ON c.id_tipocomprobante = tc.id_tipocomprobante
JOIN tb_categoria_comprobante cat
  ON tc.id_categoria = cat.id_categoria
JOIN tb_subcuentas sc
  ON t.id_subCuenta = sc.id_subCuenta
WHERE t.id_subCuenta = :id_subcuenta
  AND c.id_gestion   = :gestion_activa
  AND c.fecha_comprobante BETWEEN :desde AND :hasta
ORDER BY c.fecha_comprobante, c.hora_comprobante, c.id_comprobante, c.num_comprobante
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':id_subcuenta'   => $id_subcuenta,
  ':gestion_activa' => $gestion_activa,
  ':desde'          => $fecha_inicio,
  ':hasta'          => $fecha_fin,
]);

$listartransaccionessubcuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);
