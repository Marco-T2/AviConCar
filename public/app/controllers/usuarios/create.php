<?php
include('../../config.php');

$nombres = $_POST['nombres'];
$usuario = $_POST['usuario'];
$rol = $_POST['rol'];
$password_user = $_POST['password_user'];
$password_userRepet = $_POST['password_userRepet'];

if ($password_user == $password_userRepet) {
    $password_user = password_hash($password_user, PASSWORD_DEFAULT);

    $sentencia = $pdo->prepare("INSERT INTO tb_usuarios (nombres,usuario,id_rol, password_user,fyh_creacion) 
        VALUES (:nombres,:usuario,:id_rol,:password_user,:fyh_creacion)");

    $sentencia->bindParam('nombres', $nombres);
    $sentencia->bindParam('usuario', $usuario);
    $sentencia->bindParam('id_rol', $rol);
    $sentencia->bindParam('password_user',  $password_user);
    $sentencia->bindParam('fyh_creacion', $fechaHora);
    $sentencia->execute();
    session_start();
    $_SESSION['mensaje'] = 'Se registro de manera correcta';
    $_SESSION['icono'] = 'success';
    header('Location:' . $URL . '/usuarios');
} else {
    //echo 'error las constraseñas no son iguales';
    session_start();
    $_SESSION['mensaje'] = 'Las contraseñas no son iguales';
    $_SESSION['icono'] = 'error';
    header('Location:' . $URL . '/usuarios/create.php');
}
