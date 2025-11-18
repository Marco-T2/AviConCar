<?php
include('../../config.php');


$id_usuario = $_POST['id_usuario'];
$name_persona = $_POST['name_persona'];
// multiple tipos may be provided as array
$selected_tipos = isset($_POST['id_tipoPersona']) ? (array)$_POST['id_tipoPersona'] : [];
$id_tipoPersona = count($selected_tipos) ? $selected_tipos[0] : null; // primary type for legacy column
$direccion = $_POST['direccion'];
$celular = $_POST['celular'];
$descripcion = $_POST['descripcion'];
$tags_raw = isset($_POST['tags']) ? trim($_POST['tags']) : '';




$sentencia = $pdo->prepare("INSERT INTO tb_personas (name_persona,id_tipoPersona,direccion,celular,
                            descripcion,id_usuario,fyh_creacion) VALUES 
                            (:name_persona,:id_tipoPersona,:direccion,:celular,
                            :descripcion,:id_usuario,:fyh_creacion)");

$sentencia->bindParam('name_persona', $name_persona);
$sentencia->bindParam('id_tipoPersona', $id_tipoPersona);
$sentencia->bindParam('direccion', $direccion);
$sentencia->bindParam('celular', $celular);
$sentencia->bindParam('descripcion', $descripcion);
$sentencia->bindParam('id_usuario', $id_usuario);
$sentencia->bindParam('fyh_creacion', $fechaHora);


if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = 'Se registro de forma correcta';
    $_SESSION['icono'] = 'success';
        $persona_id = $pdo->lastInsertId();
        // sync persona tipos (pivot)
        if(!empty($selected_tipos)){
            $ins = $pdo->prepare("INSERT IGNORE INTO tb_persona_tipos (persona_id, id_tipoPersona, created_at) VALUES (?, ?, NOW())");
            foreach($selected_tipos as $tid){ $ins->execute([$persona_id, $tid]); }
        }
        // handle tags (comma separated)
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
                if($tag_id) $insPersonaTag->execute([$persona_id, $tag_id]);
            }
        }
        header('Location:' . $URL . '/personas');
} else {
    //echo 'error las constraseñas no son iguales';
    session_start();
    $_SESSION['mensaje'] = 'No se pudo registrar';
    $_SESSION['icono'] = 'error';
    header('Location:' . $URL . '/personas/create.php' . $id_persona);
}
