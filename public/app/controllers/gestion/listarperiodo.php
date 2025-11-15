<?php

// Asegúrate de que la conexión a la base de datos ($pdo) esté establecida antes de este código.

// Para obtener el listado de periodos
$sql_periodos = "SELECT * FROM tb_periodo;";
$query_periodos = $pdo->prepare($sql_periodos);
$query_periodos->execute();
$periodos_datos = $query_periodos->fetchAll(PDO::FETCH_ASSOC);
