<?php
include('../../config.php');


$id_usuario = $_POST['id_usuario'];
$id_persona = $_POST['id_persona'];
$name_persona = $_POST['name_persona'];
$id_tipoPersona = $_POST['id_tipoPersona'];
$direccion = $_POST['direccion'];
$celular = $_POST['celular'];
$descripcion = $_POST['descripcion'];




$sentencia = $pdo->prepare("UPDATE tb_personas 
                            SET name_persona=:name_persona,id_tipoPersona=:id_tipoPersona,
                                direccion=:direccion,celular=:celular,
                                descripcion=:descripcion,id_usuario=:id_usuario,
                                fyh_actualizacion=:fyh_actualizacion 
                            WHERE id_persona =:id_persona");

$sentencia->bindParam('name_persona', $name_persona);
$sentencia->bindParam('id_tipoPersona', $id_tipoPersona);
$sentencia->bindParam('direccion', $direccion);
$sentencia->bindParam('celular', $celular);
$sentencia->bindParam('descripcion', $descripcion);
$sentencia->bindParam('id_usuario', $id_usuario);
$sentencia->bindParam('fyh_actualizacion', $fechaHora);
$sentencia->bindParam('id_persona', $id_persona);


if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = 'Se actualizo la informacion de la persona';
    $_SESSION['icono'] = 'success';
    header('Location:' . $URL . '/personas');
} else {
    //echo 'error las constraseñas no son iguales';
    session_start();
    $_SESSION['mensaje'] = 'No se pudo actualizar la informacion';
    $_SESSION['icono'] = 'error';
    header('Location:' . $URL . '/personas/update.php?id=' . $id_persona);
}
