<?php
// controllers/informes/saldoclientestipo.php (o el que uses)
try {
    $sql = "SELECT id_tipoPersona, name_tipoPersona, descripcion FROM tb_tipopersonas";
    $st  = $pdo->prepare($sql);
    $st->execute();
    $tipopersonas_datos = $st->fetchAll(PDO::FETCH_ASSOC);
    // Mapa id => nombre (útil para la vista)
    $tipopersonas_map = array_column($tipopersonas_datos, 'name_tipoPersona', 'id_tipoPersona');
} catch (PDOException $e) {
    error_log($e->getMessage());
    $tipopersonas_datos = [];
    $tipopersonas_map   = [];
}
