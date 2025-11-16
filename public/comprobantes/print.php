<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/TCPDF-main/tcpdf.php';df.p

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  http_response_code(400);
  exit('Falta o es inválido el id de comprobante.');
}

/* ====== CABECERA DEL COMPROBANTE ====== */
$sql = "SELECT
          c.id_comprobante,
          c.num_comprobante,
          c.fecha_comprobante,
          c.hora_comprobante,
          c.descripcion,
          c.id_gestion,
          tc.name_tipocomprobante
        FROM tb_comprobantes c
        LEFT JOIN tb_tipocomprobante tc ON c.id_tipocomprobante = tc.id_tipocomprobante
        WHERE c.id_comprobante = :id
        LIMIT 1";
$st = $pdo->prepare($sql);
$st->execute([':id' => $id]);
$comp = $st->fetch(PDO::FETCH_ASSOC);
if (!$comp) {
  http_response_code(404);
  exit('Comprobante no encontrado.');
}

/* ====== DETALLE CONTABLE (con persona por línea) ====== */
$qd = $pdo->prepare("
  SELECT
    s.path           AS codigo_contable,
    s.name_subCuenta AS cuenta,
    t.debe, t.haber,
    t.descripcion    AS detalle,
    p.name_persona   AS persona
  FROM tb_transacciones t
  JOIN tb_subcuentas s ON t.id_subCuenta = s.id_subCuenta
  LEFT JOIN tb_personas  p ON t.id_persona  = p.id_persona
  WHERE t.id_comprobante = :id
  ORDER BY s.path, s.name_subCuenta
");
$qd->execute([':id' => $id]);
$rows = $qd->fetchAll(PDO::FETCH_ASSOC);

$sumDebe = 0.0;
$sumHaber = 0.0;
foreach ($rows as $r) {
  $sumDebe  += (float)$r['debe'];
  $sumHaber += (float)$r['haber'];
}

/* ====== TCPDF ====== */
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(215, 279), PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('AVICOLA EL CARMEN');
$pdf->SetTitle('Comprobante #' . (string)$comp['num_comprobante']);
$pdf->SetSubject('Comprobante');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//Margen de 5 a cada lado
$pdf->setMargins(15, 5, 15);
$pdf->setAutoPageBreak(true, 5);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
  require_once(dirname(__FILE__) . '/lang/eng.php');
  $pdf->setLanguageArray($l);
}

$pdf->SetFont('times', '', 12);

$pdf->AddPage();

$num = (string)($comp['num_comprobante'] ?? '');
$fechaEmi = !empty($comp['fecha_comprobante']) ? date('d/m/Y', strtotime($comp['fecha_comprobante'])) : '';
$tipoNom = (string)($comp['name_tipocomprobante'] ?? '');

$html = '
<table cellpadding="0" cellspacing="0">
    <tr>
        <td style="text-align:center;width:150px"><img src="' . K_PATH_IMAGES . 'Logo2.jpg" width="100" height="80" /></td>
        <td style="text-align:center;width:380px"></td>
        <td style="text-align:right ;width:130px; font-size: 8px; vertical-align:middle;"><b>Se generó: ' . date('d/m/Y H:i') . '</b></td>
    </tr>
</table>

<br>
<table>
<tr>
<td style="text-align:center; font-size: 12px; width:150px;color: #113563;"><b>DISTRIBUIDORA DE POLLO LA PAZ</b><br>
"EL CARMEN"<br>
67309734-74133325 <br>
LA PAZ-COCHABAMBA
</td>
<td style="text-align:center; font-size: 15px; width:380px"><b style="color: #113563;">' . $tipoNom . '</b><br><br>
<b style="color: #DD2E44;">Nro: 00' . $num . '</b>
</td>
<td style="text-align:center; font-size:15px; width:130px; color: #113563;"><b>FECHA:</b>' . $fechaEmi . '</td>
</tr>
</table>
<br>
<p style="text-align:center; font-size:18px; margin-top: 0px;color: #113563;"><b>DETALLE</b></p>
<div>
    <table>

    </table>
</div>
';


/* ====== Tabla de partidas (Código, Cuenta, Debe, Haber, Descripción, Persona) ====== */
/* Anchos: 16% | 34% | 12% | 12% | 16% | 10%  (100%) */

