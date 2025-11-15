<?php
require_once '../../config.php';

$id_gestion = (int) GESTION_ACTIVA;
$fi = $_POST['fecha_inicio'] ?? '';
$ff = $_POST['fecha_fin']    ?? '';

$dfi = DateTime::createFromFormat('Y-m-d', $fi);
$dff = DateTime::createFromFormat('Y-m-d', $ff);
if (!$dfi || !$dff) { http_response_code(400); die('Fechas inválidas'); }

$sql = "
  SELECT 
    c.id_comprobante,
    c.fecha_comprobante,
    c.num_comprobante,
    c.descripcion,
    tc.name_tipocomprobante,
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
    c.id_comprobante, c.fecha_comprobante, c.num_comprobante, c.descripcion, tc.name_tipocomprobante
  ORDER BY 
    c.fecha_comprobante DESC, c.id_comprobante DESC
";

$st = $pdo->prepare($sql);
$st->execute([
  ':id_gestion' => $id_gestion,
  ':fi' => $dfi->format('Y-m-d'),
  ':ff' => $dff->format('Y-m-d')
]);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/html; charset=UTF-8');

if (!$rows) {
  // Devuelve una fila vacía “amigable” (ajusta el colspan si cambias columnas)
  echo '<tr><td colspan="9" class="text-center">Sin resultados para el rango seleccionado.</td></tr>';
  exit;
}

foreach ($rows as $r) {
  $id = (int)$r['id_comprobante'];
  echo '<tr>';
  echo '<td style="text-align:center;">' . htmlspecialchars(date('d/m/Y', strtotime($r['fecha_comprobante']))) . '</td>';
  echo '<td style="text-align:center;">' . htmlspecialchars($r['name_tipocomprobante'] ?? '') . '</td>';
  echo '<td style="text-align:center;">' . (int)($r['num_comprobante'] ?? 0) . '</td>';
  echo '<td>' . htmlspecialchars($r['name_persona'] ?? '') . '</td>';

  echo '<td style="text-align:right;">' . number_format((float)$r['cantidadCajas'], 2, '.', ',') . '</td>';
  echo '<td style="text-align:right;">' . number_format((float)$r['pesoB_kg'],     2, '.', ',') . '</td>';
  echo '<td style="text-align:right;">' . number_format((float)$r['pesoN_kg'],     2, '.', ',') . '</td>';
  echo '<td style="text-align:right;">' . number_format((float)$r['monto_bs'],     2, '.', ',') . '</td>';

  // Botón Ver -> despachos/show.php
  echo '<td><center><div class="btn-group">
          <a href="' . $URL . '/despachos/show.php?id=' . $id . '" class="btn btn-success btn-sm" title="Ver"><i class="fa fa-eye fa-sm"></i></a>
        </div></center></td>';
  echo '</tr>';
}

exit;
