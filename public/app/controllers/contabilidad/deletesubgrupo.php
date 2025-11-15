<?php

include '../../config.php'; // Asegúrate de que la ruta al archivo de configuración es correcta.

// Verifica si el ID del subgrupo está presente.
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_subgrupo = $_GET['id'];

    try {
        // Preparar la consulta para eliminar el subgrupo.
        $sql = "DELETE FROM tb_subgrupos WHERE id_subgrupo = :id_subgrupo";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_subgrupo', $id_subgrupo, PDO::PARAM_INT);

        // Ejecutar la consulta.
        $stmt->execute();

        // Redirigir a la página de subgrupos con un mensaje de éxito.
        session_start();
        $_SESSION['mensaje'] = 'Subgrupo eliminado con éxito.';
        $_SESSION['icono'] = 'success';
        header('Location:' . $URL . '/contabilidad/subgrupos.php');
    } catch (PDOException $e) {
        // En caso de error, redirigir con un mensaje.
        session_start();
        $_SESSION['mensaje'] = 'Error al eliminar el subgrupo: ' . $e->getMessage();
        $_SESSION['icono'] = 'error';
        header('Location:' . $URL . '/contabilidad/subgrupos.php');
    }
} else {
    // Redirigir si el ID no está presente o es inválido.
    session_start();
    $_SESSION['mensaje'] = 'ID del subgrupo inválido.';
    $_SESSION['icono'] = 'warning';
    header('Location:' . $URL . '/contabilidad/subgrupos.php');
}
