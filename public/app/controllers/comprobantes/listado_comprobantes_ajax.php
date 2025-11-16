<?php
header('Content-Type: application/json; charset=utf-8');
include_once __DIR__ . '/../../config.php';

if (!defined('GESTION_ACTIVA')) { echo json_encode(['error'=>'Falta GESTION_ACTIVA']); exit; }
$id_gestion = (int) GESTION_ACTIVA;

$draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 0;
$start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$searchValue = trim($_POST['search']['value'] ?? '');
$desde = $_POST['desde'] ?? null;
$hasta = $_POST['hasta'] ?? null;

$columnsMap = [
  0 => 'c.fecha_comprobante',
  1 => 'tc.name_tipocomprobante',
  2 => 'c.num_comprobante',
  3 => 'name_persona',
  4 => 'c.descripcion',
  5 => 'debe',
  6 => 'haber'
];

try {
  // total
  $stmtTotal = $pdo->prepare("SELECT COUNT(DISTINCT c.id_comprobante) FROM tb_comprobantes c INNER JOIN tb_tipocomprobante tc ON c.id_tipocomprobante = tc.id_tipocomprobante WHERE c.id_gestion = :id_gestion AND tc.id_categoria = 1");
  $stmtTotal->execute([':id_gestion' => $id_gestion]);
  $recordsTotal = (int)$stmtTotal->fetchColumn();

  // where base
  $where = "c.id_gestion = :id_gestion AND tc.id_categoria = 1";
  $params = [':id_gestion' => $id_gestion];

  // Apply date filter only when there's no search (global search should ignore date range)
  $applyDateFilter = ($searchValue === '' && $desde && $hasta);
  if ($applyDateFilter) {
    $where .= " AND c.fecha_comprobante BETWEEN :desde AND :hasta";
    $params[':desde'] = $desde;
    $params[':hasta'] = $hasta;
  }

  if ($searchValue !== '') {
    $where .= " AND (c.num_comprobante LIKE :s OR c.descripcion LIKE :s OR p.name_persona LIKE :s OR tc.name_tipocomprobante LIKE :s OR c.fecha_comprobante LIKE :s)";
    $params[':s'] = "%" . $searchValue . "%";
  }

  $baseFrom = "FROM tb_comprobantes c
    INNER JOIN tb_tipocomprobante tc ON c.id_tipocomprobante = tc.id_tipocomprobante
    INNER JOIN tb_transacciones t ON c.id_comprobante = t.id_comprobante
    LEFT JOIN tb_personas p ON t.id_persona = p.id_persona";

  $sqlFiltered = "SELECT COUNT(DISTINCT c.id_comprobante) " . $baseFrom . " WHERE " . $where;
  $stmtFiltered = $pdo->prepare($sqlFiltered);
  $stmtFiltered->execute($params);
  $recordsFiltered = (int)$stmtFiltered->fetchColumn();

  // Order
  $orderSql = "c.fecha_comprobante DESC, c.hora_comprobante DESC, c.id_comprobante DESC";
  if (!empty($_POST['order'][0])) {
    $colIdx = (int)($_POST['order'][0]['column'] ?? 0);
    $dir = strtoupper($_POST['order'][0]['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
    if (isset($columnsMap[$colIdx])) { $orderSql = $columnsMap[$colIdx] . ' ' . $dir; }
  }

  // Main query (grouped)
  $sql = "SELECT c.id_comprobante, c.fecha_comprobante, c.hora_comprobante, c.num_comprobante, c.descripcion, tc.name_tipocomprobante, MAX(p.name_persona) AS name_persona, SUM(t.debe) AS debe, SUM(t.haber) AS haber "
       . $baseFrom . " WHERE " . $where . " GROUP BY c.id_comprobante, c.fecha_comprobante, c.hora_comprobante, c.num_comprobante, c.descripcion, tc.name_tipocomprobante ORDER BY " . $orderSql . " LIMIT :start, :length";

  $stmt = $pdo->prepare($sql);
  foreach ($params as $k => $v) { $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR); }
  $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
  $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $data = [];
  foreach ($rows as $r) {
    $id = (int)$r['id_comprobante'];
    $fecha = date('d/m/Y', strtotime($r['fecha_comprobante']));
    $debe = number_format((float)($r['debe'] ?? 0), 2);
    $haber = number_format((float)($r['haber'] ?? 0), 2);
    $acciones = '<center><div class="btn-group">'
      . '<a href="print.php?id=' . $id . '" class="btn btn-warning btn-sm" title="Imprimir"><i class="fas fa-print fa-sm"></i></a>'
      . '<a href="update.php?id=' . $id . '" class="btn btn-success btn-sm" title="Editar"><i class="fa fa-pencil-alt fa-sm"></i></a>'
      . '<a href="#" onclick="confirmDelete(' . $id . ');" class="btn btn-danger btn-sm" title="Eliminar"><i class="fa fa-trash fa-sm"></i></a>'
      . '</div></center>';

    $data[] = [
      'fecha' => $fecha,
      'tipo' => $r['name_tipocomprobante'],
      'nro' => (int)$r['num_comprobante'],
      'cliente' => $r['name_persona'],
      'descripcion' => $r['descripcion'],
      'debe' => $debe,
      'haber' => $haber,
      'acciones' => $acciones
    ];
  }

  echo json_encode(['draw'=>$draw,'recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsFiltered,'data'=>$data]);

} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error'=>'Error en la consulta','detail'=>$e->getMessage()]);
}
