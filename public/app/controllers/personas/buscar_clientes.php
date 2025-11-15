<?php
// devolver JSON para Select2: { results:[{id,text}], pagination:{more:bool} }
header('Content-Type: application/json; charset=utf-8');

try {
    require_once('../../config.php'); // ← apunta a C:\web\stack\ant\public\app\config.php

    // parámetros Select2
    $term   = isset($_GET['term']) ? trim($_GET['term']) : '';
    $page   = max(1, (int)($_GET['page'] ?? 1));
    $limit  = 25;
    $offset = ($page - 1) * $limit;

    // si conoces el id del tipo "CLIENTE", es más rápido filtrar por id
    // $idTipoCliente = 2;

    $sql = "
    SELECT a.id_persona, a.name_persona
    FROM tb_personas a
    JOIN tb_tipopersonas b ON a.id_tipoPersona = b.id_tipoPersona
    WHERE UPPER(b.name_tipoPersona) = 'CLIENTE'
      AND (:term = '' OR a.name_persona LIKE :like)
    ORDER BY a.name_persona
    LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $like = '%'.$term.'%';
    $stmt->bindValue(':term', $term, PDO::PARAM_STR);
    $stmt->bindValue(':like', $like, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $results = array_map(function($r){
        return [
            'id'   => (int)$r['id_persona'],
            'text' => $r['id_persona'].' - '.$r['name_persona']
        ];
    }, $rows);

    $more = count($rows) === $limit;
    echo json_encode(['results' => $results, 'pagination' => ['more' => $more]]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['results'=>[], 'pagination'=>['more'=>false], 'error'=>$e->getMessage()]);
}
