<?php
define('SERVIDOR', 'db1');
define('USUARIO', 'root');
define('PASSWORD', 'rootpass');
define('BD', 'carmendb');

$servidor = "mysql:dbname=" . BD . ";host=" . SERVIDOR;

try {
    $pdo = new PDO($servidor, USUARIO, PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    //echo "La conexion a la Base de datos fue con exito";
} catch (PDOException $e) {
    //print_r($e);
    echo "Error al conectar a la base de datos";
    exit;
}

$URL = 'https://avicont-carmen.mattbits.com'; // si pruebas local: 'http://localhost:8080'

date_default_timezone_set('America/La_Paz');
$fechaHora = date('Y-m-d H:i:s');
$fecha = date('Y-m-d', strtotime($fechaHora));
$hora = date('H:i', strtotime($fechaHora));
$fechaQuincenal = date('Y-m-d', strtotime("-15 days", strtotime($fechaHora)));

if (!defined('GESTION_ACTIVA')) {
    $sql_gestion = "SELECT id_gestion FROM tb_gestion WHERE estado = 1 LIMIT 1";
    $query_gestion = $pdo->prepare($sql_gestion);
    $query_gestion->execute();
    $gestion = $query_gestion->fetch(PDO::FETCH_ASSOC);

    if ($gestion) {
        define('GESTION_ACTIVA', $gestion['id_gestion']);
    } else {
        error_log("No se pudo encontrar una gestión activa.");
        die("Error crítico: No se pudo determinar la gestión activa.");
    }
}
