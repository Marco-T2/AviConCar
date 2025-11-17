<?php
header('Content-Type: application/json; charset=utf-8');
// path: public/app/controllers/cajas/save_movimiento.php
// Guarda (crea/actualiza) un movimiento de cajas (cabecera, líneas y cantidades)

try{
  include_once __DIR__ . '/../../config.php';
}catch(Exception $e){
  echo json_encode(['ok'=>false,'error'=>'config not found']);
  exit;
}

// ensure session available for user id
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

$input = json_decode(file_get_contents('php://input'), true);
if(!$input) {
  echo json_encode(['ok'=>false,'error'=>'invalid_json']);
  exit;
}

$fecha = $input['fecha'] ?? null;
$tipo = $input['tipo'] ?? null; // RECOJO or ENTREGA
$observacion = $input['observacion'] ?? ($input['obs'] ?? null);
$replace_fecha = !empty($input['replace_fecha']);
$id = isset($input['id']) && $input['id'] ? (int)$input['id'] : null;
$filas = is_array($input['filas']) ? $input['filas'] : [];

if(!$fecha || !$tipo){
  echo json_encode(['ok'=>false,'error'=>'missing_fecha_or_tipo']);
  exit;
}

// load tipo_caja mapping codigo -> id
$mapCodigoToId = [];
$stmt = $pdo->query("SELECT id, codigo, descripcion FROM tipo_caja");
foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) $mapCodigoToId[$r['codigo']] = (int)$r['id'];

try{
  $pdo->beginTransaction();

  if($id){
    // update header
    $sql = "UPDATE movimiento_caja SET fecha = ?, tipo = ?, usuario_id = ?, observacion = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $pdo->prepare($sql)->execute([$fecha, $tipo, $user_id, $observacion, $id]);
    // delete existing lines and cantidades (simple approach)
    $del1 = $pdo->prepare("DELETE mc FROM movimiento_caja_cantidad mc JOIN movimiento_caja_linea ml ON mc.linea_id = ml.id WHERE ml.movimiento_id = ?");
    $del1->execute([$id]);
    $del2 = $pdo->prepare("DELETE FROM movimiento_caja_linea WHERE movimiento_id = ?");
    $del2->execute([$id]);
  }else{
    // if requested, delete existing movimientos for this fecha+tipo before creating a new one
    if($replace_fecha && $tipo){
      $delAll = $pdo->prepare("DELETE FROM movimiento_caja WHERE fecha = ? AND tipo = ?");
      $delAll->execute([$fecha, $tipo]);
    }
    $sql = "INSERT INTO movimiento_caja (fecha, tipo, usuario_id, observacion, created_at, updated_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    $pdo->prepare($sql)->execute([$fecha, $tipo, $user_id, $observacion]);
    $id = (int)$pdo->lastInsertId();
  }

  // accumulate totals per tipo code and separate source vs entrega totals when needed
  $totales = [];
  $sourceTotals = [];
  $entregaTotals = [];

  foreach($filas as $fila){
    $nro = isset($fila['nro']) ? (int)$fila['nro'] : null;
    $cliente = isset($fila['cliente']) ? trim($fila['cliente']) : null;
    $obs = isset($fila['obs']) ? trim($fila['obs']) : null;
    $nroDesp = isset($fila['nroDesp']) ? trim($fila['nroDesp']) : null;
    $notad = !empty($fila['notad']) ? 1 : 0;
    $foto = !empty($fila['foto']) ? 1 : 0;
    $reccans = !empty($fila['reccans']) ? 1 : 0;

    // persona_id not provided by the current form (we store cliente name in obs)
    $persona_id = null;
    // include nroDesp in obs if provided
    $obs_final_parts = [];
    if($nroDesp) $obs_final_parts[] = 'NroDesp:' . $nroDesp;
    if($cliente) $obs_final_parts[] = $cliente;
    if($obs) $obs_final_parts[] = $obs;
    $obs_final = implode(' - ', $obs_final_parts);

    $stmt = $pdo->prepare("INSERT INTO movimiento_caja_linea (movimiento_id, nro_linea, persona_id, obs, foto_flag, notad_flag, reccans_flag, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
    $stmt->execute([$id, $nro, $persona_id, $obs_final, $foto, $notad, $reccans]);
    $linea_id = (int)$pdo->lastInsertId();

    // cantidades: input may use tipo codes as keys. Map them to tipo_caja_id
    if(!empty($fila['cantidades']) && is_array($fila['cantidades'])){
      foreach($fila['cantidades'] as $tipoCode => $cantidad){
        $cantidad = (int)$cantidad;
        if($cantidad === 0) continue;
        // map code -> id
        if(!isset($mapCodigoToId[$tipoCode])){
          // ignore unknown tipo codes
          continue;
        }
        $tipo_caja_id = $mapCodigoToId[$tipoCode];
        $ins = $pdo->prepare("INSERT INTO movimiento_caja_cantidad (linea_id, tipo_caja_id, cantidad, created_at, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
        $ins->execute([$linea_id, $tipo_caja_id, $cantidad]);

        if(!isset($totales[$tipoCode])) $totales[$tipoCode] = 0;
        $totales[$tipoCode] += $cantidad;

        // classify as source or entrega based on client name markers (used by entrega page)
        // Note: SaldoDeposito-DiaHoy is visual-only and should not be treated as a source for balance validation
        $isSource = in_array($cliente, ['DespachoMatadero','SaldoDeposito','SaldoDeposito-DiaAnt','OtrosTraspasos'], true);
        if($isSource){ if(!isset($sourceTotals[$tipoCode])) $sourceTotals[$tipoCode] = 0; $sourceTotals[$tipoCode] += $cantidad; }
        else { if(!isset($entregaTotals[$tipoCode])) $entregaTotals[$tipoCode] = 0; $entregaTotals[$tipoCode] += $cantidad; }
      }
    }
  }

  // If this is an ENTREGA, ensure sourceTotals - entregaTotals == 0 for every tipo
  if($tipo === 'ENTREGA'){
    // union of keys
    $allKeys = array_unique(array_merge(array_keys($sourceTotals), array_keys($entregaTotals)));
    foreach($allKeys as $k){
      $s = $sourceTotals[$k] ?? 0;
      $e = $entregaTotals[$k] ?? 0;
      if(($s - $e) !== 0){
        throw new Exception("Balance error for tipo {$k}: sources({$s}) - entregas({$e}) != 0");
      }
    }
  }

  // update totales_json in header
  $upd = $pdo->prepare("UPDATE movimiento_caja SET totales_json = ? WHERE id = ?");
  $upd->execute([json_encode($totales, JSON_UNESCAPED_UNICODE), $id]);

  $pdo->commit();

  echo json_encode(['ok'=>true,'id'=>$id,'totales'=>$totales]);
  exit;

}catch(Exception $e){
  if($pdo && $pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
  exit;
}

?>