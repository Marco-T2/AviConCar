<?php

//Para obtener listar usuarios
$sql_gestionactiva = "SELECT * FROM tb_gestion WHERE estado = '1'";
$query_gestionactiva = $pdo->prepare($sql_gestionactiva);
$query_gestionactiva->execute();
$gestionactiva_datos = $query_gestionactiva->fetchAll(PDO::FETCH_ASSOC);

foreach ($gestionactiva_datos as $gestionactiva_dato) {
    $anio_gestion = $gestionactiva_dato['anio_gestion'];
}

$sql_gestion = "SELECT * FROM tb_gestion";
$query_gestion = $pdo->prepare($sql_gestion);
$query_gestion->execute();
$gestion_datos = $query_gestion->fetchAll(PDO::FETCH_ASSOC);


$sql_ultimo_anio = "SELECT MAX(anio_gestion) AS ultimo_anio FROM tb_gestion";
$query_ultimo_anio = $pdo->prepare($sql_ultimo_anio);
$query_ultimo_anio->execute();
$resultado_ultimo_anio = $query_ultimo_anio->fetch(PDO::FETCH_ASSOC);
$ultimo_anio_registrado = $resultado_ultimo_anio['ultimo_anio'] ?? date('Y');
