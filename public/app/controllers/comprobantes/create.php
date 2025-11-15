<?php
declare(strict_types=1);
header('Content-Type: application/json');

include('../../config.php'); // Debe exponer $pdo y GESTION_ACTIVA

function insertarComprobante(array $data, PDO $pdo): array {
    // 0) Validación mínima del body
    foreach (['id_tipocomprobante','num_comprobante','fecha_comprobante','hora_comprobante','descripcion','id_usuario','detalles_comprobante'] as $k) {
        if (!isset($data[$k])) return ['ok'=>false,'error'=>"Falta campo: $k"];
    }

    $detalles = $data['detalles_comprobante'];
    if (!is_array($detalles) || count($detalles) < 2) {
        return ['ok'=>false,'error'=>'Debe registrar al menos dos líneas.'];
    }

    // 1) Normalizar líneas y validar cuadre
    $debeTotal = 0.0; $haberTotal = 0.0;
    foreach ($detalles as $i => &$d) {
        // Acepta id_subcuenta o id_subCuenta desde la vista y normaliza a entero
        $d['id_subCuenta'] = (int)($d['id_subCuenta'] ?? $d['id_subcuenta'] ?? 0);
        $d['id_persona']   = isset($d['id_persona']) && $d['id_persona'] !== '' ? (int)$d['id_persona'] : null;
        $d['debe']         = round((float)($d['debe']  ?? 0), 2);
        $d['haber']        = round((float)($d['haber'] ?? 0), 2);
        $d['descripcion']  = trim((string)($d['descripcion'] ?? ''));

        if ($d['id_subCuenta'] <= 0) return ['ok'=>false,'error'=>"Línea ".($i+1).": id_subCuenta inválido"];
        $ambos   = ($d['debe'] > 0 && $d['haber'] > 0);
        $ninguno = ($d['debe'] <= 0 && $d['haber'] <= 0);
        if ($ambos || $ninguno) return ['ok'=>false,'error'=>"Línea ".($i+1).": ponga importe solo en Debe o solo en Haber"];

        $debeTotal  += $d['debe'];
        $haberTotal += $d['haber'];
    }
    unset($d); // ¡Importante al usar foreach por referencia!

    if (round($debeTotal,2) !== round($haberTotal,2)) {
        return ['ok'=>false,'error'=>'El asiento no cuadra (Debe ≠ Haber).'];
    }

    // 1.1) Al menos una línea al Debe y una al Haber (con epsilon)
    $eps = 0.00001;
    $lineasDebe = 0; $lineasHaber = 0;
    foreach ($detalles as $d) {
        if ((float)$d['debe']  > $eps) $lineasDebe++;
        if ((float)$d['haber'] > $eps) $lineasHaber++;
    }
    if ($lineasDebe === 0 || $lineasHaber === 0) {
        return ['ok'=>false,'error'=>'Debe existir al menos una línea al Debe y una al Haber.'];
    }

    // 2) Validadores
    $qSub = $pdo->prepare("SELECT id_subCuenta, path FROM tb_subcuentas WHERE id_subCuenta = :id");
    $qPer = $pdo->prepare("SELECT id_persona FROM tb_personas   WHERE id_persona   = :id");

    try {
        $pdo->beginTransaction();

        // 3) Insertar cabecera
        $sqlC = "INSERT INTO tb_comprobantes
                 (id_tipocomprobante, num_comprobante, fecha_comprobante, hora_comprobante, descripcion, id_usuario, id_gestion)
                 VALUES (:tc, :num, :fec, :hor, :glosa, :usr, :gest)";
        $stmtC = $pdo->prepare($sqlC);
        $stmtC->execute([
            ':tc'   => (int)$data['id_tipocomprobante'],
            ':num'  => (int)$data['num_comprobante'],
            ':fec'  => $data['fecha_comprobante'],
            ':hor'  => $data['hora_comprobante'],
            ':glosa'=> $data['descripcion'],
            ':usr'  => (int)$data['id_usuario'],
            ':gest' => (int)GESTION_ACTIVA
        ]);

        $id_comprobante = (int)$pdo->lastInsertId();
        if ($id_comprobante <= 0) throw new RuntimeException('No se obtuvo id_comprobante.');

        // 4) Insertar líneas
        $insT = $pdo->prepare("INSERT INTO tb_transacciones
            (id_comprobante, id_subCuenta, debe, haber, descripcion, id_persona)
            VALUES (:comp, :subc, :debe, :haber, :desc, :per)");

        foreach ($detalles as $i => $d) {
            // Validar subcuenta
            $qSub->execute([':id'=>$d['id_subCuenta']]);
            $sub = $qSub->fetch(PDO::FETCH_ASSOC);
            if (!$sub) throw new RuntimeException("Línea ".($i+1).": subcuenta inexistente ({$d['id_subCuenta']}).");

            // (Opcional) exigir persona para paths de terceros (ej. 1.1.2.1%)
            $requiereTercero = (strpos((string)$sub['path'], '1.1.2.1') === 0);
            if ($requiereTercero && empty($d['id_persona'])) {
                throw new RuntimeException("Línea ".($i+1).": la subcuenta {$sub['path']} requiere persona.");
            }
            if (!empty($d['id_persona'])) {
                $qPer->execute([':id'=>$d['id_persona']]);
                if (!$qPer->fetch(PDO::FETCH_ASSOC)) {
                    throw new RuntimeException("Línea ".($i+1).": persona inexistente ({$d['id_persona']}).");
                }
            }

            $insT->execute([
                ':comp' => $id_comprobante,
                ':subc' => $d['id_subCuenta'],   // <-- nombre de columna en tu BD
                ':debe' => $d['debe'],
                ':haber'=> $d['haber'],
                ':desc' => $d['descripcion'],
                ':per'  => $d['id_persona']
            ]);
        }

        $pdo->commit();
        return ['ok'=>true,'id_comprobante'=>$id_comprobante];

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return ['ok'=>false,'error'=>$e->getMessage()];
    }
}

// ---------------------- Router simple ----------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success'=>false,'error'=>'Método no permitido']);
    exit;
}

$raw = file_get_contents('php://input');
// error_log("POST crear comprobante: ".$raw); // <-- habilita temporalmente si quieres ver el JSON
$data = json_decode($raw, true);
if ($data === null) {
    echo json_encode(['success'=>false,'error'=>'JSON inválido']);
    exit;
}

$res = insertarComprobante($data, $pdo);
echo json_encode(['success'=>$res['ok']] + $res);
