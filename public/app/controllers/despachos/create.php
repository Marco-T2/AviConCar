<?php
// C:\web\stack\ant\public\app\controllers\despachos\create.php
include('../../config.php');

/**
 * Defaults para subcuentas si no vienen desde la vista
 * (ajústalos a tu plan contable real).
 */
if (!defined('SUBCUENTA_CXC_ID'))    define('SUBCUENTA_CXC_ID', 15); // Cuentas por cobrar
if (!defined('SUBCUENTA_VENTAS_ID')) define('SUBCUENTA_VENTAS_ID', 16); // Ventas

/* ===================== Helpers ===================== */
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
/** Redondeo CxC: tomando x.y0, si y >= 0.7 => ceil, de lo contrario floor */
function round_cxc_from_one_decimal($x1d) {
  $floor = floor($x1d);
  $frac  = $x1d - $floor; // 0.0..0.9
  return ($frac >= 0.7) ? (int)ceil($x1d) : (int)floor($x1d);
}

/* ================ Lógica principal ================ */
function insertarDespacho(array $datos, PDO $pdo) {
  // -------- Normalización/base --------
  $id_tipocomprobante = nint($datos['id_tipocomprobante'] ?? 0);
  $num_comprobante    = nint($datos['num_comprobante'] ?? 0);
  $fecha_comprobante  = trim($datos['fecha_comprobante'] ?? date('Y-m-d'));
  $hora_comprobante   = trim($datos['hora_comprobante'] ?? date('H:i:s'));
  $id_persona         = nint($datos['id_persona'] ?? 0);
  $descripcionC       = trim($datos['descripcionC'] ?? '');
  $descripcionT       = trim($datos['descripcionT'] ?? $descripcionC);
  $id_usuario         = nint($datos['id_usuario'] ?? 0);
  $id_subcuenta1      = nint($datos['id_subcuenta1'] ?? SUBCUENTA_CXC_ID);
  $id_subcuenta2      = nint($datos['id_subcuenta2'] ?? SUBCUENTA_VENTAS_ID);
  $detalles           = $datos['detalles_transacciones'] ?? [];

  if ($id_tipocomprobante <= 0) throw new Exception('id_tipocomprobante inválido');
  if ($id_persona <= 0) throw new Exception('id_persona inválido');
  if ($id_subcuenta1 <= 0 || $id_subcuenta2 <= 0) throw new Exception('Subcuentas requeridas');
  if (!is_array($detalles) || count($detalles) === 0) throw new Exception('Sin detalles');

  // ---- Normaliza detalles y calcula total_server ----
  $detalles_norm = [];
  $total_server  = 0.0;

  foreach ($detalles as $d) {
    $tipo = nint($d['id_tipoProducto'] ?? 0);
    if ($tipo <= 0) {
      // Ignora filas sin tipo (seguridad adicional)
      continue;
    }
    $desc = trim($d['descripcionD'] ?? '');
    if ($tipo === 20 && $desc === '') $desc = 'AJUSTE'; // opcional

    $cjs = nfloat($d['cantidadCajas'] ?? 0, 0);
    $pb  = nfloat($d['pesoB_kg'] ?? 0, 2);
    $pn  = nfloat($d['pesoN_kg'] ?? 0, 2);
    // Precio puede ser negativo (descuentos por kilaje)
    $pr  = (isset($d['precio']) && $d['precio'] !== '' && is_numeric($d['precio'])) ? (float)$d['precio'] : 0.0;
    $pr  = nfloat($pr, 2);

    // Subtotal preferente: si no vino, calculamos pn * pr
    if (isset($d['subTotal']) && $d['subTotal'] !== '' && is_numeric($d['subTotal'])) {
      $st = nfloat($d['subTotal'], 2);
    } else {
      $st = nfloat($pn * $pr, 2);
    }

    $total_server += $st;

    $detalles_norm[] = [
      'id_tipoProducto' => $tipo,
      'descripcion'     => $desc,
      'cantidadCajas'   => $cjs,
      'pesoB_kg'        => $pb,
      'pesoN_kg'        => $pn,
      'precio'          => $pr,  // puede ser negativo
      'subTotal'        => $st,
    ];
  }

  if (count($detalles_norm) === 0) {
    throw new Exception('Todos los detalles carecen de tipo; no se puede crear.');
  }

  // Total exacto a 2 decimales (para reporte)
  $total_server   = nfloat($total_server, 2);
  // CxC que va a contabilidad/kardex
  $total_trunc_1d = trunc_one_decimal($total_server);
  $cxc_final      = round_cxc_from_one_decimal($total_trunc_1d);

  // ----------------- DB TX -----------------
  $pdo->beginTransaction();
  try {
    // 1) CABECERA
    $sql_comprobante = "INSERT INTO tb_comprobantes
      (id_tipocomprobante, num_comprobante, fecha_comprobante, hora_comprobante, descripcion, id_usuario, id_gestion)
      VALUES (:id_tipocomprobante, :num_comprobante, :fecha_comprobante, :hora_comprobante, :descripcion, :id_usuario, :id_gestion)";
    $st_c = $pdo->prepare($sql_comprobante);
    $st_c->execute([
      ':id_tipocomprobante' => $id_tipocomprobante,
      ':num_comprobante'    => $num_comprobante,
      ':fecha_comprobante'  => $fecha_comprobante,
      ':hora_comprobante'   => $hora_comprobante,
      ':descripcion'        => $descripcionC,
      ':id_usuario'         => $id_usuario,
      ':id_gestion'         => defined('GESTION_ACTIVA') ? GESTION_ACTIVA : null
    ]);
    if ($st_c->rowCount() === 0) throw new Exception('Fallo al insertar en tb_comprobantes');
    $id_comprobante = (int)$pdo->lastInsertId();

    // 2) TRANSACCIONES (usa CxC final)
    $sql_t = "INSERT INTO tb_transacciones
      (id_comprobante, id_subCuenta, debe, haber, descripcion, id_persona)
      VALUES
      (:idc, :sc1, :debe1, :haber1, :desc1, :idp),
      (:idc, :sc2, :debe2, :haber2, :desc2, :idp)";
    $st_t = $pdo->prepare($sql_t);
    $st_t->execute([
      ':idc'    => $id_comprobante,
      // Debe: Cuentas x Cobrar
      ':sc1'    => $id_subcuenta1,
      ':debe1'  => $cxc_final,
      ':haber1' => 0,
      ':desc1'  => $descripcionT,
      // Haber: Ventas
      ':sc2'    => $id_subcuenta2,
      ':debe2'  => 0,
      ':haber2' => $cxc_final,
      ':desc2'  => $descripcionT,
      ':idp'    => $id_persona
    ]);
    if ($st_t->rowCount() === 0) throw new Exception('Fallo al insertar en tb_transacciones');

    // 3) DETALLE
    $sql_d = "INSERT INTO tb_detalletransacciones
      (id_comprobante, id_tipoProducto, descripcion, cantidadCajas, pesoB_kg, pesoN_kg, precio, subTotal)
      VALUES (:idc, :tipo, :desc, :cjs, :pb, :pn, :pr, :st)";
    $st_d = $pdo->prepare($sql_d);

    $insertados = 0;
    foreach ($detalles_norm as $it) {
      $st_d->execute([
        ':idc'  => $id_comprobante,
        ':tipo' => $it['id_tipoProducto'],
        ':desc' => $it['descripcion'],
        ':cjs'  => $it['cantidadCajas'],
        ':pb'   => $it['pesoB_kg'],
        ':pn'   => $it['pesoN_kg'],
        ':pr'   => $it['precio'],
        ':st'   => $it['subTotal'],
      ]);
      $insertados += $st_d->rowCount();
    }

    $pdo->commit();
    return [
      'success'         => true,
      'id_comprobante'  => $id_comprobante,
      'rows_detalle'    => $insertados,
      'rows_trans'      => 2,
      'total_server'    => $total_server,    // suma exacta (2 dec)
      'total_trunc_1d'  => $total_trunc_1d,  // truncado a 1 decimal
      'cxc_final'       => (float)$cxc_final // entero asentado en contabilidad/kardex
    ];
  } catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error en transacción (create despacho): " . $e->getMessage());
    return ['success' => false, 'error' => $e->getMessage()];
  }
}

