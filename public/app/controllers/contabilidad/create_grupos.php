<?php

include('../../config.php');

$name_grupo = $_POST['name_grupo'] ?? 'default_name';
$naturaleza = $_POST['naturaleza'] ?? 'default_naturaleza';
$id_usuario = $_POST['id_usuario'] ?? 0;

try {
    $pdo->beginTransaction();

    // Inserta el nuevo grupo sin definir el 'path' inicialmente
    $sentencia = $pdo->prepare("INSERT INTO tb_grupos (name_grupo, naturaleza, fyh_creacion, fyh_actualizacion) 
                                VALUES (:name_grupo, :naturaleza, NOW(), NOW())");
    $sentencia->bindParam(':name_grupo', $name_grupo);
    $sentencia->bindParam(':naturaleza', $naturaleza);
    $sentencia->execute();

    // Obtiene el ID del último grupo insertado
    $lastId = $pdo->lastInsertId();

    // Define el 'path' como el ID del grupo recién insertado
    $newPath = $lastId;

    // Actualiza el grupo con su 'path'
    $updateSentencia = $pdo->prepare("UPDATE tb_grupos SET path = :newPath WHERE id_grupo = :lastId");
    $updateSentencia->bindParam(':newPath', $newPath);
    $updateSentencia->bindParam(':lastId', $lastId, PDO::PARAM_INT);
    $updateSentencia->execute();

    $pdo->commit();

    session_start();
    $_SESSION['mensaje'] = 'Se registró el grupo con éxito';
    $_SESSION['icono'] = 'success';
    echo "<script>location.href = '{$URL}/contabilidad/grupos.php';</script>";
} catch (Exception $e) {
    $pdo->rollBack();
    session_start();
    $_SESSION['mensaje'] = 'No se pudo registrar el grupo: ' . $e->getMessage();
    $_SESSION['icono'] = 'error';
    echo "<script>location.href = '{$URL}/contabilidad/grupos.php';</script>";
}
