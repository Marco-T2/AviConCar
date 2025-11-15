<?php
include('../../config.php');


$id_usuario = $_POST['id_usuario'];
$name_persona = $_POST['name_persona'];
$id_tipoPersona = $_POST['id_tipoPersona'];
$direccion = $_POST['direccion'];
$celular = $_POST['celular'];
$descripcion = $_POST['descripcion'];




$sentencia = $pdo->prepare("INSERT INTO tb_personas (name_persona,id_tipoPersona,direccion,celular,
                            descripcion,id_usuario,fyh_creacion) VALUES 
                            (:name_persona,:id_tipoPersona,:direccion,:celular,
                            :descripcion,:id_usuario,:fyh_creacion)");

$sentencia->bindParam('name_persona', $name_persona);
$sentencia->bindParam('id_tipoPersona', $id_tipoPersona);
$sentencia->bindParam('direccion', $direccion);
$sentencia->bindParam('celular', $celular);
$sentencia->bindParam('descripcion', $descripcion);
$sentencia->bindParam('id_usuario', $id_usuario);
$sentencia->bindParam('fyh_creacion', $fechaHora);


if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = 'Se registro de forma correcta';
    $_SESSION['icono'] = 'success';
    header('Location:' . $URL . '/personas');
} else {
    //echo 'error las constraseñas no son iguales';
    session_start();
    $_SESSION['mensaje'] = 'No se pudo registrar';
    $_SESSION['icono'] = 'error';
    header('Location:' . $URL . '/personas/create.php' . $id_persona);
}
