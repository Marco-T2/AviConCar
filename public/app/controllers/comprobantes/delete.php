<?php
include('../../config.php');

// evita "headers already sent" sin quitar tus echos
if (function_exists('ob_start')) { ob_start(); }

if (isset($_GET['id'])) {
    $id_comprobante = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    echo "ID recibido: " . $id_comprobante;

    try {
        // Iniciar transacción
        $pdo->beginTransaction();

        // Luego, eliminar las transacciones
        $sql_eliminar_transacciones = "DELETE FROM tb_transacciones WHERE id_comprobante = :id_comprobante";
        $stmt_eliminar_transacciones = $pdo->prepare($sql_eliminar_transacciones);
        $stmt_eliminar_transacciones->bindParam(":id_comprobante", $id_comprobante);
        $stmt_eliminar_transacciones->execute();

        // Finalmente, eliminar el comprobante
        $sql_eliminar_comprobante = "DELETE FROM tb_comprobantes WHERE id_comprobante = :id_comprobante";
        $stmt_eliminar_comprobante = $pdo->prepare($sql_eliminar_comprobante);
        $stmt_eliminar_comprobante->bindParam(":id_comprobante", $id_comprobante);
        $stmt_eliminar_comprobante->execute();

        // Si todo fue exitoso, confirmar transacción
        $pdo->commit();

        // Redirigir o informar del éxito
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        $_SESSION['mensaje'] = 'Comprobante eliminado correctamente';
        $_SESSION['icono']   = 'success';

        if (ob_get_level()) { ob_end_clean(); }
        header('Location:' . $URL . '/comprobantes');
        exit;

    } catch (PDOException $e) {
        // En caso de error, revertir la transacción
        if ($pdo && $pdo->inTransaction()) { $pdo->rollback(); }

        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        $_SESSION['mensaje'] = 'Error al eliminar el Comprobante';
        $_SESSION['icono']   = 'error';

        if (ob_get_level()) { ob_end_clean(); }
        header('Location:' . $URL . '/comprobantes');
        exit;
    }
} else {
    echo "ID no recibido.";

    if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
    $_SESSION['mensaje'] = 'ID no recibido';
    $_SESSION['icono']   = 'warning';

    if (ob_get_level()) { ob_end_clean(); }
    header('Location:' . $URL . '/comprobantes');
    exit;
}
