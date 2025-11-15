<?php

include('../../config.php');

if (isset($_POST['id_subcuenta'], $_POST['name_subcuenta'], $_POST['id_cuenta'])) {
    $id_subcuenta = $_POST['id_subcuenta'];
    $name_subcuenta = $_POST['name_subcuenta'];
    $id_cuenta = $_POST['id_cuenta'];

    try {
        $pdo->beginTransaction();

        // Primero, recuperar la información actual de la subcuenta
        $sql_current_info = "SELECT id_cuenta, path FROM tb_subcuentas WHERE id_subcuenta = :id_subcuenta";
        $stmt = $pdo->prepare($sql_current_info);
        $stmt->bindParam(':id_subcuenta', $id_subcuenta);
        $stmt->execute();
        $current_info = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si la cuenta ha cambiado
        if ($current_info['id_cuenta'] != $id_cuenta) {
            // Recuperar el path del nuevo cuenta seleccionado y el número máximo de subcuenta
            $sql_get_max_subcuenta = "SELECT c.path, IFNULL(MAX(CAST(SUBSTRING_INDEX(sc.path, '.', -1) AS UNSIGNED)), 0) + 1 as max_subcuenta
                                       FROM tb_cuentas c
                                       LEFT JOIN tb_subcuentas sc ON sc.id_cuenta = c.id_cuenta
                                       WHERE c.id_cuenta = :id_cuenta
                                       GROUP BY c.path";
            $stmt = $pdo->prepare($sql_get_max_subcuenta);
            $stmt->bindParam(':id_cuenta', $id_cuenta);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $cuenta_path = $result['path'];
            $max_subcuenta = $result['max_subcuenta'];

            // Construir el path de la nueva subcuenta
            $subcuenta_path = $cuenta_path . '.' . $max_subcuenta;
        } else {
            // Mantener el path existente si la cuenta no ha cambiado
            $subcuenta_path = $current_info['path'];
        }

        // Actualizar la subcuenta con el path y la cuenta posiblemente actualizados
        $sql_update_subcuenta = "UPDATE tb_subcuentas 
                                 SET name_subcuenta = :name_subcuenta, 
                                     path = :path,
                                     id_cuenta = :id_cuenta, 
                                     fyh_actualizacion = NOW()
                                 WHERE id_subcuenta = :id_subcuenta";
        $stmt = $pdo->prepare($sql_update_subcuenta);
        $stmt->bindParam(':name_subcuenta', $name_subcuenta);
        $stmt->bindParam(':path', $subcuenta_path);
        $stmt->bindParam(':id_cuenta', $id_cuenta);
        $stmt->bindParam(':id_subcuenta', $id_subcuenta);
        $stmt->execute();

        $pdo->commit();

        session_start();
        $_SESSION['mensaje'] = 'La subcuenta ha sido actualizada con éxito';
        $_SESSION['icono'] = 'success';
        echo "<script>location.href = '{$URL}/contabilidad/subcuentas.php';</script>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        session_start();
        $_SESSION['mensaje'] = 'No se pudo actualizar la subcuenta: ' . $e->getMessage();
        $_SESSION['icono'] = 'error';
        echo "<script>location.href = '{$URL}/contabilidad/subcuentas.php';</script>";
    }
} else {
    session_start();
    $_SESSION['mensaje'] = 'Falta información para actualizar la subcuenta';
    $_SESSION['icono'] = 'error';
    echo "<script>location.href = '{$URL}/contabilidad/subcuentas.php';</script>";
}
