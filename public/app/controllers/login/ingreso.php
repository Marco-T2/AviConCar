<?php
include('../../config.php');

$user = $_POST['user'];
$password_user = $_POST['password_user'];





$sql = "SELECT * FROM tb_usuarios WHERE usuario = '$user';";
$query = $pdo->prepare($sql);
$query->execute();

//Validacion del ususario
$contador = 0;
$usuarios = $query->fetchAll(PDO::FETCH_ASSOC);
foreach ($usuarios as $usuarios) {
    $contador = $contador + 1;
    $usuario = $usuarios['usuario'];
    $password_user_tabla = $usuarios['password_user'];
}


if (($contador > 0) && (password_verify($password_user, $password_user_tabla))) {
    //echo 'Datos correctos';
    session_start();
    $_SESSION['sesion_user'] = $usuario;
    header('Location:' . $URL . '/index.php');
    $_SESSION['mensaje'] = 'BIENVENIDO AL SISTEMA';
    $_SESSION['icono'] = 'success';
} else {
    //echo 'datos incorrectos vuelva a intentarlo con SWET ALERT';
    session_start();
    $_SESSION['mensaje'] = 'Error datos incorrectos';
    header('Location:' . $URL . '/');
}
