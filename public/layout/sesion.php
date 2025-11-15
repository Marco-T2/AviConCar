<?php

session_start();
if(isset($_SESSION['sesion_user'])){
  //echo 'Si existe sesion de: '.$_SESSION['sesion_user'];
  $usuario =$_SESSION['sesion_user'];
  //Para obtener el nombre del usuario
  $sql = "SELECT a.id_usuario as id_usuario, a.nombres as nombres, 
          a.usuario as usuario ,b.rol as rol FROM tb_usuarios as a 
          INNER JOIN roles as b ON a.id_rol = b.id_rol WHERE usuario = '$usuario';";
  $query = $pdo ->prepare($sql);
  $query->execute();
  $usuarios = $query->fetchAll(PDO::FETCH_ASSOC);
  foreach($usuarios as $usuarios){
      $id_usuario=$usuarios['id_usuario'];
      $nombres_sesion = $usuarios['nombres'];
      $rol_sesion = $usuarios['rol'];
}

}else{
  header('Location: '.$URL.'/login');
  exit;
}

?>