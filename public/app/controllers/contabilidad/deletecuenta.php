<?php

include '../../config.php'; // Asegúrate de que la ruta al archivo de configuración es correcta.

// Verifica si el ID de la cuenta está presente.
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_cuenta = $_GET['id'];

    try {
        // Preparar la consulta para eliminar la cuenta.
        $sql = "DELETE FROM tb_cuentas WHERE id_cuenta = :id_cuenta";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_cuenta', $id_cuenta, PDO::PARAM_INT);

        // Ejecutar la consulta.
        $stmt->execute();

        // Redirigir a la página de cuentas con un mensaje de éxito.
        session_start();
        $_SESSION['mensaje'] = 'Cuenta eliminada con éxito.';
        $_SESSION['icono'] = 'success';
        header('Location:' . $URL . '/contabilidad/cuentas.php');
    } catch (PDOException $e) {
        // En caso de error, redirigir con un mensaje.
        session_start();
        $_SESSION['mensaje'] = 'Error al eliminar la cuenta: ' . $e->getMessage();
        $_SESSION['icono'] = 'error';
        header('Location:' . $URL . '/contabilidad/cuentas.php');
    }
} else {
    // Redirigir si el ID no está presente o es inválido.
    session_start();
    $_SESSION['mensaje'] = 'ID de la cuenta inválido.';
    $_SESSION['icono'] = 'warning';
    header('Location:' . $URL . '/contabilidad/cuentas.php');
}
