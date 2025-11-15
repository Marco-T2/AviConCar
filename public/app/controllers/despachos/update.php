<?php
/**
 * C:\web\stack\ant\public\app\controllers\despachos\update.php
 * Actualiza cabecera de comprobante, borra físico transacciones y detalle, e inserta los nuevos.
 * Soporta filas de AJUSTE (id_tipoProducto=20) como filas normales.
 * CxC (lo que va a contabilidad/kardex) = total truncado a 1 decimal y luego redondeado a entero por regla:
 *   - tomar total1d = floor(total * 10) / 10  (x.yz -> x.y0)
 *   - si primer decimal >= 0.7 -> ceil(total1d), de lo contrario floor(total1d)
 */

require_once(__DIR__ . '/../../config.php');

function nfloat($v, $dec = 2) {
  if ($v === null || $v === '' || !is_numeric($v)) return 0.0;
  return (float)number_format((float)$v, $dec, '.', '');
}
function nint($v) {
  if ($v === null || $v === '' || !is_numeric($v)) return 0;
  return (int)$v;
}

/** Trunca al primer decimal: 1886.65 => 1886.60 */
function trunc_one_decimal($x) {
  return floor(((float)$x + 1e-9) * 10.0) / 10.0;
}
/** Redondeo CxC por primer decimal: >= 0.7 -> arriba, si no -> abajo */
function round_cxc_from_one_decimal($x1d) {
  $floor = floor($x1d);
  $frac  = $x1d - $floor; // 0.0 .. 0.9
  return ($frac >= 0.7) ? (int)ceil($x1d) : (int)floor($x1d);
}

