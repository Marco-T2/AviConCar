<?php
// C:\web\stack\mp\public\app\controllers\comprobantes\update.php
require_once __DIR__ . '/../../config.php'; // Debe exponer $pdo y GESTION_ACTIVA
header('Content-Type: application/json');

function jerr($msg, $http=400){
  http_response_code($http);
  echo json_encode(['success'=>false,'error'=>$msg], JSON_UNESCAPED_UNICODE);
  exit;
}

try {
  // 1) Leer JSON
  $raw = file_get_contents('php://input');
  if ($raw === '' || $raw === false) jerr('Body vacío');
  $data = json_decode($raw, true);
  if (!is_array($data)) jerr('JSON inválido');

  // 2) Campos requeridos
  $required = ['id_comprobante','id_tipocomprobante','num_comprobante','fecha_comprobante','hora_comprobante','id_usuario','detalles_comprobante'];
  foreach ($required as $k) if (!array_key_exists($k, $data)) jerr("Falta campo requerido: $k");

  // Compatibilidad descripcion/descripcionC
  $descripcion = isset($data['descripcion']) ? $data['descripcion'] :
                 (isset($data['descripcionC']) ? $data['descripcionC'] : null);
  if ($descripcion === null) jerr('Falta campo requerido: descripcion');

  // Normalizaciones
  $idComprobante = (int)$data['id_comprobante'];
  $idTipo        = (int)$data['id_tipocomprobante'];
  $numCompNew    = (int)$data['num_comprobante'];
  $fecha         = trim($data['fecha_comprobante']);
  $hora          = trim($data['hora_comprobante']);
  $glosa         = trim($descripcion);
  $idUsuario     = (int)$data['id_usuario'];
  $detalles      = $data['detalles_comprobante'];

  if (!is_array($detalles) || count($detalles) < 2) jerr('Se requieren al menos 2 líneas de detalle.');

  // 3) Verificar existencia del comprobante
  $st = $pdo->prepare("SELECT id_comprobante, id_tipocomprobante, num_comprobante, id_gestion FROM tb_comprobantes WHERE id_comprobante=?");
  $st->execute([$idComprobante]);
  $cab = $st->fetch(PDO::FETCH_ASSOC);
  if (!$cab) jerr('Comprobante no encontrado.', 404);
  $idGestion = (int)$cab['id_gestion'];

  // (Opcional) Restringir por GESTION_ACTIVA
  if (defined('GESTION_ACTIVA') && (int)GESTION_ACTIVA !== $idGestion) {
    jerr('El comprobante pertenece a otra gestión. Cambie a esa gestión para editar.');
  }

  // 4) Unicidad de (gestion, tipo, numero) si cambió
  if ($numCompNew !== (int)$cab['num_comprobante'] || $idTipo !== (int)$cab['id_tipocomprobante']) {
    $uq = $pdo->prepare("SELECT COUNT(*) FROM tb_comprobantes 
                         WHERE id_gestion=? AND id_tipocomprobante=? AND num_comprobante=? AND id_comprobante<>?");
    $uq->execute([$idGestion, $idTipo, $numCompNew, $idComprobante]);
    if ((int)$uq->fetchColumn() > 0) jerr('Ya existe un comprobante con ese número para el tipo y gestión.');
  }

  // 5) Normalizar y validar líneas
  $sumDebe=0.0; $sumHaber=0.0; $tieneDebe=false; $tieneHaber=false;

  foreach ($detalles as $i=>&$d) {
    // Aceptar id_subcuenta o id_subCuenta
    if (!isset($d['id_subCuenta']) && isset($d['id_subcuenta'])) $d['id_subCuenta'] = $d['id_subcuenta'];
    if (!isset($d['id_subCuenta'])) jerr("[Línea ".($i+1)."] Falta id_subCuenta");

    $d['id_subCuenta'] = (int)$d['id_subCuenta'];
    $d['descripcion']  = isset($d['descripcion']) ? trim($d['descripcion']) : '';

    $debe  = isset($d['debe'])  ? round((float)$d['debe'], 2)  : 0.0;
    $haber = isset($d['haber']) ? round((float)$d['haber'], 2) : 0.0;

    if ($debe > 0 && $haber > 0) jerr("[Línea ".($i+1)."] Debe y Haber no pueden ser > 0 simultáneamente");
    if ($debe <= 0 && $haber <= 0) jerr("[Línea ".($i+1)."] Debe o Haber debe ser > 0");

    $d['debe']  = $debe;
    $d['haber'] = $haber;

    $sumDebe  += $debe;
    $sumHaber += $haber;
    if ($debe  > 0) $tieneDebe  = true;
    if ($haber > 0) $tieneHaber = true;

    // Normalizar persona
    if (array_key_exists('id_persona',$d) && $d['id_persona'] !== '' && $d['id_persona'] !== null) {
      $d['id_persona'] = (int)$d['id_persona'];
    } else {
      $d['id_persona'] = null;
    }
  }
  unset($d);

  $epsilon = 0.01;
  if (abs($sumDebe - $sumHaber) > $epsilon) {
    jerr('El comprobante no cuadra: Debe '.number_format($sumDebe,2).' ≠ Haber '.number_format($sumHaber,2));
  }
  if (!$tieneDebe || !$tieneHaber) jerr('Debe existir al menos una línea al Debe y una al Haber.');

  // 6) Verificar subcuentas y regla de terceros (path 1.1.2.1%)
  $idsSub = array_values(array_unique(array_map(fn($x)=>(int)$x['id_subCuenta'], $detalles)));
  $in = implode(',', array_fill(0, count($idsSub), '?'));
  $st = $pdo->prepare("SELECT id_subCuenta, path FROM tb_subcuentas WHERE id_subCuenta IN ($in)");
  $st->execute($idsSub);
  $map = [];
  while($r = $st->fetch(PDO::FETCH_ASSOC)) $map[(int)$r['id_subCuenta']] = $r['path'];
  foreach ($idsSub as $sid) if (!isset($map[$sid])) jerr("Subcuenta inexistente: $sid");

  $idsP = [];
  foreach ($detalles as $i=>$d) {
    $path = $map[(int)$d['id_subCuenta']];
    if (strpos($path, '1.1.2.1') === 0) {
      if ($d['id_persona'] === null) jerr("[Línea ".($i+1)."] La subcuenta {$d['id_subCuenta']} es de terceros y requiere id_persona.");
      $idsP[] = (int)$d['id_persona'];
    }
  }
  if ($idsP) {
    $idsP = array_values(array_unique($idsP));
    $inp = implode(',', array_fill(0, count($idsP), '?'));
    $stp = $pdo->prepare("SELECT id_persona FROM tb_personas WHERE id_persona IN ($inp)");
    $stp->execute($idsP);
    $ok = array_map('intval', $stp->fetchAll(PDO::FETCH_COLUMN, 0));
    foreach ($idsP as $pid) if (!in_array($pid, $ok, true)) jerr("Persona inexistente: $pid");
  }

  // 7) Transacción: UPDATE cabecera + DELETE detalle + INSERT detalle
  $pdo->beginTransaction();

  $up = $pdo->prepare("UPDATE tb_comprobantes
                       SET id_tipocomprobante=?, num_comprobante=?, fecha_comprobante=?, hora_comprobante=?, descripcion=?, id_usuario=?
                       WHERE id_comprobante=?");
  $up->execute([$idTipo, $numCompNew, $fecha, $hora, $glosa, $idUsuario, $idComprobante]);

  // Borrar detalle previo
  $pdo->prepare("DELETE FROM tb_transacciones WHERE id_comprobante=?")->execute([$idComprobante]);

  // Insertar nuevas líneas (nota: PK de detalle es id_transacciones, no lo necesitamos al insertar)
  $ins = $pdo->prepare("INSERT INTO tb_transacciones (id_comprobante, id_subCuenta, id_persona, debe, haber, descripcion)
                        VALUES (?,?,?,?,?,?)");
  foreach ($detalles as $i=>$d) {
    $ok = $ins->execute([
      $idComprobante,
      (int)$d['id_subCuenta'],
      $d['id_persona'], // puede ser null
      $d['debe'],
      $d['haber'],
      $d['descripcion']
    ]);
    if (!$ok) throw new Exception("Error al insertar línea ".($i+1));
  }

  $pdo->commit();
  echo json_encode(['success'=>true,'id_comprobante'=>$idComprobante], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
  if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
  jerr('Excepción: '.$e->getMessage(), 500);
}
