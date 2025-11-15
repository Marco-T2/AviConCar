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
      tp.name_tipoPersona
    FROM tb_personas p
    LEFT JOIN tb_tipopersonas tp
      ON tp.id_tipoPersona = p.id_tipoPersona
    ORDER BY p.name_persona ASC
  ";
  $st = $pdo->prepare($sql);
  $st->execute();
  $personas_datos = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  error_log($e->getMessage());
  $personas_datos = [];
}
