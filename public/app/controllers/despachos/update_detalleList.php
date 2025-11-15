<?php
/**
 * C:\web\stack\ant\public\app\controllers\despachos\update_detalleList.php
 *
 * Loader para editar un comprobante de despacho.
 * - NO separa/filtra filas de ajuste (id_tipoProducto=20): vienen como filas normales.
 * - NO calcula $ajuste_* ni $cxc_* en PHP (la vista/JS recalcula totales y CxC).
 * - Entrega: cabecera ($..._comprobante, $id_persona, $descripcionC), transacciones ($transacccionescuentas_datos),
 *   y detalle ($detalle_rows). Además, $totalInicial y $cxc_inicial solo como valores iniciales de pantalla.
 */

// Asegura config/DB (ajusta ruta si tu estructura difiere)
require_once(__DIR__ . '/../../config.php');

// Lee ?id= del comprobante
$id_comprobante_get = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Defaults seguros
$id_comprobante       = 0;
$id_tipocomprobante   = null;
$name_tipocomprobante = '';
$num_comprobante      = '';
$fecha_comprobante    = date('Y-m-d');
$hora_comprobante     = date('H:i');
$id_persona           = null;
$name_persona         = '';
$descripcionC         = '';

$transacccionescuentas_datos = [];
$detalle_rows                = [];
$totalInicial                = 0.00; // La vista recalcula; esto es solo inicial
$cxc_inicial                 = 0.00; // La vista recalcula CxC con la regla (>0.80 redondea a entero)

if ($id_comprobante_get > 0) {
  /* ---- Cabecera, persona y tipo comprobante ---- */
  $sql_hdr = "
    SELECT 
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
    JOIN tb_tipocomprobante tc ON c.id_tipocomprobante = tc.id_tipocomprobante
    LEFT JOIN tb_transacciones t ON c.id_comprobante = t.id_comprobante
    LEFT JOIN tb_personas p ON t.id_persona = p.id_persona
    WHERE c.id_comprobante = :id
    GROUP BY c.id_comprobante
  ";
  $st = $pdo->prepare($sql_hdr);
  $st->execute([':id' => $id_comprobante_get]);
  $hdr = $st->fetch(PDO::FETCH_ASSOC) ?: [];

  $id_comprobante       = (int)($hdr['id_comprobante']       ?? 0);
  $id_tipocomprobante   = $hdr['id_tipocomprobante']         ?? null;
  $name_tipocomprobante = $hdr['name_tipocomprobante']       ?? '';
  $num_comprobante      = $hdr['num_comprobante']            ?? '';
  $fecha_comprobante    = $hdr['fecha_comprobante']          ?? date('Y-m-d');
  $hora_comprobante     = $hdr['hora_comprobante']           ?? date('H:i');
  $id_persona           = $hdr['id_persona']                 ?? null;
  $name_persona         = $hdr['name_persona']               ?? '';
  $descripcionC         = $hdr['descripcion']                ?? '';

  /* ---- Transacciones (para default de subcuentas, etc.) ---- */
  $sql_trx = "SELECT * FROM tb_transacciones WHERE id_comprobante = :id ORDER BY id_transacciones ASC";
  $stx = $pdo->prepare($sql_trx);
  $stx->execute([':id' => $id_comprobante_get]);
  $transacccionescuentas_datos = $stx->fetchAll(PDO::FETCH_ASSOC);

  /* ---- Detalle (incluye TODO, también tipo 20) ---- */
  $sql_det = "
    SELECT 
      a.*, 
      a.descripcion AS descripcion,
      b.name_tipoProducto AS name_tipoProducto
    FROM tb_detalletransacciones a
    JOIN tb_tipoproducto b ON a.id_tipoProducto = b.id_tipoProducto
    WHERE a.id_comprobante = :id
    ORDER BY a.id_detalletransacciones ASC
  ";
  $sdet = $pdo->prepare($sql_det);
  $sdet->execute([':id' => $id_comprobante_get]);
  $detalletransacciones_datos = $sdet->fetchAll(PDO::FETCH_ASSOC);

  // Normaliza/alias esperado por la vista
  $detalle_rows = [];
  $totalInicial = 0.00;

  foreach ($detalletransacciones_datos as $row) {
    // Asegura claves usadas por la vista
    $item = [
      'id_detalletransacciones' => (int)($row['id_detalletransacciones'] ?? 0),
      'id_comprobante'          => (int)($row['id_comprobante'] ?? 0),
      'id_tipoProducto'         => (int)($row['id_tipoProducto'] ?? 0),
      'name_tipoProducto'       => $row['name_tipoProducto'] ?? '',
      'descripcion'             => $row['descripcion'] ?? '',
      'cantidadCajas'           => (float)($row['cantidadCajas'] ?? 0),
      'pesoB_kg'                => (float)($row['pesoB_kg'] ?? 0),
      'pesoN_kg'                => (float)($row['pesoN_kg'] ?? 0),
      'precio'                  => (float)($row['precio'] ?? 0),
      'subTotal'                => (float)($row['subTotal'] ?? 0),
    ];
    $detalle_rows[] = $item;
    $totalInicial  += (float)$item['subTotal']; // suma de subtotales (incluye 20 si existe)
  }

  // Valor inicial para el label de CxC (la VISTA lo recalcula con su regla al cargar)
  $cxc_inicial = $totalInicial;
}

// Expuestos para la vista:
// - $id_comprobante, $id_tipocomprobante, $name_tipocomprobante, $num_comprobante,
//   $fecha_comprobante, $hora_comprobante, $id_persona, $name_persona, $descripcionC
// - $transacccionescuentas_datos, $detalle_rows
// - $totalInicial, $cxc_inicial
