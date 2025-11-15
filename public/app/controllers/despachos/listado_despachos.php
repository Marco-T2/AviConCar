<?php
// C:\web\stack\ct\public\app\controllers\despachos\listado_despachos.php

if (!defined('GESTION_ACTIVA')) { die('Falta GESTION_ACTIVA'); }
$id_gestion = (int) GESTION_ACTIVA;

// Fechas inyectadas desde index.php (por defecto: últimas 2 semanas)
$desde = isset($_FILTRO_DESDE) ? $_FILTRO_DESDE : date('Y-m-d', strtotime('-14 days'));
$hasta = isset($_FILTRO_HASTA) ? $_FILTRO_HASTA : date('Y-m-d');

$transacciones_datos = [];
$fallback_aplicado = false;

// ---- Consulta principal por rango ----
$sql = "
SELECT
  c.id_comprobante,
  c.fecha_comprobante,
  tc.name_tipocomprobante,
  c.num_comprobante,
  c.descripcion,
  agg.name_persona,
  agg.subTotal
FROM tb_comprobantes c
JOIN tb_tipocomprobante tc
  ON c.id_tipocomprobante = tc.id_tipocomprobante
JOIN (
    SELECT
      t.id_comprobante,
      MIN(p.name_persona) AS name_persona,   -- nombre estable
      SUM(t.debe) AS subTotal
    FROM tb_transacciones t
    JOIN tb_personas p ON p.id_persona = t.id_persona
    GROUP BY t.id_comprobante
) AS agg
  ON agg.id_comprobante = c.id_comprobante
WHERE
  c.id_gestion = :id_gestion
  AND tc.id_categoria = 2
  AND c.fecha_comprobante BETWEEN :desde AND :hasta
ORDER BY c.fecha_comprobante DESC, c.id_comprobante DESC
";
$q = $pdo->prepare($sql);
$q->bindParam(':id_gestion', $id_gestion, PDO::PARAM_INT);
$q->bindParam(':desde', $desde, PDO::PARAM_STR);
$q->bindParam(':hasta', $hasta, PDO::PARAM_STR);
$q->execute();
$transacciones_datos = $q->fetchAll(PDO::FETCH_ASSOC);

// ---- Fallback: últimos 15 si no hubo resultados ----
if (empty($transacciones_datos)) {
  // 1) IDs de los últimos 15 comprobantes (categoría 2) de la gestión
  $qIds = $pdo->prepare("
    SELECT c.id_comprobante
    FROM tb_comprobantes c
    JOIN tb_tipocomprobante tc ON c.id_tipocomprobante = tc.id_tipocomprobante
    WHERE c.id_gestion = :id_gestion AND tc.id_categoria = 2
    ORDER BY c.fecha_comprobante DESC, c.id_comprobante DESC
    LIMIT 15
  ");
  $qIds->execute([':id_gestion' => $id_gestion]);
  $ids = $qIds->fetchAll(PDO::FETCH_COLUMN, 0);

  if (!empty($ids)) {
    $in = implode(',', array_fill(0, count($ids), '?'));
    $qAgg = $pdo->prepare("
      SELECT
        c.id_comprobante,
        c.fecha_comprobante,
        tc.name_tipocomprobante,
        c.num_comprobante,
        c.descripcion,
        agg.name_persona,
        agg.subTotal
      FROM tb_comprobantes c
      JOIN tb_tipocomprobante tc
        ON c.id_tipocomprobante = tc.id_tipocomprobante
      JOIN (
          SELECT
            t.id_comprobante,
            MIN(p.name_persona) AS name_persona,
            SUM(t.debe) AS subTotal
          FROM tb_transacciones t
          JOIN tb_personas p ON p.id_persona = t.id_persona
          GROUP BY t.id_comprobante
      ) AS agg
        ON agg.id_comprobante = c.id_comprobante
      WHERE c.id_comprobante IN ($in)
      ORDER BY c.fecha_comprobante DESC, c.id_comprobante DESC
    ");
    foreach ($ids as $k => $v) { $qAgg->bindValue($k+1, (int)$v, PDO::PARAM_INT); }
    $qAgg->execute();
    $transacciones_datos = $qAgg->fetchAll(PDO::FETCH_ASSOC);
    $fallback_aplicado = true;
  }
}
