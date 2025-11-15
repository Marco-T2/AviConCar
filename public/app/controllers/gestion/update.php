<?php
include('../../config.php');

// Verificar si el ID de la gestión fue enviado
if (isset($_GET['id'])) {
    $id_gestion = $_GET['id'];

    try {
        // Iniciar transacción
        $pdo->beginTransaction();

        // Establecer todas las gestiones a estado no activo (0)
        $sql_desactivar_gestiones = "UPDATE tb_gestion SET estado = 0";
        $pdo->exec($sql_desactivar_gestiones);

        // Activar la gestión seleccionada (1)
        $sql_activar_gestion = "UPDATE tb_gestion SET estado = 1 WHERE id_gestion = :id_gestion";
        $stmt = $pdo->prepare($sql_activar_gestion);
        $stmt->bindValue(':id_gestion', $id_gestion, PDO::PARAM_INT);
        $stmt->execute();

        // Confirmar la transacción
        $pdo->commit();

        // Redirigir al usuario con un mensaje de éxito
        session_start();
        $_SESSION['mensaje'] = 'Gestión actualizada correctamente';
        $_SESSION['icono'] = 'success';
        header('Location: ' . $URL . '/configuracion/gestion.php');
    } catch (Exception $e) {
        // Revertir la transacción si ocurre un error
        $pdo->rollBack();
        session_start();
        $_SESSION['mensaje'] = 'Error al actualizar la gestión';
        $_SESSION['icono'] = 'error';
        header('Location: ' . $URL . '/configuracion/gestion.php');
    }
} else {
    // Redirigir al usuario si no se proporcionó un ID
    header('Location: ' . $URL . '/configuracion/gestion.php');
}
