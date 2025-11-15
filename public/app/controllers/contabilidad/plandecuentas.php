<?php
// C:\web\stack\mp\public\app\controllers\contabilidad\plandecuentas.php
// Calcula saldos bottom-up en UNA sola consulta y expone funciones rápidas para la vista.

function pc_cargarEstructuraYBalances() {
    // Usa $pdo global
    global $pdo;

    // Trae TODO de una sola vez (estructura + saldo por subcuenta).
    // Filtra por GESTION_ACTIVA para no mezclar gestiones
    $sql = "
      SELECT
        g.id_grupo, g.name_grupo, g.path AS grupo_path,
        sg.id_subgrupo, sg.name_subgrupo, sg.path AS subgrupo_path,
        c.id_cuenta, c.name_cuenta, c.path AS cuenta_path,
        sc.id_subCuenta, sc.name_subCuenta, sc.path AS subcuenta_path,
        COALESCE(SUM(t.debe),0)  AS debe,
        COALESCE(SUM(t.haber),0) AS haber
      FROM tb_grupos g
      JOIN tb_subgrupos sg ON sg.id_grupo = g.id_grupo
      JOIN tb_cuentas c    ON c.id_subgrupo = sg.id_subgrupo
      JOIN tb_subcuentas sc ON sc.id_cuenta = c.id_cuenta
      LEFT JOIN tb_transacciones t
        ON t.id_subCuenta = sc.id_subCuenta
      LEFT JOIN tb_comprobantes cmp
        ON cmp.id_comprobante = t.id_comprobante
       AND cmp.id_gestion = :id_gestion
      GROUP BY
        g.id_grupo, g.name_grupo, g.path,
        sg.id_subgrupo, sg.name_subgrupo, sg.path,
        c.id_cuenta, c.name_cuenta, c.path,
        sc.id_subCuenta, sc.name_subCuenta, sc.path
      ORDER BY g.path, sg.path, c.path, sc.path
    ";
    $st = $pdo->prepare($sql);
    $st->execute([':id_gestion' => (int)GESTION_ACTIVA]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    // Índices y acumuladores
    $byGrupo = [];       // id_grupo => datos + total
    $bySubgrupo = [];    // id_subgrupo => datos + total
    $byCuenta = [];      // id_cuenta => datos + total
    $bySubcuenta = [];   // id_subCuenta => datos + total

    foreach ($rows as $r) {
        $saldo = ((float)$r['debe']) - ((float)$r['haber']);

        // Subcuenta
        $sid = (int)$r['id_subCuenta'];
        if (!isset($bySubcuenta[$sid])) {
            $bySubcuenta[$sid] = [
                'id_subCuenta'   => $sid,
                'name_subCuenta' => $r['name_subCuenta'],
                'path'           => $r['subcuenta_path'],
                'id_cuenta'      => (int)$r['id_cuenta'],
                'total'          => 0.0,
            ];
        }
        $bySubcuenta[$sid]['total'] += $saldo;

        // Cuenta
        $cid = (int)$r['id_cuenta'];
        if (!isset($byCuenta[$cid])) {
            $byCuenta[$cid] = [
                'id_cuenta'   => $cid,
                'name_cuenta' => $r['name_cuenta'],
                'path'        => $r['cuenta_path'],
                'id_subgrupo' => (int)$r['id_subgrupo'],
                'total'       => 0.0,
                'subcuentas'  => [], // ids
            ];
        }
        $byCuenta[$cid]['total'] += $saldo;
        $byCuenta[$cid]['subcuentas'][$sid] = true;

        // Subgrupo
        $sgid = (int)$r['id_subgrupo'];
        if (!isset($bySubgrupo[$sgid])) {
            $bySubgrupo[$sgid] = [
                'id_subgrupo'   => $sgid,
                'name_subgrupo' => $r['name_subgrupo'],
                'path'          => $r['subgrupo_path'],
                'id_grupo'      => (int)$r['id_grupo'],
                'total'         => 0.0,
                'cuentas'       => [], // ids
            ];
        }
        $bySubgrupo[$sgid]['total'] += $saldo;
        $bySubgrupo[$sgid]['cuentas'][$cid] = true;

        // Grupo
        $gid = (int)$r['id_grupo'];
        if (!isset($byGrupo[$gid])) {
            $byGrupo[$gid] = [
                'id_grupo'   => $gid,
                'name_grupo' => $r['name_grupo'],
                'path'       => $r['grupo_path'],
                'total'      => 0.0,
                'subgrupos'  => [], // ids
            ];
        }
        $byGrupo[$gid]['total'] += $saldo;
        $byGrupo[$gid]['subgrupos'][$sgid] = true;
    }

    // Persistimos en variables globales para las funciones
    $GLOBALS['PC_BY_GRUPO']     = $byGrupo;
    $GLOBALS['PC_BY_SUBGRUPO']  = $bySubgrupo;
    $GLOBALS['PC_BY_CUENTA']    = $byCuenta;
    $GLOBALS['PC_BY_SUBCUENTA'] = $bySubcuenta;
}

// -------- API para la vista (sin queries adicionales) --------
function obtenerGrupos() {
    if (!isset($GLOBALS['PC_BY_GRUPO'])) pc_cargarEstructuraYBalances();
    // Orden por path ascendente (opcional)
    $items = array_values($GLOBALS['PC_BY_GRUPO']);
    usort($items, fn($a,$b)=>strcmp($a['path'],$b['path']));
    return $items;
}

function obtenerSubgruposPorGrupo($id_grupo) {
    if (!isset($GLOBALS['PC_BY_SUBGRUPO']) || !isset($GLOBALS['PC_BY_GRUPO'])) pc_cargarEstructuraYBalances();
    $out = [];
    foreach ($GLOBALS['PC_BY_SUBGRUPO'] as $sg) {
        if ((int)$sg['id_grupo'] === (int)$id_grupo) $out[] = $sg;
    }
    usort($out, fn($a,$b)=>strcmp($a['path'],$b['path']));
    return $out;
}

function obtenerCuentasPorSubgrupo($id_subgrupo) {
    if (!isset($GLOBALS['PC_BY_CUENTA'])) pc_cargarEstructuraYBalances();
    $out = [];
    foreach ($GLOBALS['PC_BY_CUENTA'] as $c) {
        if ((int)$c['id_subgrupo'] === (int)$id_subgrupo) $out[] = $c;
    }
    usort($out, fn($a,$b)=>strcmp($a['path'],$b['path']));
    return $out;
}

function obtenerSubcuentasPorCuenta($id_cuenta) {
    if (!isset($GLOBALS['PC_BY_SUBCUENTA'])) pc_cargarEstructuraYBalances();
    $out = [];
    foreach ($GLOBALS['PC_BY_SUBCUENTA'] as $sc) {
        if ((int)$sc['id_cuenta'] === (int)$id_cuenta) $out[] = $sc;
    }
    usort($out, fn($a,$b)=>strcmp($a['path'],$b['path']));
    return $out;
}
