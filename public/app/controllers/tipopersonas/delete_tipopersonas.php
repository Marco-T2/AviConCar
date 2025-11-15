<?php

include '../../config.php'; // Asegúrate de que la ruta al archivo de configuración es correcta.

// Verifica si el ID del tipo de persona está presente.
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_tipoPersona = $_GET['id'];

    try {
        // Preparar la consulta para eliminar el tipo de persona.
        $sql = "DELETE FROM tb_tipopersonas WHERE id_tipoPersona = :id_tipoPersona";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_tipoPersona', $id_tipoPersona, PDO::PARAM_INT);

        // Ejecutar la consulta.
        $stmt->execute();

        // Redirigir a la página de tipos de persona con un mensaje de éxito.
        session_start();
        $_SESSION['mensaje'] = 'Tipo de persona eliminado con éxito.';
        $_SESSION['icono'] = 'success';
        header('Location:' . $URL . '/tipopersonas/index.php');
    } catch (PDOException $e) {
        // En caso de error, redirigir con un mensaje.
        session_start();
        $_SESSION['mensaje'] = 'Error al eliminar el tipo de persona: ' . $e->getMessage();
        $_SESSION['icono'] = 'error';
        header('Location:' . $URL . '/tipopersonas/index.php');
    }
} else {
    // Redirigir si el ID no está presente o es inválido.
    session_start();
    $_SESSION['mensaje'] = 'ID del tipo de persona inválido.';
    $_SESSION['icono'] = 'warning';
    header('Location:' . $URL . '/tipopersonas/index.php');
}
