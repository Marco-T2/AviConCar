<?php

$id_usuario_get = $_GET['id'];


//Para obtener el usuario del ID
$sql_usuarios = "SELECT a.id_usuario as id_usuario, a.nombres as nombres, 
  a.usuario as usuario ,b.rol as rol , a.fyh_creacion as fyh_creacion FROM tb_usuarios as a 
  INNER JOIN roles as b ON a.id_rol = b.id_rol where id_usuario = $id_usuario_get;";
$query_usuarios = $pdo->prepare($sql_usuarios);
$query_usuarios->execute();
$usuarios_datos = $query_usuarios->fetchAll(PDO::FETCH_ASSOC);

foreach ($usuarios_datos as $usuarios_datos) {
  $nombres = $usuarios_datos['nombres'];
  $usuario = $usuarios_datos['usuario'];
  $rol = $usuarios_datos['rol'];
  $fyh_creacion = $usuarios_datos['fyh_creacion'];
}
