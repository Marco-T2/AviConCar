<?php
// Este archivo asume que ya se incluyó config.php y existe $pdo (PDO)

if (!isset($_GET['id'])) {
  http_response_code(400);
  die('Falta parámetro id');
}

$id_comprobante_get = (int)$_GET['id'];

// 1) CABECERA: sin join a transacciones para evitar duplicados
$sql_comprobantes = "
  SELECT
    c.id_comprobante,
    c.id_tipocomprobante,
    c.num_comprobante,
    c.fecha_comprobante,
    c.hora_comprobante,
    c.descripcion,
    c.id_usuario,
    c.id_gestion,
    tc.name_tipocomprobante
  FROM tb_comprobantes c
  INNER JOIN tb_tipocomprobante tc
    ON c.id_tipocomprobante = tc.id_tipocomprobante
  WHERE c.id_comprobante = :id_comprobante
  LIMIT 1
";
$qCab = $pdo->prepare($sql_comprobantes);
$qCab->execute([':id_comprobante' => $id_comprobante_get]);
$cab = $qCab->fetch(PDO::FETCH_ASSOC);

if (!$cab) {
  http_response_code(404);
  die('Comprobante no encontrado');
}

// Exponer variables para la vista
$id_comprobante     = (int)$cab['id_comprobante'];
$id_tipocomprobante = (int)$cab['id_tipocomprobante'];
$num_comprobante    = (int)$cab['num_comprobante'];
$fecha_comprobante  = $cab['fecha_comprobante'];
$hora_comprobante   = $cab['hora_comprobante'];
$descripcionC       = $cab['descripcion'];
// $id_usuario ya viene de sesión; aquí dejamos disponible el de la cabecera si se requiere:
$id_usuario_cab     = (int)$cab['id_usuario'];
$name_tipocomprobante = $cab['name_tipocomprobante'];

// 2) DETALLE: LEFT JOIN a personas para no perder filas con id_persona NULL
$sql_transacciones = "
  SELECT
    t.id_transacciones,
    t.id_subCuenta,
    t.id_persona,
    t.debe,
    t.haber,
    t.descripcion AS descripcion,
    sc.name_subCuenta AS name_subCuenta,
    p.name_persona  AS name_persona
  FROM tb_transacciones t
  INNER JOIN tb_subcuentas sc
    ON t.id_subCuenta = sc.id_subCuenta
  LEFT JOIN tb_personas p
    ON t.id_persona = p.id_persona
  WHERE t.id_comprobante = :id_comprobante
  ORDER BY t.id_transacciones ASC
";
$qDet = $pdo->prepare($sql_transacciones);
$qDet->execute([':id_comprobante' => $id_comprobante_get]);
$transacciones_datos = $qDet->fetchAll(PDO::FETCH_ASSOC);

// Si no hay detalle (poco probable), inicializa arreglo para evitar warnings en la vista
if (!$transacciones_datos) $transacciones_datos = [];
