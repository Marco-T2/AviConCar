<?php

//Para obtener listar usuarios
$sql_usuarios = "SELECT a.id_usuario as id_usuario, a.nombres as nombres, 
                  a.usuario as usuario ,b.rol as rol FROM tb_usuarios as a 
                  INNER JOIN roles as b ON a.id_rol = b.id_rol;";
$query_usuarios = $pdo->prepare($sql_usuarios);
$query_usuarios->execute();
$usuarios_datos = $query_usuarios->fetchAll(PDO::FETCH_ASSOC);
