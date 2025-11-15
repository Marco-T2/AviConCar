<?php
include('../../config.php');

if (isset($_GET['anio'])) {
    $anioGestion = $_GET['anio'];
    $estado = 0;  // Estado inactivo por defecto, ya que es una nueva gestión.

    // Crear fecha de inicio y fin para la gestión.
    $fechaInicio = $anioGestion . '-01-01'; // Primer día del año.
    $fechaFin = $anioGestion . '-12-31'; // Último día del año.

    try {
        // Preparar la sentencia SQL para insertar la nueva gestión.
        $sql = "INSERT INTO tb_gestion (anio_gestion, fecha_inicio, fecha_fin, estado) VALUES (:anio_gestion, :fecha_inicio, :fecha_fin, :estado)";
        $stmt = $pdo->prepare($sql);

        // Vincular parámetros.
        $stmt->bindParam(':anio_gestion', $anioGestion, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_inicio', $fechaInicio);
        $stmt->bindParam(':fecha_fin', $fechaFin);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);

        // Ejecutar la sentencia.
        $stmt->execute();

        session_start();
        $_SESSION['mensaje'] = 'Nueva gestión creada con éxito.';
        $_SESSION['icono'] = 'success';
    } catch (PDOException $e) {
        session_start();
        $_SESSION['mensaje'] = "Error al crear la gestión: " . $e->getMessage();
        $_SESSION['icono'] = 'error';
        header('Location:' . $URL . '/usuarios');
    }
    header('Location: ' . $URL . '/configuracion/gestion.php');
    exit();
} else {
    session_start();
    $_SESSION['mensaje'] = 'Año de gestión no especificado.';
    $_SESSION['icono'] = 'warning';
    header('Location: ' . $URL . '/configuracion/gestion.php');
    exit();
}
