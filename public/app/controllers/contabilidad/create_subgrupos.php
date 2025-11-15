<?php

include('../../config.php');

if (isset($_POST['name_subgrupo'], $_POST['id_grupo'], $_POST['id_usuario'])) {
    $name_subgrupo = $_POST['name_subgrupo'];
    $id_grupo = $_POST['id_grupo'];
    $id_usuario = $_POST['id_usuario'];

    try {
        // Iniciar la transacción
        $pdo->beginTransaction();

        // Recuperar el path del grupo seleccionado y el número máximo de subgrupo
        $sql_get_max_subgrupo = "SELECT g.path, IFNULL(MAX(CAST(SUBSTRING_INDEX(sg.path, '.', -1) AS UNSIGNED)), 0) as max_subgrupo
                                 FROM tb_grupos g
                                 LEFT JOIN tb_subgrupos sg ON sg.id_grupo = g.id_grupo
                                 WHERE g.id_grupo = :id_grupo
                                 GROUP BY g.path";
        $stmt = $pdo->prepare($sql_get_max_subgrupo);
        $stmt->bindParam(':id_grupo', $id_grupo);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $grupo_path = $result['path'];
        $max_subgrupo = $result['max_subgrupo'] + 1;

        // Construir el path del nuevo subgrupo
        $subgrupo_path = $grupo_path . '.' . $max_subgrupo;

        // Insertar el nuevo subgrupo con el path definitivo
        $sql_insert_subgrupo = "INSERT INTO tb_subgrupos (name_subgrupo, path, id_grupo, fyh_creacion, fyh_actualizacion) 
                                VALUES (:name_subgrupo, :path, :id_grupo, NOW(), NOW())";
        $stmt = $pdo->prepare($sql_insert_subgrupo);
        $stmt->bindParam(':name_subgrupo', $name_subgrupo);
        $stmt->bindParam(':path', $subgrupo_path);
        $stmt->bindParam(':id_grupo', $id_grupo);
        $stmt->execute();

        // Confirmar la transacción
        $pdo->commit();

        session_start();
        $_SESSION['mensaje'] = 'Se registró el subgrupo con éxito';
        $_SESSION['icono'] = 'success';
        echo "<script>location.href = '{$URL}/contabilidad/subgrupos.php';</script>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        session_start();
        $_SESSION['mensaje'] = 'No se pudo registrar el subgrupo: ' . $e->getMessage();
        $_SESSION['icono'] = 'error';
        echo "<script>location.href = '{$URL}/contabilidad/subgrupos.php';</script>";
    }
} else {
    session_start();
    $_SESSION['mensaje'] = 'No se pudo registrar el subgrupo por falta de información';
    $_SESSION['icono'] = 'error';
    echo "<script>location.href = '{$URL}/contabilidad/subgrupos.php';</script>";
}
