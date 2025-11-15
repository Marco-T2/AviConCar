<?php

// Inicio del bloque try-catch para el manejo de excepciones de PDO
try {
    // Modificación de la consulta SQL para filtrar por path
    $sql_subcuentasVentas = "SELECT 
                          id_subCuenta AS 'id_subCuenta', 
                          name_subCuenta AS 'name_subCuenta', 
                          path AS 'path', 
                          id_usuario AS 'id_usuario', 
                          id_cuenta AS 'id_cuenta', 
                          fyh_creacion AS 'fyh_creacion', 
                          fyh_actualizacion AS 'fyh_actualizacion' 
                       FROM 
                          tb_subcuentas
                       WHERE 
                          path LIKE '4.%'";

    // Preparación y ejecución de la consulta
    $query_subcuentasVentas = $pdo->prepare($sql_subcuentasVentas);
    $query_subcuentasVentas->execute();

    // Obtención de los resultados
    $subcuentasVentas_datos = $query_subcuentasVentas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Registro del error y manejo de la excepción
    error_log($e->getMessage());
    die("Error al ejecutar la consulta: " . $e->getMessage());
}

try {
    // Modificación de la consulta SQL para filtrar por path
    $sql_subcuentasActivoCorriente = "SELECT 
                          id_subCuenta AS 'id_subCuenta', 
                          name_subCuenta AS 'name_subCuenta', 
                          path AS 'path', 
                          id_usuario AS 'id_usuario', 
                          id_cuenta AS 'id_cuenta', 
                          fyh_creacion AS 'fyh_creacion', 
                          fyh_actualizacion AS 'fyh_actualizacion' 
                       FROM 
                          tb_subcuentas
                          ";

    // Preparación y ejecución de la consulta
    $query_subcuentasActivoCorriente = $pdo->prepare($sql_subcuentasActivoCorriente);
    $query_subcuentasActivoCorriente->execute();

    // Obtención de los resultados
    $subcuentasActivoCorriente_datos = $query_subcuentasActivoCorriente->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Registro del error y manejo de la excepción
    error_log($e->getMessage());
    die("Error al ejecutar la consulta: " . $e->getMessage());
}

