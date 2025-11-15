<?php
require_once '../app/config.php';

$id_gestion = (int) GESTION_ACTIVA;
$fi = $_GET['fecha_inicio'] ?? '';
$ff = $_GET['fecha_fin']    ?? '';

$dfi = DateTime::createFromFormat('Y-m-d', $fi);
$dff = DateTime::createFromFormat('Y-m-d', $ff);
if (!$dfi || !$dff) { die('Fechas inválidas'); }

$fname = 'ResumenVentas_'.$dfi->format('Y-m-d').'_a_'.$dff->format('Y-m-d').'.csv';

$sql = "
  SELECT 
    c.fecha_comprobante,
    tc.name_tipocomprobante,
    c.num_comprobante,
    MAX(p.name_persona)                           AS name_persona,
    COALESCE(SUM(d.cantidadCajas),0)              AS cantidadCajas,
    COALESCE(SUM(d.pesoB_kg),0)                   AS pesoB_kg,
    COALESCE(SUM(d.pesoN_kg),0)                   AS pesoN_kg,
    COALESCE(SUM(d.subTotal),0)                   AS monto_bs
  FROM tb_comprobantes c
  JOIN tb_tipocomprobante tc ON c.id_tipocomprobante = tc.id_tipocomprobante
  LEFT JOIN tb_transacciones t  ON c.id_comprobante = t.id_comprobante
  LEFT JOIN tb_personas p       ON t.id_persona     = p.id_persona
  LEFT JOIN tb_detalletransacciones d ON d.id_comprobante = c.id_comprobante
  WHERE 
    c.id_gestion = :id_gestion
    AND tc.id_categoria = 2
    AND c.fecha_comprobante BETWEEN :fi AND :ff
  GROUP BY 
    c.id_comprobante, c.fecha_comprobante, c.num_comprobante, tc.name_tipocomprobante
  ORDER BY 
    c.fecha_comprobante DESC, c.id_comprobante DESC
";
$st = $pdo->prepare($sql);
$st->execute([
  ':id_gestion' => $id_gestion,
  ':fi' => $dfi->format('Y-m-d'),
  ':ff' => $dff->format('Y-m-d')
]);

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$fname.'"');

$out = fopen('php://output', 'w');
// Cabecera
fputcsv($out, ['Fecha','Tipo','Nro','Cliente','Cajas','Peso B (kg)','Peso N (kg)','Monto (Bs)'], ';');

// Totales
$totC=0.0; $totPB=0.0; $totPN=0.0; $totM=0.0;

// Datos
while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
  $row = [
    date('Y-m-d', strtotime($r['fecha_comprobante'])),
    $r['name_tipocomprobante'],
    (int)$r['num_comprobante'],
    $r['name_persona'] ?? '',
    number_format((float)$r['cantidadCajas'], 2, '.', ''),
    number_format((float)$r['pesoB_kg'],     2, '.', ''),
    number_format((float)$r['pesoN_kg'],     2, '.', ''),
    number_format((float)$r['monto_bs'],     2, '.', ''),
  ];
  fputcsv($out, $row, ';');

  $totC += (float)$r['cantidadCajas'];
  $totPB+= (float)$r['pesoB_kg'];
  $totPN+= (float)$r['pesoN_kg'];
  $totM += (float)$r['monto_bs'];
}

// Línea en blanco y totales
fputcsv($out, [], ';');
fputcsv($out, ['Totales','','','',
  number_format($totC, 2, '.', ''),
  number_format($totPB,2, '.', ''),
  number_format($totPN,2, '.', ''),
  number_format($totM, 2, '.', ''),
], ';');

fclose($out);
exit;
