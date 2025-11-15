<?php
// C:\web\stack\ct\public\app\controllers\informes\listarkardexfiltro.php
declare(strict_types=1);
require_once __DIR__ . '/../../config.php';

header('Content-Type: text/html; charset=utf-8');

$g = (int) GESTION_ACTIVA;

// --------- INPUT ----------
$fecha_inicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : null;
$fecha_fin    = isset($_POST['fecha_fin'])    ? trim($_POST['fecha_fin'])    : null;
$id_subcuenta = isset($_POST['id_subcuenta']) ? (int)$_POST['id_subcuenta']  : 0;
$id_persona   = isset($_POST['id_persona'])   ? (int)$_POST['id_persona']    : 0;
$ultimos      = isset($_POST['ultimos'])      ? (int)$_POST['ultimos']       : 0;

if (!$id_subcuenta && !$id_persona) {
  http_response_code(400);
  echo "<tr><td colspan='8' class='text-center text-danger'>Falta id_persona o id_subcuenta</td></tr>";
  exit;
}

// --------- HELPERS ----------
function linkDestino(string $categoria, int $idComprobante, string $URL): string {
  switch ($categoria) {
    case 'Comprobantes-Contables': return $URL . "/comprobantes/show.php?id=" . $idComprobante;
    case 'Comprobantes-Ventas':    return $URL . "/despachos/show.php?id=" . $idComprobante;
    default:                       return $URL . "/otra_categoria/update.php?id=" . $idComprobante;
  }
}

// Agregado de descripciones por detalle (por comprobante) — opcional como fallback
$descAggSQL = "
  SELECT d.id_comprobante,
         GROUP_CONCAT(TRIM(d.descripcion) ORDER BY d.id_detalletransacciones SEPARATOR '; ') AS desc_det
  FROM tb_detalletransacciones d
  GROUP BY d.id_comprobante
";

/* ==========================================================
   A) RANGO DE FECHAS (si vienen fechas válidas)
   ========================================================== */
