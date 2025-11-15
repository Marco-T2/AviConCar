<?php
// C:\web\stack\ct\public\kardex\kardexprint.php
// Incluye TCPDF y config (mantengo tus rutas)
require_once('../app/TCPDF-main/tcpdf.php');
include('../app/config.php');

$gestion_activa = (int) GESTION_ACTIVA;

// --------- ENTRADA (GET) ----------
$fechaInicio = isset($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : null;
$fechaFin    = isset($_GET['fecha_fin'])    ? trim($_GET['fecha_fin'])    : null;
$id_persona  = isset($_GET['id_persona'])   ? (int)$_GET['id_persona']    : 0;

// Validación simple
if (!$fechaInicio || !$fechaFin || !$id_persona ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaInicio) ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFin)) {
  die('Parámetros inválidos (fecha_inicio, fecha_fin, id_persona).');
}

// --------- CONSULTAS ----------

// 1) Datos de la persona
$sql_personas = "
  SELECT 
    a.id_persona,
    a.name_persona,
    b.name_tipoPersona,
    a.direccion,
    a.celular,
    a.descripcion
  FROM tb_personas a
  JOIN tb_tipopersonas b ON a.id_tipoPersona = b.id_tipoPersona
  WHERE a.id_persona = :id_persona
";
$st_persona = $pdo->prepare($sql_personas);
$st_persona->bindValue(':id_persona', $id_persona, PDO::PARAM_INT);
$st_persona->execute();
$persona = $st_persona->fetch(PDO::FETCH_ASSOC);
if (!$persona) { die('No se encontraron datos para la persona.'); }
$name_persona = $persona['name_persona'];

// 2) Saldo inicial (antes de fechaInicio) SOLO terceros (1.1.2.1%)
$sql_ini = "
  SELECT COALESCE(SUM(t.debe),0) - COALESCE(SUM(t.haber),0) AS saldo_inicial
  FROM tb_transacciones t
  JOIN tb_comprobantes c ON c.id_comprobante = t.id_comprobante AND c.id_gestion = :g
  JOIN tb_subcuentas s ON s.id_subcuenta = t.id_subcuenta
  WHERE t.id_persona = :id AND s.path LIKE '1.1.2.1%' AND c.fecha_comprobante < :fi
";
$st_ini = $pdo->prepare($sql_ini);
$st_ini->execute([':g'=>$gestion_activa, ':id'=>$id_persona, ':fi'=>$fechaInicio]);
$saldo_inicial = (float)$st_ini->fetchColumn();
$saldo = $saldo_inicial;

// 3) Movimientos en el rango
// Regla de descripción:
// - Si id_tipocomprobante IN (1,2,3,4,5,6,8) => usar descripción de tb_detalletransacciones (cualquier fila, elegimos MIN para estabilidad). Si no existe, usar c.descripcion
// - Si id_tipocomprobante IN (7,9) => usar c.descripcion
$sql_movs = "
  SELECT
    c.id_comprobante,
    c.fecha_comprobante,
    c.hora_comprobante,
    tc.id_tipocomprobante,
    tc.name_tipocomprobante,
    c.num_comprobante,
    -- Descripción final según regla
    CASE 
      WHEN tc.id_tipocomprobante IN (1,2,3,4,5,6,8) THEN COALESCE(
        (SELECT MIN(d.descripcion) FROM tb_detalletransacciones d WHERE d.id_comprobante = c.id_comprobante),
        c.descripcion
      )
      ELSE c.descripcion
    END AS descripcion_final,
    t.debe,
    t.haber
  FROM tb_transacciones t
  JOIN tb_comprobantes c
    ON c.id_comprobante = t.id_comprobante AND c.id_gestion = :g
  JOIN tb_tipocomprobante tc
    ON tc.id_tipocomprobante = c.id_tipocomprobante
  JOIN tb_subcuentas s
    ON s.id_subcuenta = t.id_subcuenta
  WHERE
    t.id_persona = :id
    AND s.path LIKE '1.1.2.1%'
    AND c.fecha_comprobante BETWEEN :fi AND :ff
  ORDER BY c.fecha_comprobante, c.hora_comprobante, c.id_comprobante, c.num_comprobante
