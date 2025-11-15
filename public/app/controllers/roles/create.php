<?php
include('../../config.php');

$rol = $_POST['rol'];

$sentencia = $pdo->prepare("INSERT INTO roles (rol,fyh_creacion) 
        VALUES (:rol,:fyh_creacion)");

$sentencia->bindParam('rol', $rol);
$sentencia->bindParam('fyh_creacion', $fechaHora);

if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = 'Se registro el nuevo rol';
    $_SESSION['icono'] = 'success';
    header('Location:' . $URL . '/roles');
} else {
    //echo 'error las constraseñas no son iguales';
    session_start();
    $_SESSION['mensaje'] = 'No se pudo registrar el Rol';
    $_SESSION['icono'] = 'error';
    header('Location:' . $URL . '/usuarios/create.php');
}
