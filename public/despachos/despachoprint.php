<?php
require_once('../app/TCPDF-main/tcpdf.php');
include('../app/config.php');

$gestion_activa = GESTION_ACTIVA;

if (isset($_GET['id_comprobante']) && is_numeric($_GET['id_comprobante'])) {
    $id_comprobante = $_GET['id_comprobante'];
} else {
    // Si 'id_comprobante' no está presente o no es numérico, manejar el error o asignar un valor predeterminado
    $id_comprobante = 'ID de comprobante no definido o inválido';
}

// Ahora puedes usar $id_comprobante en tu script
//echo "El ID del comprobante es: " . $id_comprobante;


// Consulta para obtener el listado de notas de despacho
$sql_transaccioneslist = "SELECT 
                            c.id_comprobante,
                            tc.id_tipocomprobante,
                            tc.name_tipocomprobante,
                            c.num_comprobante,
                            c.fecha_comprobante,
                            c.hora_comprobante,
                            p.id_persona,
                            p.name_persona,
                            c.descripcion
                          FROM tb_comprobantes c
                          JOIN tb_transacciones t ON c.id_comprobante = t.id_comprobante
                          JOIN tb_detalletransacciones dt ON c.id_comprobante = dt.id_comprobante
                          JOIN tb_tipocomprobante tc ON c.id_tipocomprobante = tc.id_tipocomprobante
                          JOIN tb_personas p ON t.id_persona = p.id_persona
                          JOIN tb_subcuentas sc ON t.id_subCuenta = sc.id_subCuenta
                          WHERE c.id_comprobante = :id_comprobante
                          GROUP BY c.id_comprobante";
$query_transaccioneslist = $pdo->prepare($sql_transaccioneslist);
$query_transaccioneslist->bindParam(':id_comprobante', $id_comprobante, PDO::PARAM_INT);
$query_transaccioneslist->execute();
$transaccioneslist_datos = $query_transaccioneslist->fetchAll(PDO::FETCH_ASSOC);


foreach ($transaccioneslist_datos as $transaccioneslist_dato) {
    $id_comprobante = $transaccioneslist_dato['id_comprobante'];
    $id_tipocomprobante = $transaccioneslist_dato['id_tipocomprobante'];
    $name_tipocomprobante = $transaccioneslist_dato['name_tipocomprobante'];
    $num_comprobante = $transaccioneslist_dato['num_comprobante'];
    $fecha_comprobante = $transaccioneslist_dato['fecha_comprobante'];
    $hora_comprobante = $transaccioneslist_dato['hora_comprobante'];
    $id_persona = $transaccioneslist_dato['id_persona'];
    $name_persona = $transaccioneslist_dato['name_persona'];
    $descripcionC = $transaccioneslist_dato['descripcion'];
}

// Consulta para obtener transacciones de cuentas
$sql_transaccionescuentas = "SELECT * FROM `tb_transacciones` WHERE id_comprobante = :id_comprobante";
$query_transacccionescuentas = $pdo->prepare($sql_transaccionescuentas);
$query_transacccionescuentas->bindParam(':id_comprobante', $id_comprobante, PDO::PARAM_INT);
$query_transacccionescuentas->execute();
$transacccionescuentas_datos = $query_transacccionescuentas->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener detalles de las transacciones
$sql_detalletransacciones = "SELECT a.*, b.name_tipoProducto 
                             FROM tb_detalletransacciones AS a
                             JOIN tb_tipoproducto AS b ON a.id_tipoProducto = b.id_tipoProducto
                             WHERE a.id_comprobante = :id_comprobante
                             ORDER BY a.id_detalletransacciones ASC";
$query_detalletransacciones = $pdo->prepare($sql_detalletransacciones);
$query_detalletransacciones->bindParam(':id_comprobante', $id_comprobante, PDO::PARAM_INT);
$query_detalletransacciones->execute();
$detalletransacciones_datos = $query_detalletransacciones->fetchAll(PDO::FETCH_ASSOC);


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(215, 279), PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema avicola');
$pdf->SetTitle('Kardex');
$pdf->SetSubject('Kardex');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

//Desactiva el encabezado y pie de pagina
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

$fecha_comprobante_formateada = date('d/m/Y', strtotime($fecha_comprobante));
$total = 0;


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
<td style="text-align:center; font-size: 15px; width:380px"><b style="color: #113563;">NOTA DE DESPACHO</b><br><br>
<b style="color: #DD2E44;">Nro: 00' . $num_comprobante . '</b>
</td>
<td style="text-align:center; font-size:15px; width:130px; color: #113563;"><b>FECHA:</b>' . $fecha_comprobante_formateada . '</td>
</tr>
</table>
<br>
<p style="text-align:center; font-size:18px; margin-top: 0px;color: #113563;"><b>DETALLE</b></p>
<div style="font-size:10px">
    <table>
        <tr>
            <td>
                <b>CLIENTE: </b>' . $name_persona . '
            </td>
        </tr>
        <br>
        <tr>
            <td>
                <b>DESCRIPCION: </b>' . $descripcionC . '
            </td>
        </tr>
    </table>