";
$st_movs = $pdo->prepare($sql_movs);
$st_movs->execute([
  ':g'  => $gestion_activa,
  ':id' => $id_persona,
  ':fi' => $fechaInicio,
  ':ff' => $fechaFin
]);
$movs = $st_movs->fetchAll(PDO::FETCH_ASSOC);

// --------- TCPDF ----------

// Fechas legibles
$fechaInicioFormato = date('d/m/Y', strtotime($fechaInicio));
$fechaFinFormato    = date('d/m/Y', strtotime($fechaFin));
$textoFecha         = 'Del ' . $fechaInicioFormato . ' al ' . $fechaFinFormato;

// PDF A4
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(215, 279), PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema avícola');
$pdf->SetTitle('Kardex');
$pdf->SetSubject('Kardex');
$pdf->SetKeywords('TCPDF, PDF, kardex');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->setMargins(15, 5, 15);
$pdf->setAutoPageBreak(true, 5);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFont('times', '', 12);
$pdf->AddPage();

// Cabecera
$html = '
<table cellpadding="0" cellspacing="0">
  <tr>
    <td rowspan="4"><img src="' . K_PATH_IMAGES . 'Logo2.jpg" width="80" height="60" /></td>
    <td style="text-align:center; font-size: 14px;"><b>Avícola el Carmen</b></td>
    <td rowspan="4" style="text-align:center; font-size: 8px; vertical-align:middle;"><b>Se generó: ' . date('d/m/Y H:i') . '</b></td>
  </tr>
  <tr>
    <td style="text-align:center; font-size: 12px;">Kardex - ' . htmlspecialchars($name_persona, ENT_QUOTES, 'UTF-8') . '</td>
  </tr>
  <tr>
    <td style="text-align:center; font-size: 12px;">' . $textoFecha . '</td>
  </tr>
</table>

<br>

<table cellpadding="5" cellspacing="0" style="width: 100%; margin-top: 10px;">
  <tr><td style="text-align:center;"><h4 style="margin: 0;">Transacciones</h4></td></tr>
</table>

<table border="1">
  <thead>
    <tr style="text-align:center; background-color:#c0c0c0; font-size: 14px;">
      <th style="width:65px">Fecha</th>
      <th style="width:105px">Doc</th>
      <th style="width:40px">Nro</th>
      <th style="width:250px">Detalle</th>
      <th style="width:65px">Debe</th>
      <th style="width:65px">Haber</th>
      <th style="width:65px">Saldo</th>
    </tr>
  </thead>
  <tbody>';

// Fila de saldo inicial (opcional, si quieres mostrarlo)
$html .= '
  <tr style="font-size:12px">
    <td style="text-align:center;width:65px">' . $fechaInicioFormato . '</td>
    <td style="text-align:left;width:105px">Saldo Inicial</td>
    <td style="text-align:center;width:40px"></td>
    <td style="width:250px">Saldo antes del rango</td>
    <td style="text-align:right;width:65px">0.00</td>
    <td style="text-align:right;width:65px">0.00</td>
    <td style="text-align:right;width:65px">' . number_format($saldo_inicial, 2, '.', ',') . '</td>
  </tr>';

// Detalle
foreach ($movs as $m) {
  $saldo += (float)$m['debe'] - (float)$m['haber'];
  $html .= '
    <tr style="font-size:12px">
      <td style="text-align:center;width:65px">' . date('d/m/Y', strtotime($m['fecha_comprobante'])) . '</td>
      <td style="text-align:left;width:105px">' . htmlspecialchars($m['name_tipocomprobante'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>
      <td style="text-align:center;width:40px">' . (int)$m['num_comprobante'] . '</td>
      <td style="width:250px">' . htmlspecialchars($m['descripcion_final'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>
      <td style="text-align:right;width:65px">' . number_format((float)$m['debe'], 2, '.', ',') . '</td>
      <td style="text-align:right;width:65px">' . number_format((float)$m['haber'], 2, '.', ',') . '</td>
      <td style="text-align:right;width:65px">' . number_format($saldo, 2, '.', ',') . '</td>
    </tr>';
}

$html .= '
  </tbody>
</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Kardex.pdf', 'I');
