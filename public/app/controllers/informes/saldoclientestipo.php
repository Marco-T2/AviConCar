<?php
//Para obtener el listado de cuentas
// Asumiendo que GESTION_ACTIVA es una constante y ya está definida.
$gestion_activa = GESTION_ACTIVA;

$sql = "
SELECT 
  tp.id_tipoPersona,
  tp.name_tipoPersona,
  COALESCE(SUM(
    CASE 
      WHEN c.id_gestion = :g 
       AND s.path LIKE '1.1.2.1%' 
      THEN (COALESCE(t.debe,0) - COALESCE(t.haber,0))
      ELSE 0
    END
  ),0) AS saldo_total
FROM tb_tipopersonas tp
LEFT JOIN tb_personas      p ON p.id_tipoPersona = tp.id_tipoPersona
LEFT JOIN tb_transacciones t ON t.id_persona     = p.id_persona
LEFT JOIN tb_comprobantes  c ON c.id_comprobante = t.id_comprobante
LEFT JOIN tb_subcuentas    s ON s.id_subcuenta   = t.id_subcuenta
WHERE tp.id_tipoPersona IN (1,2,3,4,15)
GROUP BY tp.id_tipoPersona, tp.name_tipoPersona
ORDER BY tp.id_tipoPersona";
$stmt = $pdo->prepare($sql);
$stmt->execute([':g' => $gestion_activa]);
$sumaTotalPorTipoPersona_datos = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
$saldos_por_tipo = array_column($sumaTotalPorTipoPersona_datos, 'saldo_total', 'id_tipoPersona');


$sqlCaja = "
SELECT COALESCE(SUM(
  CASE 
    WHEN c.id_gestion = :g AND s.path = '1.1.1.1' 
    THEN (COALESCE(t.debe,0) - COALESCE(t.haber,0)) 
    ELSE 0 
  END
),0) AS saldo_total
FROM tb_transacciones t
LEFT JOIN tb_comprobantes c ON c.id_comprobante = t.id_comprobante
LEFT JOIN tb_subcuentas s   ON s.id_subcuenta   = t.id_subcuenta";
$stmt = $pdo->prepare($sqlCaja);
$stmt->execute([':g' => $gestion_activa]);
$sumaCajaGeneral_datos = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['saldo_total' => 0];


function listarSaldoClientesPorTipo(PDO $pdo, int $gestion_activa, int $tipo_objetivo): array {
  $sql = "
  SELECT
    p.id_persona,
    p.name_persona,
    MAX(c.fecha_comprobante) AS fecha_comprobante,
    COALESCE(SUM(t.debe - t.haber), 0) AS saldo
  FROM tb_personas p
  JOIN tb_transacciones t ON t.id_persona = p.id_persona
  JOIN tb_comprobantes  c ON c.id_comprobante = t.id_comprobante
                          AND c.id_gestion   = :g
  JOIN tb_subcuentas   s ON s.id_subcuenta  = t.id_subcuenta
                          AND s.path LIKE '1.1.2.1%'
  WHERE p.id_tipoPersona = :tipo
  GROUP BY p.id_persona, p.name_persona
  ORDER BY saldo DESC, name_persona ASC";
  $st = $pdo->prepare($sql);
  $st->execute([':g' => $gestion_activa, ':tipo' => $tipo_objetivo]);
  return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
