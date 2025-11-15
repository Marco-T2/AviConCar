<?php

include('../../config.php');

if (isset($_POST['id_subgrupo'], $_POST['name_subgrupo'], $_POST['id_grupo'])) {
    $id_subgrupo = $_POST['id_subgrupo'];
    $name_subgrupo = $_POST['name_subgrupo'];
    $id_grupo = $_POST['id_grupo'];

    try {
        $pdo->beginTransaction();

        // Recuperar la información actual del subgrupo
        $sql_current_info = "SELECT id_grupo, path FROM tb_subgrupos WHERE id_subgrupo = :id_subgrupo";
        $stmt = $pdo->prepare($sql_current_info);
        $stmt->bindParam(':id_subgrupo', $id_subgrupo);
        $stmt->execute();
        $current_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($current_info['id_grupo'] != $id_grupo) {
            // Recuperar el path del nuevo grupo
            $sql_get_grupo_path = "SELECT path FROM tb_grupos WHERE id_grupo = :id_grupo";
            $stmt = $pdo->prepare($sql_get_grupo_path);
            $stmt->bindParam(':id_grupo', $id_grupo);
            $stmt->execute();
            $grupo_path = $stmt->fetch(PDO::FETCH_COLUMN);

            // Construir el nuevo path base para el subgrupo y sus entidades subordinadas
            $new_subgrupo_base_path = $grupo_path . '.' . $id_subgrupo;

            // Actualizar el path del subgrupo
            $sql_update_subgrupo = "UPDATE tb_subgrupos SET name_subgrupo = :name_subgrupo, path = :path, id_grupo = :id_grupo, fyh_actualizacion = NOW() WHERE id_subgrupo = :id_subgrupo";
            $stmt = $pdo->prepare($sql_update_subgrupo);
            $stmt->bindParam(':name_subgrupo', $name_subgrupo);
            $stmt->bindParam(':path', $new_subgrupo_base_path);
            $stmt->bindParam(':id_grupo', $id_grupo);
            $stmt->bindParam(':id_subgrupo', $id_subgrupo);
            $stmt->execute();

            // Actualizar el path de las cuentas asociadas
            $sql_update_cuentas = "UPDATE tb_cuentas SET path = CONCAT(:new_subgrupo_base_path, '.', SUBSTRING_INDEX(path, '.', -1)) WHERE id_subgrupo = :id_subgrupo";
            $stmt = $pdo->prepare($sql_update_cuentas);
            $stmt->bindParam(':new_subgrupo_base_path', $new_subgrupo_base_path);
            $stmt->bindParam(':id_subgrupo', $id_subgrupo);
            $stmt->execute();

            // Actualizar el path de las subcuentas asociadas
            $sql_update_subcuentas = "UPDATE tb_subcuentas sc INNER JOIN tb_cuentas c ON sc.id_cuenta = c.id_cuenta SET sc.path = CONCAT(c.path, '.', SUBSTRING_INDEX(sc.path, '.', -1)) WHERE c.id_subgrupo = :id_subgrupo";
            $stmt = $pdo->prepare($sql_update_subcuentas);
            $stmt->bindParam(':id_subgrupo', $id_subgrupo);
            $stmt->execute();
        }

        $pdo->commit();

        session_start();
        $_SESSION['mensaje'] = 'El subgrupo y las entidades relacionadas han sido actualizadas con éxito';
        $_SESSION['icono'] = 'success';
        header("Location: {$URL}/contabilidad/subgrupos.php");
    } catch (PDOException $e) {
        $pdo->rollBack();
        session_start();
        $_SESSION['mensaje'] = 'No se pudo actualizar el subgrupo y las entidades relacionadas: ' . $e->getMessage();
        $_SESSION['icono'] = 'error';
        header("Location: {$URL}/contabilidad/subgrupos.php");
    }
} else {
    session_start();
    $_SESSION['mensaje'] = 'Falta información para actualizar el subgrupo y las entidades relacionadas';
    $_SESSION['icono'] = 'error';
    header("Location: {$URL}/contabilidad/subgrupos.php");
}
