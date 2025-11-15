<?php
include('../../config.php');

$id_usuario = $_POST['id_usuario'];
$nombres = $_POST['nombres'];
$usuario = $_POST['usuario'];
$rol = $_POST['rol'];
$password_user = $_POST['password_user'];
$password_userRepet = $_POST['password_userRepet'];


if ($password_user == '') {
    if ($password_user == $password_userRepet) {
        $password_user = password_hash($password_user, PASSWORD_DEFAULT);

        $sentencia = $pdo->prepare("UPDATE tb_usuarios 
        SET nombres=:nombres,usuario=:usuario,
            id_rol=:id_rol,
            fyh_actualizacion=:fyh_actualizacion 
        WHERE id_usuario = :id_usuario");

        $sentencia->bindParam('nombres', $nombres);
        $sentencia->bindParam('usuario', $usuario);
        $sentencia->bindParam('id_rol', $rol);
        $sentencia->bindParam('fyh_actualizacion', $fechaHora);
        $sentencia->bindParam('id_usuario', $id_usuario);
        $sentencia->execute();
        session_start();
        $_SESSION['mensaje'] = 'Se actualizo al usuario de manera correcta';
        $_SESSION['icono'] = 'success';
        header('Location:' . $URL . '/usuarios');
    } else {
        //echo 'error las constraseñas no son iguales';
        session_start();
        $_SESSION['mensaje'] = 'Las contraseñas no son iguales';
        $_SESSION['icono'] = 'error';
        header('Location:' . $URL . '/usuarios/update.php?id=' . $id_usuario);
    }
} else {
    if ($password_user == $password_userRepet) {
        $password_user = password_hash($password_user, PASSWORD_DEFAULT);

        $sentencia = $pdo->prepare("UPDATE tb_usuarios 
        SET nombres=:nombres,usuario=:usuario,
            id_rol=:id_rol,
            password_user=:password_user,
            fyh_actualizacion=:fyh_actualizacion 
        WHERE id_usuario = :id_usuario");

        $sentencia->bindParam('nombres', $nombres);
        $sentencia->bindParam('usuario', $usuario);
        $sentencia->bindParam('id_rol', $rol);
        $sentencia->bindParam('password_user', $password_user);
        $sentencia->bindParam('fyh_actualizacion', $fechaHora);
        $sentencia->bindParam('id_usuario', $id_usuario);
        $sentencia->execute();
        session_start();
        $_SESSION['mensaje'] = 'Se actualizo al usuario de manera correcta';
        $_SESSION['icono'] = 'success';
        header('Location:' . $URL . '/usuarios');
    } else {
        //echo 'error las constraseñas no son iguales';
        session_start();
        $_SESSION['mensaje'] = 'Las contraseñas no son iguales';
        $_SESSION['icono'] = 'error';
        header('Location:' . $URL . '/usuarios/update.php?id=' . $id_usuario);
    }
}
