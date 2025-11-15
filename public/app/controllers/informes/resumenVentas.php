<?php
// app/controllers/informes/resumenVentas.php
$id_gestion = (int) GESTION_ACTIVA;

$fecha_inicio = $_GET['fecha_inicio'] ?? null;
$fecha_fin    = $_GET['fecha_fin']    ?? null;

$fi = DateTime::createFromFormat('Y-m-d', (string)$fecha_inicio);
$ff = DateTime::createFromFormat('Y-m-d', (string)$fecha_fin);
if (!$fi || !$ff) { die('Las fechas proporcionadas no son válidas.'); }

$sql = "
  SELECT 
    c.id_comprobante,
    c.fecha_comprobante,
    c.num_comprobante,
    c.descripcion,
    tc.name_tipocomprobante,

    -- Si un comprobante tiene varias personas en transacciones, tomamos una estable (MAX)
    MAX(p.name_persona)                           AS name_persona,

    -- Sumas desde el detalle de ventas
    COALESCE(SUM(d.cantidadCajas),0)              AS cantidadCajas,
    COALESCE(SUM(d.pesoB_kg),0)                   AS pesoB_kg,
    COALESCE(SUM(d.pesoN_kg),0)                   AS pesoN_kg,
    COALESCE(SUM(d.subTotal),0)                   AS monto_bs

  FROM tb_comprobantes c
  JOIN tb_tipocomprobante tc ON c.id_tipocomprobante = tc.id_tipocomprobante

  -- LEFT JOIN para persona (si no hubiese t/persona no perdemos el comprobante)
  LEFT JOIN tb_transacciones t  ON c.id_comprobante = t.id_comprobante
  LEFT JOIN tb_personas p       ON t.id_persona     = p.id_persona

  -- Detalle de ventas
  LEFT JOIN tb_detalletransacciones d ON d.id_comprobante = c.id_comprobante

  WHERE 
    c.id_gestion = :id_gestion
    AND tc.id_categoria = 2
    AND c.fecha_comprobante BETWEEN :fi AND :ff

  GROUP BY 
    c.id_comprobante, c.fecha_comprobante, c.num_comprobante, c.descripcion, tc.name_tipocomprobante

  ORDER BY 
    c.fecha_comprobante DESC, c.id_comprobante DESC
";
$st = $pdo->prepare($sql);
$st->bindValue(':id_gestion', $id_gestion, PDO::PARAM_INT);
$st->bindValue(':fi', $fi->format('Y-m-d'));
$st->bindValue(':ff', $ff->format('Y-m-d'));
$st->execute();
$transacciones_datos = $st->fetchAll(PDO::FETCH_ASSOC);