</div>
<table cellpadding="6" cellspacing="0" width="100%" style="margin-top:6px; font-size:10px; border-collapse:collapse;">
        <thead>
                <tr style="background-color:#f6f8fb;">
                        <th style="width:35px; border:0.3px solid #bbb; padding:6px; text-align:center;"><b>Nro</b></th>
                        <th style="width:100px; border:0.3px solid #bbb; padding:6px; text-align:left;"><b>Tipo</b></th>
                        <th style="width:160px; border:0.3px solid #bbb; padding:6px; text-align:left;"><b>Descripcion</b></th>
                        <th style="width:65px; border:0.3px solid #bbb; padding:6px; text-align:center;"><b>NroCajas</b></th>
                        <th style="width:75px; border:0.3px solid #bbb; padding:6px; text-align:right;"><b>Peso/Bruto</b></th>
                        <th style="width:75px; border:0.3px solid #bbb; padding:6px; text-align:right;"><b>Peso/Neto</b></th>
                        <th style="width:75px; border:0.3px solid #bbb; padding:6px; text-align:right;"><b>Precio</b></th>
                        <th style="width:75px; border:0.3px solid #bbb; padding:6px; text-align:right;"><b>SubTotal</b></th>
                </tr>
        </thead>
        <tbody>';

// Asumiendo que tienes los datos en $detalletransacciones_datos
foreach ($detalletransacciones_datos as $index => $detalle) {
    $total += (float)($detalle['subTotal'] ?? 0);
    $rowBg = ($index % 2 === 0) ? 'background-color:#ffffff;' : 'background-color:#fbfcfe;';

    $pesoB = number_format((float)($detalle['pesoB_kg'] ?? 0), 2, '.', ',');
    $pesoN = number_format((float)($detalle['pesoN_kg'] ?? 0), 2, '.', ',');
    $precio = number_format((float)($detalle['precio'] ?? 0), 2, '.', ',');
    $subTotal = number_format((float)($detalle['subTotal'] ?? 0), 2, '.', ',');

    $html .= '<tr style="' . $rowBg . '">'
        . '<td style="width:35px; border:0.3px solid #bbb; padding:6px; text-align:center;">' . ($index + 1) . '</td>'
        . '<td style="width:100px; border:0.3px solid #bbb; padding:6px;">' . htmlspecialchars($detalle['name_tipoProducto'], ENT_QUOTES, 'UTF-8') . '</td>'
        . '<td style="width:160px; border:0.3px solid #bbb; padding:6px;">' . htmlspecialchars($detalle['descripcion'], ENT_QUOTES, 'UTF-8') . '</td>'
        . '<td style="width:65px; border:0.3px solid #bbb; padding:6px; text-align:center;">' . htmlspecialchars((string)($detalle['cantidadCajas'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>'
        . '<td style="width:75px; border:0.3px solid #bbb; padding:6px; text-align:right;">' . $pesoB . '</td>'
        . '<td style="width:75px; border:0.3px solid #bbb; padding:6px; text-align:right;">' . $pesoN . '</td>'
        . '<td style="width:75px; border:0.3px solid #bbb; padding:6px; text-align:right;">' . $precio . '</td>'
        . '<td style="width:75px; border:0.3px solid #bbb; padding:6px; text-align:right;">' . $subTotal . '</td>'
    . '</tr>';
}
$html .= '<tr>'
        . '<td colspan="7" style="text-align: right; border:0.3px solid #bbb; padding:6px; background-color:#f6f8fb;"><strong>Total Bs: </strong></td>'
        . '<td style="text-align: right; border:0.3px solid #bbb; padding:6px;"><strong>' . number_format($total, 2, '.', ',') . '</strong></td>'
.'</tr>';
// Cerrar tbody y la tabla
$html .= '</tbody>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

// Título: Registro contable (alineado a la izquierda, mismo tamaño que la tabla)
$titleHtml = '<p style="text-align:left; font-size:11px; margin-top:8px; margin-bottom:4px;"><b>Registro contable</b></p>';
$pdf->writeHTML($titleHtml, true, false, true, false, '');

/* ====== Tabla contable (mostrar cómo se registró contablemente) ====== */
$qd = $pdo->prepare("SELECT
        s.path AS codigo_contable,
        s.name_subCuenta AS cuenta,
        t.debe, t.haber,
        t.descripcion AS detalle,
        p.name_persona AS persona
    FROM tb_transacciones t
    JOIN tb_subcuentas s ON t.id_subCuenta = s.id_subCuenta
    LEFT JOIN tb_personas p ON t.id_persona = p.id_persona
    WHERE t.id_comprobante = :id
    ORDER BY s.path, s.name_subCuenta
");
$qd->execute([':id' => $id_comprobante]);
$rows = $qd->fetchAll(PDO::FETCH_ASSOC);

$sumDebe = 0.0;
$sumHaber = 0.0;
foreach ($rows as $r) {
        $sumDebe  += (float)($r['debe']  ?? 0);
        $sumHaber += (float)($r['haber'] ?? 0);
}

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
        $codigo = htmlspecialchars((string)($r['codigo_contable'] ?? ''), ENT_QUOTES, 'UTF-8');
        $cuenta = htmlspecialchars((string)($r['cuenta'] ?? ''), ENT_QUOTES, 'UTF-8');
        $debe   = number_format((float)($r['debe'] ?? 0),  2, '.', ',');
        $haber  = number_format((float)($r['haber'] ?? 0), 2, '.', ',');
        $det    = htmlspecialchars((string)($r['detalle'] ?? ''), ENT_QUOTES, 'UTF-8');
        $pers   = htmlspecialchars((string)($r['persona'] ?? ''), ENT_QUOTES, 'UTF-8');

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

$pdf->writeHTML($tbl, true, false, true, false, '');

// Dibujar cajas de firma al final de la hoja (mismo estilo que la tabla)
$pdf->SetY(-60);
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

$pdf->Output('ND' . $num_comprobante . '.pdf', 'I');