function updateComprobante($pdo, $payload) {
  // --- Extrae / normaliza payload ---
  $id_comprobante     = nint($payload['id_comprobante'] ?? 0);
  $id_tipocomprobante = nint($payload['id_tipocomprobante'] ?? 0);
  $num_comprobante    = nint($payload['num_comprobante'] ?? 0);
  $fecha_comprobante  = trim($payload['fecha_comprobante'] ?? date('Y-m-d'));
  $hora_comprobante   = trim($payload['hora_comprobante'] ?? date('H:i:s'));
  $id_persona         = nint($payload['id_persona'] ?? 0);
  $descripcionC       = trim($payload['descripcionC'] ?? '');
  $descripcionT       = trim($payload['descripcionT'] ?? $descripcionC);
  $id_subcuenta1      = nint($payload['id_subcuenta1'] ?? 0);
  $id_subcuenta2      = nint($payload['id_subcuenta2'] ?? 0);
  $id_usuario         = nint($payload['id_usuario'] ?? 0);
  $detalles           = $payload['detalles_transacciones'] ?? [];

  if ($id_comprobante <= 0) throw new Exception('id_comprobante inválido');
  if ($id_tipocomprobante <= 0) throw new Exception('id_tipocomprobante inválido');
  if ($id_subcuenta1 <= 0 || $id_subcuenta2 <= 0) throw new Exception('Subcuentas requeridas');
  if (!is_array($detalles) || count($detalles) === 0) throw new Exception('Sin detalles');

  // --- Normaliza detalles y calcula total en servidor ---
  $detalles_norm = [];
  $total_server  = 0.0;

  foreach ($detalles as $d) {
    $tipo = nint($d['id_tipoProducto'] ?? 0);
    if ($tipo <= 0) { // seguridad extra: ignora filas sin tipo
      // si quieres abortar por completo:
      // throw new Exception('Hay filas con datos sin Tipo seleccionado');
      continue;
    }
    $desc = trim($d['descripcionD'] ?? '');
    $cjs  = nfloat($d['cantidadCajas'] ?? 0, 0);
    $pb   = nfloat($d['pesoB_kg'] ?? 0, 2);
    $pn   = nfloat($d['pesoN_kg'] ?? 0, 2);
    $pr   = (isset($d['precio']) && $d['precio'] !== '' && is_numeric($d['precio'])) ? (float)$d['precio'] : 0.0; // permite negativo
    $pr   = nfloat($pr, 2);
    $st   = (isset($d['subTotal']) && $d['subTotal'] !== '' && is_numeric($d['subTotal'])) ? (float)$d['subTotal'] : null;

    // AJUSTE (20) se comporta IGUAL que los demás: subtotal preferente = pn * pr si no vino
    if ($desc === '' && $tipo === 20) $desc = 'AJUSTE';

    if ($st === null) {
      $st = nfloat($pn * $pr, 2); // siempre recalculable si faltó
    } else {
      $st = nfloat($st, 2);
    }

    $total_server += $st;

    $detalles_norm[] = [
      'id_tipoProducto' => $tipo,
      'descripcion'     => $desc,
      'cantidadCajas'   => $cjs,
      'pesoB_kg'        => $pb,
      'pesoN_kg'        => $pn,
      'precio'          => $pr,     // puede ser negativo
      'subTotal'        => $st,     // firma se mantiene
    ];
  }

  // Suma exacta a 2 decimales (para reporte)
  $total_server = nfloat($total_server, 2);

  // === Reglas de CxC para CONTABILIDAD/KARDEX ===
  $total_trunc_1d = trunc_one_decimal($total_server);         // x.yz -> x.y0
  $cxc_final      = round_cxc_from_one_decimal($total_trunc_1d); // entero segun >=0.7

  // --- Transacción DB ---
  $pdo->beginTransaction();
  try {
    // 1) Actualiza cabecera
    $sql_upd = "UPDATE tb_comprobantes
                SET id_tipocomprobante = :id_tipocomprobante,
                    num_comprobante    = :num_comprobante,
                    fecha_comprobante  = :fecha_comprobante,
                    hora_comprobante   = :hora_comprobante,
                    descripcion        = :descripcion,
                    id_usuario         = :id_usuario
                WHERE id_comprobante   = :id_comprobante";
    $st_upd = $pdo->prepare($sql_upd);
    $st_upd->execute([
      ':id_tipocomprobante' => $id_tipocomprobante,
      ':num_comprobante'    => $num_comprobante,
      ':fecha_comprobante'  => $fecha_comprobante,
      ':hora_comprobante'   => $hora_comprobante,
      ':descripcion'        => $descripcionC,
      ':id_usuario'         => $id_usuario,
      ':id_comprobante'     => $id_comprobante,
    ]);

    // 2) Borra físico transacciones y detalle previos
    $st_del_t = $pdo->prepare("DELETE FROM tb_transacciones WHERE id_comprobante = :id");
    $st_del_d = $pdo->prepare("DELETE FROM tb_detalletransacciones WHERE id_comprobante = :id");
    $st_del_t->execute([':id' => $id_comprobante]);
    $st_del_d->execute([':id' => $id_comprobante]);

    // 3) Inserta doble partida usando CxC (lo que va a contabilidad/kardex)
    $sql_ins_t = "INSERT INTO tb_transacciones (id_comprobante, id_subCuenta, debe, haber, descripcion, id_persona)
                  VALUES (:idc1, :sc1, :debe1, :haber1, :desc1, :per1),
                         (:idc2, :sc2, :debe2, :haber2, :desc2, :per2)";
    $st_t = $pdo->prepare($sql_ins_t);
    $zero = 0.00;
    $st_t->execute([
      ':idc1'   => $id_comprobante,
      ':sc1'    => $id_subcuenta1,
      ':debe1'  => $cxc_final,
      ':haber1' => $zero,
      ':desc1'  => $descripcionT,
      ':per1'   => $id_persona,

      ':idc2'   => $id_comprobante,
      ':sc2'    => $id_subcuenta2,
      ':debe2'  => $zero,
      ':haber2' => $cxc_final,
      ':desc2'  => $descripcionT,
      ':per2'   => $id_persona,
    ]);

    // 4) Inserta detalle (incluye ajustes tipo 20 guardados igual que los demás)
    $sql_ins_d = "INSERT INTO tb_detalletransacciones
                  (id_comprobante, id_tipoProducto, descripcion, cantidadCajas, pesoB_kg, pesoN_kg, precio, subTotal)
                  VALUES
                  (:idc, :tipo, :desc, :cjs, :pb, :pn, :pr, :st)";
    $st_d = $pdo->prepare($sql_ins_d);

    $insertados = 0;
    foreach ($detalles_norm as $it) {
      $st_d->execute([
        ':idc'  => $id_comprobante,
        ':tipo' => $it['id_tipoProducto'],
        ':desc' => $it['descripcion'],
        ':cjs'  => $it['cantidadCajas'],
        ':pb'   => $it['pesoB_kg'],
        ':pn'   => $it['pesoN_kg'],
        ':pr'   => $it['precio'],     // acepta negativos
        ':st'   => $it['subTotal'],
      ]);
      $insertados += $st_d->rowCount();
    }

    // 5) Commit
    $pdo->commit();

    return [
      'success'         => true,
      'total_server'    => $total_server,      // suma exacta (2 dec)
      'total_trunc_1d'  => $total_trunc_1d,    // truncado a 1 decimal
      'cxc_final'       => (float)$cxc_final,  // entero asentado en contabilidad/kardex
      'rows_detalle'    => $insertados,
      'rows_trans'      => 2,
      'id_comprobante'  => $id_comprobante,
    ];
  } catch (Exception $e) {
    $pdo->rollBack();
    error_log('[UPDATE DESPACHO] ' . $e->getMessage());
    return ['success' => false, 'error' => $e->getMessage()];
  }
}

// --- Entrada HTTP ---
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'error' => 'Método no permitido']);
  exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => 'JSON inválido']);
  exit;
}

try {
  $res = updateComprobante($pdo, $data);
  echo json_encode($res);
} catch (Exception $ex) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => $ex->getMessage()]);
}
