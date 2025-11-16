<?php
header('Content-Type: application/json; charset=utf-8');

// Incluye configuración y $pdo
include_once __DIR__ . '/../../config.php';

if (!defined('GESTION_ACTIVA')) {
    echo json_encode(['error' => 'Falta GESTION_ACTIVA']);
    exit;
}

$id_gestion = (int) GESTION_ACTIVA;

$draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 0;
$start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$searchValue = trim($_POST['search']['value'] ?? '');
$desde = $_POST['desde'] ?? null;
$hasta = $_POST['hasta'] ?? null;

// Column mapping (DataTables column index -> DB expression)
$columnsMap = [
    0 => 'c.fecha_comprobante',
    1 => 'tc.name_tipocomprobante',
    2 => 'c.num_comprobante',
    3 => 'agg.name_persona',
    4 => 'c.descripcion',
    5 => 'agg.subTotal'
];

try {
    // Total records (sin filtros de fecha/búsqueda)
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM tb_comprobantes c JOIN tb_tipocomprobante tc ON c.id_tipocomprobante = tc.id_tipocomprobante WHERE c.id_gestion = :id_gestion AND tc.id_categoria = 2");
    $stmtTotal->execute([':id_gestion' => $id_gestion]);
    $recordsTotal = (int)$stmtTotal->fetchColumn();

    // Construcción de WHERE común
    $where = "c.id_gestion = :id_gestion AND tc.id_categoria = 2";
    $params = [':id_gestion' => $id_gestion];

    // Si hay búsqueda global, permitimos buscar en toda la BD (ignorar filtro de fecha)
    $applyDateFilter = ($searchValue === '' && $desde && $hasta);
    if ($applyDateFilter) {
        $where .= " AND c.fecha_comprobante BETWEEN :desde AND :hasta";
        $params[':desde'] = $desde;
        $params[':hasta'] = $hasta;
    }

    if ($searchValue !== '') {
        // Búsqueda global: no se aplica el filtro de fechas
        $where .= " AND (c.num_comprobante LIKE :s OR c.descripcion LIKE :s OR agg.name_persona LIKE :s OR tc.name_tipocomprobante LIKE :s OR c.fecha_comprobante LIKE :s)";
        $params[':s'] = "%" . $searchValue . "%";
    }

    $baseFrom = "FROM tb_comprobantes c
        JOIN tb_tipocomprobante tc ON c.id_tipocomprobante = tc.id_tipocomprobante
        JOIN (
            SELECT t.id_comprobante, MIN(p.name_persona) AS name_persona, SUM(t.debe) AS subTotal
            FROM tb_transacciones t
            JOIN tb_personas p ON p.id_persona = t.id_persona
            GROUP BY t.id_comprobante
        ) agg ON agg.id_comprobante = c.id_comprobante";

    // Registros filtrados
    $sqlFiltered = "SELECT COUNT(*) " . $baseFrom . " WHERE " . $where;
    $stmtFiltered = $pdo->prepare($sqlFiltered);
    $stmtFiltered->execute($params);
    $recordsFiltered = (int)$stmtFiltered->fetchColumn();

    // Orden
    $orderSql = "c.fecha_comprobante DESC, c.id_comprobante DESC";
    if (!empty($_POST['order'][0])) {
        $colIdx = (int)($_POST['order'][0]['column'] ?? 0);
        $dir = strtoupper($_POST['order'][0]['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        if (isset($columnsMap[$colIdx])) {
            $orderSql = $columnsMap[$colIdx] . ' ' . $dir;
        }
    }

    // Consulta principal
    $sql = "SELECT c.id_comprobante, c.fecha_comprobante, tc.name_tipocomprobante, c.num_comprobante, agg.name_persona, c.descripcion, agg.subTotal "
         . $baseFrom . " WHERE " . $where . " ORDER BY " . $orderSql . " LIMIT :start, :length";

    $stmt = $pdo->prepare($sql);
    // Bind params dinámicos
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
    $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($rows as $r) {
        $id = (int)$r['id_comprobante'];
        $fechaFmt = date('d/m/Y', strtotime($r['fecha_comprobante']));
        $monto = number_format((float)($r['subTotal'] ?? 0), 2);
        $acciones = '<center><div class="btn-group">'
            . '<button type="button" class="imprimir btn btn-warning btn-sm mb-2 mb-md-0" data-id-comprobante="' . $id . '" title="Imprimir"><i class="fa fa-print fa-sm"></i></button>'
            . '<a href="update.php?id=' . $id . '" class="btn btn-success btn-sm" title="Editar"><i class="fa fa-pencil-alt fa-sm"></i></a>'
            . '<a href="#" onclick="confirmDelete(' . $id . ');" class="btn btn-danger btn-sm" title="Eliminar"><i class="fa fa-trash fa-sm"></i></a>'
            . '</div></center>';

        $data[] = [
            'fecha' => $fechaFmt,
            'tipo' => $r['name_tipocomprobante'],
            'nro' => (int)$r['num_comprobante'],
            'cliente' => $r['name_persona'],
            'descripcion' => $r['descripcion'],
            'monto' => $monto,
            'acciones' => $acciones
        ];
    }

    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data' => $data
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta', 'detail' => $e->getMessage()]);
}
