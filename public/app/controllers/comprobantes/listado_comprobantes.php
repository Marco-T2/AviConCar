<?php
// Requiere $pdo y GESTION_ACTIVA desde config.php
$id_gestion = (int) GESTION_ACTIVA;

// --------- Rango (por defecto: últimas 2 semanas) ----------
if (!isset($_FILTRO_DESDE) || !isset($_FILTRO_HASTA)) {
  $hoy = new DateTime('today');
  $_FILTRO_HASTA = $hoy->format('Y-m-d');
  $_FILTRO_DESDE = (clone $hoy)->modify('-14 days')->format('Y-m-d');
}

$cat = 1; // tu filtro tc.id_categoria = 1
$comprobantes_datos = [];
$fallback_aplicado = false;

// --------- Consulta principal por rango ----------
$sql = "
  SELECT
    c.id_comprobante,
    c.fecha_comprobante,
    c.hora_comprobante,
    c.num_comprobante,
    c.descripcion,
    tc.name_tipocomprobante,
    MAX(p.name_persona) AS name_persona,
    SUM(t.debe)  AS debe,
    SUM(t.haber) AS haber
  FROM tb_comprobantes c
  INNER JOIN tb_tipocomprobante tc ON c.id_tipocomprobante = tc.id_tipocomprobante
  INNER JOIN tb_transacciones t     ON c.id_comprobante = t.id_comprobante
  LEFT  JOIN tb_personas p          ON t.id_persona = p.id_persona
  WHERE
    c.id_gestion = :id_gestion
    AND tc.id_categoria = :cat
    AND c.fecha_comprobante BETWEEN :desde AND :hasta
  GROUP BY
    c.id_comprobante, c.fecha_comprobante, c.hora_comprobante, c.num_comprobante, c.descripcion, tc.name_tipocomprobante
  ORDER BY
    c.fecha_comprobante DESC, c.hora_comprobante DESC, c.id_comprobante DESC
";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id_gestion', $id_gestion, PDO::PARAM_INT);
$stmt->bindValue(':cat', $cat, PDO::PARAM_INT);
$stmt->bindValue(':desde', $_FILTRO_DESDE, PDO::PARAM_STR);
$stmt->bindValue(':hasta', $_FILTRO_HASTA, PDO::PARAM_STR);
$stmt->execute();
$comprobantes_datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --------- Fallback: últimos 15 si no hubo resultados ----------
if (empty($comprobantes_datos)) {
  // 1) Obtener IDs de los últimos 15 comprobantes en la gestión/categoría
  $qIds = $pdo->prepare("
    SELECT c.id_comprobante
    FROM tb_comprobantes c
    INNER JOIN tb_tipocomprobante tc ON c.id_tipocomprobante = tc.id_tipocomprobante
    WHERE c.id_gestion = :id_gestion AND tc.id_categoria = :cat
    ORDER BY c.fecha_comprobante DESC, c.hora_comprobante DESC, c.id_comprobante DESC
    LIMIT 15
  ");
  $qIds->execute([':id_gestion'=>$id_gestion, ':cat'=>$cat]);
  $ids = $qIds->fetchAll(PDO::FETCH_COLUMN, 0);

  if (!empty($ids)) {
    $in = implode(',', array_fill(0, count($ids), '?'));
    $qAgg = $pdo->prepare("
      SELECT
        c.id_comprobante,
        c.fecha_comprobante,
        c.hora_comprobante,
        c.num_comprobante,
        c.descripcion,
        tc.name_tipocomprobante,
        MAX(p.name_persona) AS name_persona,
        SUM(t.debe)  AS debe,
        SUM(t.haber) AS haber
      FROM tb_comprobantes c
      INNER JOIN tb_tipocomprobante tc ON c.id_tipocomprobante = tc.id_tipocomprobante
      INNER JOIN tb_transacciones t     ON c.id_comprobante = t.id_comprobante
      LEFT  JOIN tb_personas p          ON t.id_persona = p.id_persona
      WHERE c.id_comprobante IN ($in)
      GROUP BY
        c.id_comprobante, c.fecha_comprobante, c.hora_comprobante, c.num_comprobante, c.descripcion, tc.name_tipocomprobante
      ORDER BY
        c.fecha_comprobante DESC, c.hora_comprobante DESC, c.id_comprobante DESC
    ");
    // bind dinámico
    foreach ($ids as $k=>$v) { $qAgg->bindValue($k+1, (int)$v, PDO::PARAM_INT); }
    $qAgg->execute();
    $comprobantes_datos = $qAgg->fetchAll(PDO::FETCH_ASSOC);
    $fallback_aplicado = true;
  }
}

// (Opcional) Puedes exponer una bandera para la vista
$RANGO_INFO = $fallback_aplicado
  ? 'Sin resultados en el rango. Mostrando últimos 15.'
  : 'Rango aplicado: ' . $_FILTRO_DESDE . ' → ' . $_FILTRO_HASTA;