if ($fecha_inicio && $fecha_fin) {
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
    http_response_code(400);
    echo "<tr><td colspan='8' class='text-center text-danger'>Formato de fecha inválido</td></tr>";
    exit;
  }

  // Saldo inicial antes del rango (por subcuenta o por persona/terceros 1.1.2.1%)
  if ($id_subcuenta > 0) {
    $sql_ini = "
      SELECT COALESCE(SUM(t.debe),0) - COALESCE(SUM(t.haber),0) AS saldo_inicial
      FROM tb_transacciones t
      JOIN tb_comprobantes c
        ON c.id_comprobante = t.id_comprobante
       AND c.id_gestion     = :g
      WHERE t.id_subCuenta  = :id
        AND c.fecha_comprobante < :desde
    ";
    $st_ini = $pdo->prepare($sql_ini);
    $st_ini->execute([':g'=>$g, ':id'=>$id_subcuenta, ':desde'=>$fecha_inicio]);
  } else {
    $sql_ini = "
      SELECT COALESCE(SUM(t.debe),0) - COALESCE(SUM(t.haber),0) AS saldo_inicial
      FROM tb_transacciones t
      JOIN tb_comprobantes c
        ON c.id_comprobante = t.id_comprobante
       AND c.id_gestion     = :g
      JOIN tb_subcuentas s
        ON s.id_subCuenta   = t.id_subCuenta
      WHERE t.id_persona    = :id
        AND s.path LIKE '1.1.2.1%'
        AND c.fecha_comprobante < :desde
    ";
    $st_ini = $pdo->prepare($sql_ini);
    $st_ini->execute([':g'=>$g, ':id'=>$id_persona, ':desde'=>$fecha_inicio]);
  }

  $saldo = (float)$st_ini->fetchColumn();

  // SELECT común con mapeo de descripción:
  // - Para id_tipocomprobante IN (1,2,3,4,5,6,8) usar t.descripcion
  // - Para id_tipocomprobante IN (7,9) usar c.descripcion
  $commonSelect = "
    c.id_comprobante,
    c.fecha_comprobante,
    c.hora_comprobante,
    c.id_tipocomprobante,
    tc.name_tipocomprobante,
    cat.nombre_categoria,
    c.num_comprobante,
    CASE
      WHEN c.id_tipocomprobante IN (1,2,3,4,5,6,8)
        THEN COALESCE(t.descripcion, dd.desc_det, c.descripcion)
      ELSE
        COALESCE(c.descripcion, dd.desc_det, t.descripcion)
    END AS descripcion,
    t.debe,
    t.haber
  ";

  if ($id_subcuenta > 0) {
    $sql = "
      SELECT $commonSelect
      FROM tb_transacciones t
      JOIN tb_comprobantes c
        ON c.id_comprobante = t.id_comprobante
       AND c.id_gestion     = :g
      JOIN tb_subcuentas s                ON s.id_subCuenta   = t.id_subCuenta
      JOIN tb_tipocomprobante tc          ON tc.id_tipocomprobante = c.id_tipocomprobante
      JOIN tb_categoria_comprobante cat   ON cat.id_categoria = tc.id_categoria
      LEFT JOIN ( $descAggSQL ) dd        ON dd.id_comprobante = c.id_comprobante
      WHERE t.id_subCuenta = :id
        AND c.fecha_comprobante BETWEEN :desde AND :hasta
      ORDER BY c.fecha_comprobante, c.hora_comprobante, c.id_comprobante, c.num_comprobante
    ";
    $st = $pdo->prepare($sql);
    $st->execute([':g'=>$g, ':id'=>$id_subcuenta, ':desde'=>$fecha_inicio, ':hasta'=>$fecha_fin]);

  } else {
    $sql = "
      SELECT $commonSelect
      FROM tb_transacciones t
      JOIN tb_comprobantes c
        ON c.id_comprobante = t.id_comprobante
       AND c.id_gestion     = :g
      JOIN tb_subcuentas s                ON s.id_subCuenta   = t.id_subCuenta
      JOIN tb_tipocomprobante tc          ON tc.id_tipocomprobante = c.id_tipocomprobante
      JOIN tb_categoria_comprobante cat   ON cat.id_categoria = tc.id_categoria
      LEFT JOIN ( $descAggSQL ) dd        ON dd.id_comprobante = c.id_comprobante
      WHERE t.id_persona = :id
        AND s.path LIKE '1.1.2.1%'
        AND c.fecha_comprobante BETWEEN :desde AND :hasta
      ORDER BY c.fecha_comprobante, c.hora_comprobante, c.id_comprobante, c.num_comprobante
    ";
    $st = $pdo->prepare($sql);
    $st->execute([':g'=>$g, ':id'=>$id_persona, ':desde'=>$fecha_inicio, ':hasta'=>$fecha_fin]);
  }

  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  if (!$rows) {
    echo "<tr><td colspan='8' class='text-center text-muted'>Sin movimientos en el rango</td></tr>";
    exit;
  }

  foreach ($rows as $r) {
    $saldo += (float)$r['debe'] - (float)$r['haber'];

    $fecha = $r['fecha_comprobante'] ? date('d/m/Y', strtotime($r['fecha_comprobante'])) : '';
    $doc   = htmlspecialchars((string)($r['name_tipocomprobante'] ?? ''), ENT_QUOTES, 'UTF-8');
    $nro   = htmlspecialchars((string)($r['num_comprobante'] ?? ''),     ENT_QUOTES, 'UTF-8');
    $det   = htmlspecialchars((string)($r['descripcion'] ?? ''),         ENT_QUOTES, 'UTF-8');

    $debeFmt  = number_format((float)$r['debe'],  2, '.', ',');
    $haberFmt = number_format((float)$r['haber'], 2, '.', ',');
    $saldoFmt = number_format($saldo,             2, '.', ',');

    $urlDestino = linkDestino((string)$r['nombre_categoria'], (int)$r['id_comprobante'], $URL);

    echo "<tr>";
    echo   "<td style='vertical-align:middle;'>{$fecha}</td>";
    echo   "<td style='vertical-align:middle;'>{$doc}</td>";
    echo   "<td style='vertical-align:middle;'>{$nro}</td>";
    echo   "<td style='vertical-align:middle;'>{$det}</td>";
    echo   "<td style='vertical-align:middle; text-align:right;'>{$debeFmt}</td>";
    echo   "<td style='vertical-align:middle; text-align:right;'>{$haberFmt}</td>";
    echo   "<td style='vertical-align:middle; text-align:right;'>{$saldoFmt}</td>";
    echo   "<td style='vertical-align:middle;'>
              <div class='text-center'>
                <div class='btn-group'>
                  <a href='" . htmlspecialchars($urlDestino, ENT_QUOTES, 'UTF-8') . "' class='btn btn-success btn-sm' title='Ver'>
                    <i class='fa fa-eye fa-xs'></i>
                  </a>
                </div>
              </div>
            </td>";
    echo "</tr>";
  }
  exit;
}

