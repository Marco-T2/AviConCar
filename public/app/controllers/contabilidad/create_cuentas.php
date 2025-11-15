<?php

include('../../config.php');

if (isset($_POST['name_cuenta'], $_POST['id_subgrupo'], $_POST['id_usuario'])) {
    $name_cuenta = $_POST['name_cuenta'];
    $id_subgrupo = $_POST['id_subgrupo'];
    $id_usuario = $_POST['id_usuario'];

    try {
        // Iniciar la transacción
        $pdo->beginTransaction();

        // Recuperar el path del subgrupo seleccionado y calcular el número máximo de cuenta
        $sql_get_max_cuenta = "SELECT sg.path, IFNULL(MAX(CAST(SUBSTRING_INDEX(c.path, '.', -1) AS UNSIGNED)), 0) as max_cuenta
                               FROM tb_subgrupos sg
                               LEFT JOIN tb_cuentas c ON c.id_subgrupo = sg.id_subgrupo
                               WHERE sg.id_subgrupo = :id_subgrupo
                               GROUP BY sg.path";
        $stmt = $pdo->prepare($sql_get_max_cuenta);
        $stmt->bindParam(':id_subgrupo', $id_subgrupo);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $subgrupo_path = $result['path'];
        $max_cuenta = $result['max_cuenta'] + 1;

        // Construir el path de la nueva cuenta
        $cuenta_path = $subgrupo_path . '.' . $max_cuenta;

        // Insertar la nueva cuenta con el path definitivo
        $sql_insert_cuenta = "INSERT INTO tb_cuentas (name_cuenta, path, id_subgrupo, id_usuario, fyh_creacion, fyh_actualizacion) 
                              VALUES (:name_cuenta, :path, :id_subgrupo, :id_usuario, NOW(), NOW())";
        $stmt = $pdo->prepare($sql_insert_cuenta);
        $stmt->bindParam(':name_cuenta', $name_cuenta);
        $stmt->bindParam(':path', $cuenta_path);
        $stmt->bindParam(':id_subgrupo', $id_subgrupo);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        // Confirmar la transacción
        $pdo->commit();

        session_start();
        $_SESSION['mensaje'] = 'La cuenta ha sido creada exitosamente.';
        $_SESSION['icono'] = 'success';
        echo "<script>location.href = '{$URL}/contabilidad/cuentas.php';</script>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        session_start();
        $_SESSION['mensaje'] = 'No se pudo registrar la cuenta: ' . $e->getMessage();
        $_SESSION['icono'] = 'error';
        echo "<script>location.href = '{$URL}/contabilidad/cuentas.php';</script>";
    }
} else {
    session_start();
    $_SESSION['mensaje'] = 'No se pudo registrar la cuenta por falta de información';
    $_SESSION['icono'] = 'error';
    echo "<script>location.href = '{$URL}/contabilidad/cuentas.php';</script>";
}
