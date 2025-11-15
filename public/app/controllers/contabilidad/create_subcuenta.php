<?php

include('../../config.php');

if (isset($_POST['name_subcuenta'], $_POST['id_cuenta'], $_POST['id_usuario'])) {
    $name_subcuenta = $_POST['name_subcuenta'];
    $id_cuenta = $_POST['id_cuenta'];
    $id_usuario = $_POST['id_usuario'];

    try {
        // Iniciar la transacción
        $pdo->beginTransaction();

        // Recuperar el path de la cuenta seleccionada y calcular el número máximo de subcuenta
        $sql_get_max_subcuenta = "SELECT c.path, IFNULL(MAX(CAST(SUBSTRING_INDEX(sc.path, '.', -1) AS UNSIGNED)), 0) as max_subcuenta
                                  FROM tb_cuentas c
                                  LEFT JOIN tb_subcuentas sc ON sc.id_cuenta = c.id_cuenta
                                  WHERE c.id_cuenta = :id_cuenta
                                  GROUP BY c.path";
        $stmt = $pdo->prepare($sql_get_max_subcuenta);
        $stmt->bindParam(':id_cuenta', $id_cuenta);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $cuenta_path = $result['path'];
        $max_subcuenta = $result['max_subcuenta'] + 1;

        // Construir el path de la nueva subcuenta
        $subcuenta_path = $cuenta_path . '.' . $max_subcuenta;

        // Insertar la nueva subcuenta con el path definitivo
        $sql_insert_subcuenta = "INSERT INTO tb_subcuentas (name_subcuenta, path, id_cuenta, id_usuario, fyh_creacion, fyh_actualizacion) 
                                 VALUES (:name_subcuenta, :path, :id_cuenta, :id_usuario, NOW(), NOW())";
        $stmt = $pdo->prepare($sql_insert_subcuenta);
        $stmt->bindParam(':name_subcuenta', $name_subcuenta);
        $stmt->bindParam(':path', $subcuenta_path);
        $stmt->bindParam(':id_cuenta', $id_cuenta);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        // Confirmar la transacción
        $pdo->commit();

        session_start();
        $_SESSION['mensaje'] = 'La subcuenta ha sido creada exitosamente.';
        $_SESSION['icono'] = 'success';
        echo "<script>location.href = '{$URL}/contabilidad/subcuentas.php';</script>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        session_start();
        $_SESSION['mensaje'] = 'No se pudo registrar la subcuenta: ' . $e->getMessage();
        $_SESSION['icono'] = 'error';
        echo "<script>location.href = '{$URL}/contabilidad/subcuentas.php';</script>";
    }
} else {
    session_start();
    $_SESSION['mensaje'] = 'No se pudo registrar la subcuenta por falta de información';
    $_SESSION['icono'] = 'error';
    echo "<script>location.href = '{$URL}/contabilidad/subcuentas.php';</script>";
}