/* ==========================================================
   B) ÚLTIMOS N (por defecto: 20)
   ========================================================== */
$N = ($ultimos > 0 ? $ultimos : 20);

// SELECT común con mapeo (igual que arriba)
$commonSelect = "
  c.id_comprobante,
  c.fecha_comprobante,
  c.hora_comprobante,
  c.id_tipocomprobante,
  tc.name_tipocomprobante,
  cat.nombre_categoria,
  c.num_comprobante,
  CASE
    WHEN c.id_tipocomprobante IN (1,2,3,4,5,6,8)
      THEN COALESCE(t.descripcion, dd.desc_det, c.descripcion)
    ELSE
      COALESCE(c.descripcion, dd.desc_det, t.descripcion)
  END AS descripcion,
  t.debe,
  t.haber
";

if ($id_subcuenta > 0) {
  $sql_lastN = "
    SELECT $commonSelect
    FROM tb_transacciones t
    JOIN tb_comprobantes c
      ON c.id_comprobante = t.id_comprobante
     AND c.id_gestion     = :g
    JOIN tb_subcuentas s                ON s.id_subCuenta   = t.id_subCuenta
    JOIN tb_tipocomprobante tc          ON tc.id_tipocomprobante = c.id_tipocomprobante
    JOIN tb_categoria_comprobante cat   ON cat.id_categoria = tc.id_categoria
    LEFT JOIN ( $descAggSQL ) dd        ON dd.id_comprobante = c.id_comprobante
    WHERE t.id_subCuenta = :id
    ORDER BY c.fecha_comprobante DESC, c.hora_comprobante DESC, c.id_comprobante DESC, c.num_comprobante DESC
    LIMIT $N
  ";
  $stN = $pdo->prepare($sql_lastN);
  $stN->execute([':g'=>$g, ':id'=>$id_subcuenta]);
} else {
  $sql_lastN = "
    SELECT $commonSelect
    FROM tb_transacciones t
    JOIN tb_comprobantes c
      ON c.id_comprobante = t.id_comprobante
     AND c.id_gestion     = :g
    JOIN tb_subcuentas s                ON s.id_subCuenta   = t.id_subCuenta
    JOIN tb_tipocomprobante tc          ON tc.id_tipocomprobante = c.id_tipocomprobante
    JOIN tb_categoria_comprobante cat   ON cat.id_categoria = tc.id_categoria
    LEFT JOIN ( $descAggSQL ) dd        ON dd.id_comprobante = c.id_comprobante
    WHERE t.id_persona = :id
      AND s.path LIKE '1.1.2.1%'
    ORDER BY c.fecha_comprobante DESC, c.hora_comprobante DESC, c.id_comprobante DESC, c.num_comprobante DESC
    LIMIT $N
  ";
  $stN = $pdo->prepare($sql_lastN);
  $stN->execute([':g'=>$g, ':id'=>$id_persona]);
}

$lastRowsDesc = $stN->fetchAll(PDO::FETCH_ASSOC);

if (!$lastRowsDesc) {
  echo "<tr><td colspan='8' class='text-center text-muted'>Sin movimientos</td></tr>";
  exit;
}

// Orden ascendente para acumular saldo correctamente
$lastRowsAsc = array_reverse($lastRowsDesc);
$first = $lastRowsAsc[0];
$firstFecha = $first['fecha_comprobante'] ?? null;
$firstHora  = $first['hora_comprobante'] ?? '00:00:00';
$firstId    = (int)($first['id_comprobante'] ?? 0);