/* ================== Endpoint HTTP ================== */
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'error' => 'Método no permitido']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?? [];

/* Defaults backend (por si la vista no los envía) */
if (!isset($data['id_subcuenta1']) || !$data['id_subcuenta1']) $data['id_subcuenta1'] = SUBCUENTA_CXC_ID;
if (!isset($data['id_subcuenta2']) || !$data['id_subcuenta2']) $data['id_subcuenta2'] = SUBCUENTA_VENTAS_ID;

/* Validación mínima */
$requeridos = [
  'id_tipocomprobante','num_comprobante','fecha_comprobante','hora_comprobante',
  'id_persona','descripcionC','descripcionT','id_subcuenta1','id_subcuenta2',
  'id_usuario','detalles_transacciones'
];
foreach ($requeridos as $k) {
  if (!isset($data[$k])) { echo json_encode(['success'=>false,'error'=>"Falta: $k"]); exit; }
}

/* Normalizaciones básicas */
$data['id_tipocomprobante'] = nint($data['id_tipocomprobante']);
$data['num_comprobante']    = nint($data['num_comprobante']);
$data['id_persona']         = nint($data['id_persona']);
$data['id_subcuenta1']      = nint($data['id_subcuenta1']);
$data['id_subcuenta2']      = nint($data['id_subcuenta2']);
$data['id_usuario']         = nint($data['id_usuario']);

try {
  $res = insertarDespacho($data, $pdo);
  echo json_encode($res);
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
