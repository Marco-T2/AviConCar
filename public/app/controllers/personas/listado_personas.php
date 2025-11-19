<?php
// app/controllers/personas/listado_personas.php
try {
    // helper: check table existence
    $tableExists = function(string $table) use ($pdo) : bool {
      try{
        $st = $pdo->prepare("SHOW TABLES LIKE ?");
        $st->execute([$table]);
        return (bool)$st->fetchColumn();
      }catch(Throwable $e){ return false; }
    };

    // detect optional columns and build a compact movement summary
    // detect column name for movimiento_caja_linea
    $caja_col = null;
    if ($tableExists('movimiento_caja_linea')) {
      try{
        $stCol = $pdo->prepare("SHOW COLUMNS FROM movimiento_caja_linea LIKE ?");
        $stCol->execute(['persona_id']);
        if ($stCol->fetchColumn()) $caja_col = 'persona_id';
        else { $stCol->execute(['id_persona']); if ($stCol->fetchColumn()) $caja_col = 'id_persona'; }
      }catch(Throwable $e){ $caja_col = null; }
    }

    // detect column name for tb_detallecomprobantes
    $detalle_col = null;
    if ($tableExists('tb_detallecomprobantes')) {
      try{
        $stCol2 = $pdo->prepare("SHOW COLUMNS FROM tb_detallecomprobantes LIKE ?");
        $stCol2->execute(['id_persona']);
        if ($stCol2->fetchColumn()) $detalle_col = 'id_persona';
        else { $stCol2->execute(['persona_id']); if ($stCol2->fetchColumn()) $detalle_col = 'persona_id'; }
      }catch(Throwable $e){ $detalle_col = null; }
    }

    $trans_sub = "(SELECT COUNT(*) FROM tb_transacciones t WHERE t.id_persona = p.id_persona)";
    $cajas_sub = $caja_col ? "(SELECT COUNT(*) FROM movimiento_caja_linea mcl WHERE mcl.`" . $caja_col . "` = p.id_persona)" : "0";
    $detalle_sub = $detalle_col ? "(SELECT COUNT(*) FROM tb_detallecomprobantes d WHERE d.`" . $detalle_col . "` = p.id_persona)" : "0";

    $sql = "
      SELECT
        p.id_persona,
        p.name_persona,
        p.direccion,
        p.celular,
        p.descripcion,
        p.id_tipoPersona,
        tp.name_tipoPersona,
        CONCAT('Trans:', " . $trans_sub . ", ' • Cajas:', " . $cajas_sub . ", ' • Otros:', " . $detalle_sub . ") AS mov_summary,
        -- tipos adicionales desde pivot (subconsulta para compatibilidad con ONLY_FULL_GROUP_BY)
        (SELECT GROUP_CONCAT(DISTINCT tps.name_tipoPersona SEPARATOR ', ')
           FROM tb_persona_tipos pt2
           JOIN tb_tipopersonas tps ON tps.id_tipoPersona = pt2.id_tipoPersona
          WHERE pt2.id_persona = p.id_persona AND pt2.id_tipoPersona <> p.id_tipoPersona
        ) AS tipos_extra,
        -- tags (subconsulta)
        (SELECT GROUP_CONCAT(DISTINCT tg.tag SEPARATOR ', ')
           FROM tb_persona_tags ptg2
           JOIN tb_tags tg ON tg.id = ptg2.tag_id
          WHERE ptg2.id_persona = p.id_persona
        ) AS tags
      FROM tb_personas p
      LEFT JOIN tb_tipopersonas tp ON tp.id_tipoPersona = p.id_tipoPersona
      ORDER BY p.name_persona ASC
    ";
    $st = $pdo->prepare($sql);
    $st->execute();
    $personas_datos = $st->fetchAll(PDO::FETCH_ASSOC);

    // Auto-detect any table columns that look like persona foreign keys (column name contains 'persona')
    $cols = [];
    try{
      $colStmt = $pdo->prepare("SELECT table_name, column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND column_name LIKE ?");
      $colStmt->execute(['%persona%']);
      while($r = $colStmt->fetch(PDO::FETCH_ASSOC)){
        // skip the main personas table itself
        if ($r['table_name'] === 'tb_personas') continue;
        $cols[] = $r;
      }
    }catch(Throwable $e){ $cols = []; }

    // prepare count statements for each detected table/column
    $checkStmts = [];
    foreach($cols as $c){
      $t = $c['table_name']; $col = $c['column_name'];
      // safe because names come from information_schema; wrap with backticks
      $sqlCount = "SELECT COUNT(*) FROM `" . $t . "` WHERE `" . $col . "` = ?";
      try{ $stmtc = $pdo->prepare($sqlCount); $checkStmts[] = ['table'=>$t,'column'=>$col,'stmt'=>$stmtc]; }catch(Throwable $e){ /* ignore */ }
    }

    // friendly labels for common tables
    $labels = [
      'tb_transacciones' => 'Transacciones',
      'movimiento_caja_linea' => 'Cajas',
      'movimiento_caja_cantidad' => 'CajasCant',
      'tb_detallecomprobantes' => 'Detalle',
      'tb_persona_tipos' => 'Tipos',
      'tb_persona_tags' => 'Tags'
    ];

    // For each persona row compute a compact movement summary from detected tables
    foreach($personas_datos as &$p){
      $pid = (int)$p['id_persona'];
      $parts = [];
      $total = 0;
      foreach($checkStmts as $ch){
        try{
          $ch['stmt']->execute([$pid]);
          $cnt = (int)$ch['stmt']->fetchColumn();
        }catch(Throwable $e){ $cnt = 0; }
        if ($cnt > 0) {
          $tbl = $ch['table'];
          $label = $labels[$tbl] ?? ucfirst(str_replace(['_','tb-'],' ', $tbl));
          $parts[] = $label . ':' . $cnt;
          $total += $cnt;
        }
      }
      // expose caja count specifically for UI adjustment
      $p['cajas_count'] = 0;
      foreach($checkStmts as $ch){
        if ($ch['table'] === 'movimiento_caja_linea'){
          try{ $ch['stmt']->execute([$pid]); $p['cajas_count'] = (int)$ch['stmt']->fetchColumn(); }catch(Throwable $e){ $p['cajas_count'] = 0; }
          break;
        }
      }
      $p['mov_summary'] = implode(' • ', $parts);
      $p['has_mov'] = $total > 1 ? 1 : 0;
      $p['total_assoc'] = $total;
    }
    unset($p);
  } catch (PDOException $e) {
    error_log($e->getMessage());
    $personas_datos = [];
  }
