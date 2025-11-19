<?php
// app/controllers/personas/listado_personas.php
try {
  $sql = "
    SELECT
      p.id_persona,
      p.name_persona,
      p.direccion,
      p.celular,
      p.descripcion,
      p.id_tipoPersona,
      tp.name_tipoPersona,
      -- cantidad de movimientos para esta persona
      (SELECT COUNT(*) FROM tb_transacciones t WHERE t.id_persona = p.id_persona) AS trans_count,
      -- tipos adicionales desde pivot
      GROUP_CONCAT(DISTINCT tps.name_tipoPersona SEPARATOR ', ') AS tipos_extra,
      -- tags
      GROUP_CONCAT(DISTINCT tg.tag SEPARATOR ', ') AS tags
    FROM tb_personas p
    LEFT JOIN tb_tipopersonas tp ON tp.id_tipoPersona = p.id_tipoPersona
    LEFT JOIN tb_persona_tipos pt ON pt.id_persona = p.id_persona
    -- exclude the primary tipo (stored in p.id_tipoPersona) from the 'tipos_extra' list
    LEFT JOIN tb_tipopersonas tps ON tps.id_tipoPersona = pt.id_tipoPersona AND pt.id_tipoPersona <> p.id_tipoPersona
    LEFT JOIN tb_persona_tags ptg ON ptg.id_persona = p.id_persona
    LEFT JOIN tb_tags tg ON tg.id = ptg.tag_id
    GROUP BY p.id_persona
    ORDER BY p.name_persona ASC
  ";
  $st = $pdo->prepare($sql);
  $st->execute();
  $personas_datos = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  error_log($e->getMessage());
  $personas_datos = [];
}
