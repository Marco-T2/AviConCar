<?php

include('../../config.php');

if (isset($_POST['id_cuenta'], $_POST['name_cuenta'], $_POST['id_subgrupo'])) {
    $id_cuenta = $_POST['id_cuenta'];
    $name_cuenta = $_POST['name_cuenta'];
    $id_subgrupo = $_POST['id_subgrupo'];

    try {
        $pdo->beginTransaction();

        // Recuperar la información actual de la cuenta.
        $sql_current_info = "SELECT id_subgrupo, path FROM tb_cuentas WHERE id_cuenta = :id_cuenta";
        $stmt = $pdo->prepare($sql_current_info);
        $stmt->bindParam(':id_cuenta', $id_cuenta);
        $stmt->execute();
        $current_info = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el subgrupo ha cambiado.
        if ($current_info['id_subgrupo'] != $id_subgrupo) {
            // Recuperar el path base del nuevo subgrupo.
            $sql_get_subgrupo_path = "SELECT path FROM tb_subgrupos WHERE id_subgrupo = :id_subgrupo";
            $stmt = $pdo->prepare($sql_get_subgrupo_path);
            $stmt->bindParam(':id_subgrupo', $id_subgrupo);
            $stmt->execute();
            $subgrupo_path = $stmt->fetch(PDO::FETCH_COLUMN);

            // Construir el nuevo path para la cuenta.
            $new_cuenta_path = $subgrupo_path . '.' . $id_cuenta;

            // Actualizar el path de la cuenta.
            $sql_update_cuenta = "UPDATE tb_cuentas SET name_cuenta = :name_cuenta, path = :path, id_subgrupo = :id_subgrupo, fyh_actualizacion = NOW() WHERE id_cuenta = :id_cuenta";
            $stmt = $pdo->prepare($sql_update_cuenta);
            $stmt->bindParam(':name_cuenta', $name_cuenta);
            $stmt->bindParam(':path', $new_cuenta_path);
            $stmt->bindParam(':id_subgrupo', $id_subgrupo);
            $stmt->bindParam(':id_cuenta', $id_cuenta);
            $stmt->execute();

            // Actualizar el path de las subcuentas asociadas.
            $sql_update_subcuentas = "UPDATE tb_subcuentas SET path = CONCAT(:new_cuenta_path, '.', SUBSTRING_INDEX(path, '.', -1)) WHERE id_cuenta = :id_cuenta";
            $stmt = $pdo->prepare($sql_update_subcuentas);
            $stmt->bindParam(':new_cuenta_path', $new_cuenta_path);
            $stmt->bindParam(':id_cuenta', $id_cuenta);
            $stmt->execute();
        }

        $pdo->commit();

        session_start();
        $_SESSION['mensaje'] = 'La cuenta y sus subcuentas relacionadas han sido actualizadas con éxito';
        $_SESSION['icono'] = 'success';
        header("Location: {$URL}/contabilidad/cuentas.php");
    } catch (PDOException $e) {
        $pdo->rollBack();
        session_start();
        $_SESSION['mensaje'] = 'No se pudo actualizar la cuenta y sus subcuentas relacionadas: ' . $e->getMessage();
        $_SESSION['icono'] = 'error';
        header("Location: {$URL}/contabilidad/cuentas.php");
    }
} else {
    session_start();
    $_SESSION['mensaje'] = 'Falta información para actualizar la cuenta y sus subcuentas relacionadas';
    $_SESSION['icono'] = 'error';
    header("Location: {$URL}/contabilidad/cuentas.php");
}
