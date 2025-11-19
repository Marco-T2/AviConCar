<?php

$id_usuario_get = $_GET['id'];

// Validar el ID del usuario
if (!filter_var($id_usuario_get, FILTER_VALIDATE_INT)) {
    die('ID inválido.');
}

// Usar alias en la consulta que coincidan con los nombres de las variables PHP, sin comillas o con comillas invertidas para MySQL
$sql_personas = "SELECT 
                    a.id_persona AS `id_persona`, 
                    a.name_persona AS `name_persona`, 
                    b.name_tipoPersona AS `name_tipoPersona`, 
                    a.direccion AS `direccion`, 
                    a.celular AS `celular`, 
                    a.descripcion AS `descripcion` 
                 FROM 
                    tb_personas AS a 
                 INNER JOIN 
                    tb_tipopersonas AS b 
                 ON 
                    a.id_tipoPersona = b.id_tipoPersona 
                 WHERE 
                    a.id_persona = :id_usuario;";

try {
    $query_personas = $pdo->prepare($sql_personas);
    $query_personas->bindParam(':id_usuario', $id_usuario_get, PDO::PARAM_INT);
    $query_personas->execute();

    // Suponiendo que solo habrá un registro para cada ID
    if ($personas_dato = $query_personas->fetch(PDO::FETCH_ASSOC)) {
        $id_persona = $personas_dato['id_persona'];
        $name_persona = $personas_dato['name_persona'];
        $name_tipoPersona = $personas_dato['name_tipoPersona'];
        // primary tipo id (legacy column)
        $primary_tipo_id = $personas_dato['id_tipoPersona'] ?? null;
        $direccion = $personas_dato['direccion'];
        $celular = $personas_dato['celular'];
        $descripcion = $personas_dato['descripcion'];
                // fetch existing tipos (pivot)
                try{
                    $stt = $pdo->prepare("SELECT id_tipoPersona FROM tb_persona_tipos WHERE id_persona = ?");
                    $stt->execute([$id_persona]);
                    $existing_tipo_ids = array_column($stt->fetchAll(PDO::FETCH_ASSOC), 'id_tipoPersona');
                }catch(Exception $e){ $existing_tipo_ids = []; }
                // fetch existing tags
                try{
                    $stt2 = $pdo->prepare("SELECT tg.tag FROM tb_persona_tags pt JOIN tb_tags tg ON tg.id = pt.tag_id WHERE pt.id_persona = ?");
                    $stt2->execute([$id_persona]);
                    $existing_tags = implode(', ', array_column($stt2->fetchAll(PDO::FETCH_ASSOC), 'tag'));
                }catch(Exception $e){ $existing_tags = ''; }
    } else {
        // Manejar el caso donde no se encuentra el usuario
        echo "No se encontró información para el ID proporcionado.";
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Error al realizar la consulta: ' . $e->getMessage());
}
