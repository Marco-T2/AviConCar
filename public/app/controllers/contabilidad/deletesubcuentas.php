<?php

include '../../config.php'; // Asegúrate de que la ruta al archivo de configuración es correcta.

// Verifica si el ID de la subcuenta está presente.
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_subcuenta = $_GET['id'];

    try {
        // Preparar la consulta para eliminar la subcuenta.
        $sql = "DELETE FROM tb_subcuentas WHERE id_subcuenta = :id_subcuenta";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_subcuenta', $id_subcuenta, PDO::PARAM_INT);

        // Ejecutar la consulta.
        $stmt->execute();

        // Redirigir a la página de subcuentas con un mensaje de éxito.
        session_start();
        $_SESSION['mensaje'] = 'Subcuenta eliminada con éxito.';
        $_SESSION['icono'] = 'success';
        header('Location:' . $URL . '/contabilidad/subcuentas.php');
    } catch (PDOException $e) {
        // En caso de error, redirigir con un mensaje.
        session_start();
        $_SESSION['mensaje'] = 'Error al eliminar la subcuenta: ' . $e->getMessage();
        $_SESSION['icono'] = 'error';
        header('Location:' . $URL . '/contabilidad/subcuentas.php');
    }
} else {
    // Redirigir si el ID no está presente o es inválido.
    session_start();
    $_SESSION['mensaje'] = 'ID de la subcuenta inválido.';
    $_SESSION['icono'] = 'warning';
    header('Location:' . $URL . '/contabilidad/subcuentas.php');
}
