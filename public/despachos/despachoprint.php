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
<div>
    <table>
        <tr>
            <td>
                <b>CLIENTE: </b>' . $name_persona . '
            </td>
        </tr>
        <tr>
            <td>
                <b>DESCRIPCION: </b>' . $descripcionC . '
            </td>
        </tr>
    </table>
</div>
<table border="1" cellpadding="0" cellspacing="0" align="center">
    <thead>
        <tr style="background-color: #f2f2f2;font-size: 14px">
            <th style="width:35px"><b>Nro</b></th>
            <th style="width:100px"><b>Tipo</b></th>
            <th style="width:160px"><b>Descripcion</b></th>
            <th style="width:65px"><b>NroCajas</b></th>
            <th style="width:75px; text-align: center;"><b>Peso/Bruto</b></th>
            <th style="width:75px; text-align: center;"><b>Peso/Neto</b></th>
            <th style="width:75px"><b>Precio</b></th>
            <th style="width:75px"><b>SubTotal</b></th>
        </tr>
    </thead>
    <tbody>';

// Asumiendo que tienes los datos en $detalletransacciones_datos
foreach ($detalletransacciones_datos as $index => $detalle) {

    $total += $detalle['subTotal'];
    $html .= '<tr style="font-size: 13px">
        <td style="width:35px">' . ($index + 1) . '</td>
        <td style="width:100px">' . htmlspecialchars($detalle['name_tipoProducto'], ENT_QUOTES, 'UTF-8') . '</td>
        <td style="width:160px">' . htmlspecialchars($detalle['descripcion'], ENT_QUOTES, 'UTF-8') . '</td>
        <td style="text-align: center;width:65px">' . $detalle['cantidadCajas'] . '</td>
        <td style="text-align: right;width:75px; padding-right: 10px">' . $detalle['pesoB_kg'] . '&nbsp;&nbsp;</td>
        <td style="text-align: right;width:75px; padding-right: 10px;">' . $detalle['pesoN_kg'] . '&nbsp;&nbsp;</td>
        <td style="text-align: right;width:75px; padding-right: 10px;">' . $detalle['precio'] . '&nbsp;&nbsp;</td>
        <td style="text-align: right;width:75px; padding-right: 10px;">' . $detalle['subTotal'] . '&nbsp;&nbsp;</td>
    </tr>';
}
$html .= '<tr>
    <td colspan="7" style="text-align: right;background-color: #f2f2f2;"><strong>Total Bs: </strong></td>
    <td style="text-align: right;"><strong>' . number_format($total, 2, '.', ',') . '</strong>&nbsp;&nbsp;</td>
</tr>';
// Cerrar tbody y la tabla
$html .= '</tbody>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('ND' . $num_comprobante . '.pdf', 'I');
