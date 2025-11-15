<?php

// Asumiendo que $pdo es tu objeto PDO ya creado y configurado

try {
    // Preparando y ejecutando la consulta SQL
    $sql_tipoproductos = "SELECT 
                              id_tipoProducto AS 'id_tipoProducto', 
                              name_tipoProducto AS 'name_tipoProducto', 
                              descripcion AS 'descripcion' 
                           FROM 
                              tb_tipoproducto;";
    $query_tipoproductos = $pdo->prepare($sql_tipoproductos);
    $query_tipoproductos->execute();

    // Recuperación de los resultados
    $tipoproductos_datos = $query_tipoproductos->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Manejo de la excepción
    error_log($e->getMessage());
    // Aquí puedes implementar lógica adicional para la excepción, como mostrar un mensaje de error genérico
    die("Se ha producido un error al intentar obtener los tipos de producto.");
}
