<?php

include('../../config.php');

$name_subcuenta = $_GET['name_subcuenta'];
$id_cuenta = $_GET['id_cuenta'];
$id_usuario = $_GET['id_usuario'];


$sentencia = $pdo->prepare("INSERT INTO tb_subcuentas(name_subCuenta,id_cuenta,id_usuario,
                            fyh_creacion) F
                            VALUES (:name_subCuenta,:id_cuenta,:id_usuario,:fyh_creacion)");

$sentencia->bindParam('name_subCuenta', $name_subcuenta);
$sentencia->bindParam('id_cuenta', $id_cuenta);
$sentencia->bindParam('id_usuario', $id_usuario);
$sentencia->bindParam('fyh_creacion', $fechaHora);

if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = 'Se registro la sub Cuenta';
    $_SESSION['icono'] = 'success';
    //header('Location:' . $URL . '/cuentas');
?>
    <script>
        location.href = "<?php echo $URL ?>/subcuentas";
    </script>


<?php
} else {
    //echo 'error las constraseñas no son iguales';
    session_start();
    $_SESSION['mensaje'] = 'No se pudo registrar sub Cuenta';
    $_SESSION['icono'] = 'error';
    //header('Location:' . $URL . '/cuentas');
?>
    <script>
        location.href = "<?php echo $URL ?>/subcuentas";
    </script>

<?php
}
