<?php
include('../../config.php');


$id_usuario = $_POST['id_usuario'];
$id_persona = $_POST['id_persona'];
$name_persona = $_POST['name_persona'];
// multiple tipos may be provided
$selected_tipos = isset($_POST['id_tipoPersona']) ? (array)$_POST['id_tipoPersona'] : [];
$primary_tipo_post = isset($_POST['primary_tipo']) ? $_POST['primary_tipo'] : null;
$id_tipoPersona = $primary_tipo_post ? $primary_tipo_post : (count($selected_tipos) ? $selected_tipos[0] : null); // primary for legacy column
$direccion = $_POST['direccion'];
$celular = $_POST['celular'];
$descripcion = $_POST['descripcion'];
$tags_raw = isset($_POST['tags']) ? trim($_POST['tags']) : '';




$sentencia = $pdo->prepare("UPDATE tb_personas 
                            SET name_persona=:name_persona,id_tipoPersona=:id_tipoPersona,
                                direccion=:direccion,celular=:celular,
                                descripcion=:descripcion,id_usuario=:id_usuario,
                                fyh_actualizacion=:fyh_actualizacion 
                            WHERE id_persona =:id_persona");

$sentencia->bindParam('name_persona', $name_persona);
$sentencia->bindParam('id_tipoPersona', $id_tipoPersona);
$sentencia->bindParam('direccion', $direccion);
$sentencia->bindParam('celular', $celular);
$sentencia->bindParam('descripcion', $descripcion);
$sentencia->bindParam('id_usuario', $id_usuario);
$sentencia->bindParam('fyh_actualizacion', $fechaHora);
$sentencia->bindParam('id_persona', $id_persona);


if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = 'Se actualizo la informacion de la persona';
    $_SESSION['icono'] = 'success';
        // sync tb_persona_tipos: remove existing and insert new selections
        $del = $pdo->prepare("DELETE FROM tb_persona_tipos WHERE persona_id = ?");
        $del->execute([$id_persona]);
        if(!empty($selected_tipos)){
            $ins = $pdo->prepare("INSERT IGNORE INTO tb_persona_tipos (persona_id, id_tipoPersona, created_at) VALUES (?, ?, NOW())");
            foreach($selected_tipos as $tid){ $ins->execute([$id_persona, $tid]); }
        }
        // sync tags
        // delete existing
        $delTags = $pdo->prepare("DELETE FROM tb_persona_tags WHERE persona_id = ?");
        $delTags->execute([$id_persona]);
        if(!empty($tags_raw)){
            $tags = array_filter(array_map('trim', explode(',', $tags_raw)));
            $selTag = $pdo->prepare("SELECT id FROM tb_tags WHERE tag = ? LIMIT 1");
            $insTag = $pdo->prepare("INSERT INTO tb_tags (tag, descripcion, created_at) VALUES (?, ?, NOW())");
            $insPersonaTag = $pdo->prepare("INSERT IGNORE INTO tb_persona_tags (persona_id, tag_id, created_at) VALUES (?, ?, NOW())");
            foreach($tags as $t){
                if($t === '') continue;
                $selTag->execute([$t]);
                $r = $selTag->fetch(PDO::FETCH_ASSOC);
                if($r){ $tag_id = $r['id']; }
                else { $insTag->execute([$t, null]); $tag_id = $pdo->lastInsertId(); }
                if($tag_id) $insPersonaTag->execute([$id_persona, $tag_id]);
            }
        }
        header('Location:' . $URL . '/personas');
} else {
    //echo 'error las constraseñas no son iguales';
    session_start();
    $_SESSION['mensaje'] = 'No se pudo actualizar la informacion';
    $_SESSION['icono'] = 'error';
    header('Location:' . $URL . '/personas/update.php?id=' . $id_persona);
}