// Saldo inicial antes del primer registro del bloque
if ($id_subcuenta > 0) {
  $sql_iniN = "
    SELECT COALESCE(SUM(t.debe),0) - COALESCE(SUM(t.haber),0) AS saldo_inicial
    FROM tb_transacciones t
    JOIN tb_comprobantes c
      ON c.id_comprobante = t.id_comprobante
     AND c.id_gestion     = :g
    WHERE t.id_subCuenta  = :id
      AND (c.fecha_comprobante < :f
           OR (c.fecha_comprobante = :f AND c.hora_comprobante < :h)
           OR (c.fecha_comprobante = :f AND c.hora_comprobante = :h AND c.id_comprobante < :cid))
  ";
  $sti = $pdo->prepare($sql_iniN);
  $sti->execute([':g'=>$g, ':id'=>$id_subcuenta, ':f'=>$firstFecha, ':h'=>$firstHora, ':cid'=>$firstId]);
} else {
  $sql_iniN = "
    SELECT COALESCE(SUM(t.debe),0) - COALESCE(SUM(t.haber),0) AS saldo_inicial
    FROM tb_transacciones t
    JOIN tb_comprobantes c
      ON c.id_comprobante = t.id_comprobante
     AND c.id_gestion     = :g
    JOIN tb_subcuentas s
      ON s.id_subCuenta   = t.id_subCuenta
    WHERE t.id_persona    = :id
      AND s.path LIKE '1.1.2.1%'
      AND (c.fecha_comprobante < :f
           OR (c.fecha_comprobante = :f AND c.hora_comprobante < :h)
           OR (c.fecha_comprobante = :f AND c.hora_comprobante = :h AND c.id_comprobante < :cid))
  ";
  $sti = $pdo->prepare($sql_iniN);
  $sti->execute([':g'=>$g, ':id'=>$id_persona, ':f'=>$firstFecha, ':h'=>$firstHora, ':cid'=>$firstId]);
}

$saldo = (float)$sti->fetchColumn();

// Pintar filas
foreach ($lastRowsAsc as $r) {
  $saldo += (float)$r['debe'] - (float)$r['haber'];

  $fecha = $r['fecha_comprobante'] ? date('d/m/Y', strtotime($r['fecha_comprobante'])) : '';
  $doc   = htmlspecialchars((string)($r['name_tipocomprobante'] ?? ''), ENT_QUOTES, 'UTF-8');
  $nro   = htmlspecialchars((string)($r['num_comprobante'] ?? ''),     ENT_QUOTES, 'UTF-8');
  $det   = htmlspecialchars((string)($r['descripcion'] ?? ''),         ENT_QUOTES, 'UTF-8');

  $debeFmt  = number_format((float)$r['debe'],  2, '.', ',');
  $haberFmt = number_format((float)$r['haber'], 2, '.', ',');
  $saldoFmt = number_format($saldo,             2, '.', ',');

  $urlDestino = linkDestino((string)$r['nombre_categoria'], (int)$r['id_comprobante'], $URL);

  echo "<tr>";
  echo   "<td style='vertical-align:middle;'>{$fecha}</td>";
  echo   "<td style='vertical-align:middle;'>{$doc}</td>";
  echo   "<td style='vertical-align:middle;'>{$nro}</td>";
  echo   "<td style='vertical-align:middle;'>{$det}</td>";
  echo   "<td style='vertical-align:middle; text-align:right;'>{$debeFmt}</td>";
  echo   "<td style='vertical-align:middle; text-align:right;'>{$haberFmt}</td>";
  echo   "<td style='vertical-align:middle; text-align:right;'>{$saldoFmt}</td>";
  echo   "<td style='vertical-align:middle;'>
            <div class='text-center'>
              <div class='btn-group'>
                <a href='" . htmlspecialchars($urlDestino, ENT_QUOTES, 'UTF-8') . "' class='btn btn-success btn-sm' title='Ver'>
                  <i class='fa fa-eye fa-xs'></i>
                </a>
              </div>
            </div>
          </td>";
  echo "</tr>";
}
exit;
