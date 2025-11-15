<?php

include '../../config.php'; // Asegúrate de que la ruta al archivo de configuración es correcta.

// Verifica si el ID del tipo de persona y los demás campos necesarios están presentes.
if (isset($_POST['id_tipoPersona'], $_POST['name_tipoPersona'], $_POST['descripcion'])) {
    $id_tipoPersona = $_POST['id_tipoPersona'];
    $name_tipoPersona = $_POST['name_tipoPersona'];
    $descripcion = $_POST['descripcion'];

    try {
        // Iniciar la transacción
        $pdo->beginTransaction();

        // Actualizar la información del tipo de persona
        $sql_update_tipoPersona = "UPDATE tb_tipopersonas SET name_tipoPersona = :name_tipoPersona, descripcion = :descripcion, fyh_actualizacion = NOW() WHERE id_tipoPersona = :id_tipoPersona";
        $stmt = $pdo->prepare($sql_update_tipoPersona);
        $stmt->bindParam(':name_tipoPersona', $name_tipoPersona);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':id_tipoPersona', $id_tipoPersona);

        // Ejecutar la sentencia
        $stmt->execute();

        // Confirmar la transacción
        $pdo->commit();

        session_start();
        $_SESSION['mensaje'] = 'Tipo de persona actualizado con éxito';
        $_SESSION['icono'] = 'success';
        echo "<script>location.href = '{$URL}/tipopersonas/index.php';</script>";
    } catch (PDOException $e) {
        // En caso de error, revertir la transacción
        $pdo->rollBack();
        session_start();
        $_SESSION['mensaje'] = 'Error al actualizar el tipo de persona: ' . $e->getMessage();
        $_SESSION['icono'] = 'error';
        echo "<script>location.href = '{$URL}/tipopersonas/index.php';</script>";
    }
} else {
    session_start();
    $_SESSION['mensaje'] = 'Información incompleta para la actualización del tipo de persona';
    $_SESSION['icono'] = 'warning';
    echo "<script>location.href = '{$URL}/tipopersonas/index.php';</script>";
}
