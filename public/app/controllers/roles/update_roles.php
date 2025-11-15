<?php

$id_rol_get = $_GET['id'];


//Para obtener el usuario del ID
$sql_roles = "SELECT * from roles where id_rol = $id_rol_get;";
$query_roles = $pdo->prepare($sql_roles);
$query_roles->execute();
$roles_datos = $query_roles->fetchAll(PDO::FETCH_ASSOC);

foreach ($roles_datos as $roles_datos) {
  $rol = $roles_datos['rol'];
}
