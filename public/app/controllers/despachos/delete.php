<?php
include('../../config.php');

// Inicia un buffer para que cualquier echo temprano NO envíe cabeceras aún
if (function_exists('ob_start')) { ob_start(); }

if (isset($_GET['id'])) {
    $id_comprobante = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    echo "ID recibido: " . $id_comprobante; // se limpiará antes de header()

    try {
        // Iniciar transacción
        $pdo->beginTransaction();

        // Eliminar primero los detalles de las transacciones
        $sql_eliminar_detalletransacciones = "DELETE FROM tb_detalletransacciones WHERE id_comprobante = :id_comprobante";
        $stmt_eliminar_detalletransacciones = $pdo->prepare($sql_eliminar_detalletransacciones);
        $stmt_eliminar_detalletransacciones->bindParam(":id_comprobante", $id_comprobante, PDO::PARAM_INT);
        $stmt_eliminar_detalletransacciones->execute();

        // Luego, eliminar las transacciones
        $sql_eliminar_transacciones = "DELETE FROM tb_transacciones WHERE id_comprobante = :id_comprobante";
        $stmt_eliminar_transacciones = $pdo->prepare($sql_eliminar_transacciones);
        $stmt_eliminar_transacciones->bindParam(":id_comprobante", $id_comprobante, PDO::PARAM_INT);
        $stmt_eliminar_transacciones->execute();

        // Finalmente, eliminar el comprobante
        $sql_eliminar_comprobante = "DELETE FROM tb_comprobantes WHERE id_comprobante = :id_comprobante";
        $stmt_eliminar_comprobante = $pdo->prepare($sql_eliminar_comprobante);
        $stmt_eliminar_comprobante->bindParam(":id_comprobante", $id_comprobante, PDO::PARAM_INT);
        $stmt_eliminar_comprobante->execute();

        // Si todo fue exitoso, confirmar transacción
        $pdo->commit();

        // Preparar redirección segura (sin enviar nada antes)
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        $_SESSION['mensaje'] = 'Despacho eliminado correctamente';
        $_SESSION['icono']   = 'success';

        // Limpiar cualquier salida previa y redirigir
        if (ob_get_level()) { ob_end_clean(); }
        header('Location: ' . $URL . '/despachos');
        exit;

    } catch (PDOException $e) {
        // Revertir la transacción si estaba activa
        if ($pdo && $pdo->inTransaction()) { $pdo->rollback(); }

        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        $_SESSION['mensaje'] = 'Error al eliminar el Despacho';
        $_SESSION['icono']   = 'error';

        if (ob_get_level()) { ob_end_clean(); }
        header('Location: ' . $URL . '/despachos');
        exit;
    }
} else {
    echo "ID no recibido."; // también se limpia
    if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
    $_SESSION['mensaje'] = 'ID no recibido';
    $_SESSION['icono']   = 'warning';

    if (ob_get_level()) { ob_end_clean(); }
    header('Location: ' . $URL . '/despachos');
    exit;
}
