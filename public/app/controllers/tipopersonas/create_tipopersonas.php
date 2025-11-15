<?php

include('../../config.php');

// Verifica si se recibieron los datos necesarios.
if (isset($_POST['name_tipoPersona'], $_POST['descripcion'], $_POST['id_usuario'])) {
    $name_tipoPersona = $_POST['name_tipoPersona'];
    $descripcion = $_POST['descripcion'];  // Corregido para coincidir con el nombre del campo enviado.
    $id_usuario = $_POST['id_usuario'];

    try {
        // Iniciar la transacción
        $pdo->beginTransaction();

        // Preparar la sentencia de inserción
        $sql_insert_tipopersona = "INSERT INTO tb_tipopersonas (name_tipoPersona, descripcion, id_usuario, fyh_creacion, fyh_actualizacion) 
                                   VALUES (:name_tipoPersona, :descripcion, :id_usuario, NOW(), NOW())";
        $stmt = $pdo->prepare($sql_insert_tipopersona);
        $stmt->bindParam(':name_tipoPersona', $name_tipoPersona);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':id_usuario', $id_usuario);

        // Ejecutar la sentencia
        $stmt->execute();

        // Confirmar la transacción
        $pdo->commit();

        session_start();
        $_SESSION['mensaje'] = 'Tipo de persona creada con éxito';
        $_SESSION['icono'] = 'success';
        echo "<script>location.href = '{$URL}/tipopersonas/index.php';</script>";
    } catch (PDOException $e) {
        // En caso de error, revertir la transacción
        $pdo->rollBack();
        session_start();
        $_SESSION['mensaje'] = 'Error al crear el tipo de persona: ' . $e->getMessage();
        $_SESSION['icono'] = 'error';
        echo "<script>location.href = '{$URL}/tipopersonas/index.php';</script>";
    }
} else {
    session_start();
    $_SESSION['mensaje'] = 'Información incompleta para el registro del tipo de persona';
    $_SESSION['icono'] = 'warning';
    echo "<script>location.href = '{$URL}/tipopersonas/index.php';</script>";
}
