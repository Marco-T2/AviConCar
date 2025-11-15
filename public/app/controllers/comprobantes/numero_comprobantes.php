<?php
include('../../config.php');

if (!isset($_GET['id_tipocomprobante'])) { echo 1; exit; }
$id_tipocomprobante = (int)$_GET['id_tipocomprobante'];

$stmt = $pdo->prepare(
  "SELECT IFNULL(MAX(num_comprobante),0)+1 AS next_num
   FROM tb_comprobantes
   WHERE id_tipocomprobante = :tc AND id_gestion = :g"
);
$stmt->execute([':tc'=>$id_tipocomprobante, ':g'=>GESTION_ACTIVA]);
$next = (int)$stmt->fetchColumn();

echo $next > 0 ? $next : 1;