$tbl = <<<'HTML'
<style>
  .th { background-color:#f6f8fb; font-weight:bold; }
  .b  { border:0.3px solid #bbb; }
  .c  { text-align:center; }
  .r  { text-align:right; }
  .p  { padding:6px 4px; }
  .small{ font-size:10px; }
  .foot { font-weight:700; }
</style>

<table cellpadding="6" cellspacing="0" width="100%" style="margin-top:6px; font-size:11px;">
  <thead>
    <tr class="th">
      <td class="b c p" style="width:9%"><b>Código</b></td>
      <td class="b c p" style="width:20%"><b>Cuenta</b></td>
      <td class="b c p" style="width:10%"><b>Debe</b></td>
      <td class="b c p" style="width:12%"><b>Haber</b></td>
      <td class="b c p" style="width:34%"><b>Descripción</b></td>
      <td class="b c p" style="width:15%"><b>Persona</b></td>
    </tr>
  </thead>
  <tbody>
HTML;

if ($rows) {
  $i = 0;
  foreach ($rows as $r) {
    $codigo = htmlspecialchars((string)$r['codigo_contable'], ENT_QUOTES, 'UTF-8');
    $cuenta = htmlspecialchars((string)$r['cuenta'],           ENT_QUOTES, 'UTF-8');
    $debe   = number_format((float)$r['debe'],  2, '.', ',');
    $haber  = number_format((float)$r['haber'], 2, '.', ',');
    $det    = htmlspecialchars((string)($r['detalle']  ?? ''), ENT_QUOTES, 'UTF-8');
    $pers   = htmlspecialchars((string)($r['persona']  ?? ''), ENT_QUOTES, 'UTF-8');

    $rowStyle = ($i % 2 === 0) ? 'background-color:#ffffff;' : 'background-color:#fbfcfe;';

    $tbl .= "\n        <tr style=\"{$rowStyle}\">\n          <td class=\"b p small\" style=\"width:9%\">{$codigo}</td>\n          <td class=\"b p small\" style=\"width:20%\">{$cuenta}</td>\n          <td class=\"b r p small\" style=\"width:10%\">{$debe}</td>\n          <td class=\"b r p small\" style=\"width:12%\">{$haber}</td>\n          <td class=\"b p small\" style=\"width:34%\">{$det}</td>\n          <td class=\"b p small\" style=\"width:15%\">{$pers}</td>\n        </tr>";
    $i++;
  }
} else {
  $tbl .= '<tr><td class="b c p" colspan="6">Sin partidas contables</td></tr>';
}

$tbl .= <<<'HTML'
  </tbody>
  <tfoot>
    <tr class="th">
      <td class="b p" colspan="2"><b>&nbsp;Totales&nbsp;</b></td>
      <td class="b r p foot"><b>
HTML;

$tbl .= number_format($sumDebe, 2, '.', ',');

$tbl .= <<<'HTML'
</b></td>
      <td class="b r p foot"><b>
HTML;

$tbl .= number_format($sumHaber, 2, '.', ',');

$tbl .= <<<'HTML'
</b></td>
      <td class="b p" colspan="2">&nbsp;</td>
    </tr>
  </tfoot>
</table>
HTML;

// (Las cajas de firma se dibujarán al final de la hoja, fuera del bloque de detalle)

/* ====== Firmas ====== */

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->writeHTML($tbl, true, false, true, false, '');

// Dibujar cajas de firma siempre al final de la hoja
// Usar el mismo estilo de borde que la tabla (.b) y colocar las etiquetas dentro, al fondo del recuadro
$pdf->SetY(-82); // dejar espacio para cajas (ajustar si es necesario)
$signHtml = '<table cellpadding="6" cellspacing="0" width="100%" style="font-family:times; font-size:11px;">'
  . '<tr>'
  . '<td style="width:33%; text-align:center; padding:4px;">'
    . '<table cellpadding="0" cellspacing="0" width="100%" style="border:0.3px solid #bbb; height:72px;">'
      . '<tr><td style="vertical-align:bottom; text-align:center; font-size:10px; padding-bottom:6px;"><b>Elaboró</b></td></tr>'
    . '</table>'
  . '</td>'
  . '<td style="width:33%; text-align:center; padding:4px;">'
    . '<table cellpadding="0" cellspacing="0" width="100%" style="border:0.3px solid #bbb; height:72px;">'
      . '<tr><td style="vertical-align:bottom; text-align:center; font-size:10px; padding-bottom:6px;"><b>Revisó</b></td></tr>'
    . '</table>'
  . '</td>'
  . '<td style="width:34%; text-align:center; padding:4px;">'
    . '<table cellpadding="0" cellspacing="0" width="100%" style="border:0.3px solid #bbb; height:72px;">'
      . '<tr><td style="vertical-align:bottom; text-align:center; font-size:10px; padding-bottom:6px;"><b>Aprobó</b></td></tr>'
    . '</table>'
  . '</td>'
  . '</tr>'
  . '</table>';
$pdf->writeHTML($signHtml, true, false, true, false, '');

$pdf->Output('Comprobante_' . (string)$comp['num_comprobante'] . '.pdf', 'I');
