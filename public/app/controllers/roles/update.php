<?php
include('../../config.php');

$id_rol = $_POST['$id_rol'];
$rol = $_POST['rol'];



$sentencia = $pdo->prepare("UPDATE roles 
        SET rol=:rol,
            fyh_actualizacion=:fyh_actualizacion 
        WHERE id_rol =:id_rol");

$sentencia->bindParam('rol', $rol);
$sentencia->bindParam('fyh_actualizacion', $fechaHora);
$sentencia->bindParam('id_rol', $id_rol);


if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = 'Se actualizo el rol de manera correcta';
    $_SESSION['icono'] = 'success';
    header('Location:' . $URL . '/roles');
} else {
    //echo 'error las constraseñas no son iguales';
    session_start();
    $_SESSION['mensaje'] = 'No se pudo actualizar el ROL';
    $_SESSION['icono'] = 'error';
    header('Location:' . $URL . '/roles/update.php?id=' . $id_rol);
}
