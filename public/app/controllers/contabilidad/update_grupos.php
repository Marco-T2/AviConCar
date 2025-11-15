<?php
include('../../config.php');


if (isset($_POST['id_grupo'], $_POST['name_grupo'], $_POST['naturaleza'])) {
    $id_grupo = $_POST['id_grupo'];
    $name_grupo = $_POST['name_grupo'];
    $naturaleza = $_POST['naturaleza'];

    try {
        $sql_update = "UPDATE tb_grupos SET name_grupo = :name_grupo, naturaleza = :naturaleza WHERE id_grupo = :id_grupo";
        $stmt = $pdo->prepare($sql_update);
        $stmt->bindParam(':name_grupo', $name_grupo);
        $stmt->bindParam(':naturaleza', $naturaleza);
        $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
        $stmt->execute();

        // Redireccionar de vuelta a la página de grupos con un mensaje de éxito
        session_start();
        $_SESSION['mensaje'] = 'Se actualizo el grupo con éxito';
        $_SESSION['icono'] = 'success';
        echo "<script>location.href = '{$URL}/contabilidad/grupos.php';</script>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        session_start();
        $_SESSION['mensaje'] = 'No se pudo actualizar el grupo: ' . $e->getMessage();
        $_SESSION['icono'] = 'error';
        echo "<script>location.href = '{$URL}/contabilidad/grupos.php';</script>";
    }
} else {
    session_start();
    $_SESSION['mensaje'] = 'Información insuficiente para actualizar el grupo.';
    $_SESSION['mensaje_tipo'] = 'warning';
    echo "<script>location.href = '{$URL}/contabilidad/grupos.php';</script>";
}
