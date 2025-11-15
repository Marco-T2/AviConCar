<?php
include('../../config.php');

$id_subCuenta = $_GET['id_subCuenta'];
$name_subcuenta = $_GET['name_subcuenta'];
$id_cuenta = $_GET['id_cuenta'];
$id_usuario = $_GET['id_usuario'];



$sentencia = $pdo->prepare("UPDATE tb_subcuentas 
        SET name_subCuenta=:name_subCuenta,
            id_cuenta=:id_cuenta,
            fyh_actualizacion=:fyh_actualizacion,
            id_usuario=:id_usuario 
        WHERE id_subCuenta =:id_subCuenta");

$sentencia->bindParam('name_subCuenta', $name_subcuenta);
$sentencia->bindParam('id_cuenta', $id_cuenta);
$sentencia->bindParam('id_usuario', $id_usuario);
$sentencia->bindParam('fyh_actualizacion', $fechaHora);
$sentencia->bindParam('id_subCuenta', $id_subCuenta);

if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = 'Se registro la Sub-cuenta';
    $_SESSION['icono'] = 'success';
    //header('Location:' . $URL . '/cuentas');
?>
    <script>
        location.href = "<?php echo $URL ?>/subCuentas";
    </script>


<?php
} else {
    //echo 'error las constraseñas no son iguales';
    session_start();
    $_SESSION['mensaje'] = 'No se pudo registrar la Sub-cuenta';
    $_SESSION['icono'] = 'error';
    //header('Location:' . $URL . '/cuentas');
?>
    <script>
        location.href = "<?php echo $URL ?>/subCuentas";
    </script>

<?php
}
