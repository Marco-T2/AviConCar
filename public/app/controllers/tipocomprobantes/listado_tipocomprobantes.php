<?php


try {
    // Preparando la consulta SQL con un filtro para id_categoria
    $sql_tipocomprobantesComprobantes = "SELECT 
                                id_tipocomprobante AS 'id_tipocomprobante', 
                                name_tipocomprobante AS 'name_tipocomprobante', 
                                descripcion AS 'descripcion', 
                                id_categoria AS 'id_categoria'
                             FROM 
                                tb_tipocomprobante
                             WHERE 
                                id_categoria = 1;";  // Filtro aplicado aquí

    // Preparación y ejecución de la consulta
    $query_tipocomprobantesComprobantes = $pdo->prepare($sql_tipocomprobantesComprobantes);
    $query_tipocomprobantesComprobantes->execute();

    // Recuperación de los resultados en forma de array asociativo
    $tipocomprobantesComprobantes_datos = $query_tipocomprobantesComprobantes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Manejo de la excepción
    error_log($e->getMessage());
    die("Error al recuperar los tipos de comprobante con categoría 1: " . $e->getMessage());
}

// Inicio del bloque try-catch para manejar excepciones
try {
    // Preparando la consulta SQL con un filtro para id_categoria
    $sql_tipocomprobantesVentas = "SELECT 
                                id_tipocomprobante AS 'id_tipocomprobante', 
                                name_tipocomprobante AS 'name_tipocomprobante', 
                                descripcion AS 'descripcion', 
                                id_categoria AS 'id_categoria'
                             FROM 
                                tb_tipocomprobante
                             WHERE 
                                id_categoria = 2;";  // Filtro aplicado aquí

    // Preparación y ejecución de la consulta
    $query_tipocomprobantesVentas = $pdo->prepare($sql_tipocomprobantesVentas);
    $query_tipocomprobantesVentas->execute();

    // Recuperación de los resultados en forma de array asociativo
    $tipocomprobantesVentas_datos = $query_tipocomprobantesVentas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Manejo de la excepción
    error_log($e->getMessage());
    die("Error al recuperar los tipos de comprobante con categoría 2: " . $e->getMessage());
}
