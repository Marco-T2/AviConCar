<?php

$id_usuario_get = $_GET['id'];

// Validar el ID del usuario
if (!filter_var($id_usuario_get, FILTER_VALIDATE_INT)) {
    die('ID inválido.');
}

// Usar alias en la consulta que coincidan con los nombres de las variables PHP, sin comillas o con comillas invertidas para MySQL
$sql_personas = "SELECT 
                    a.id_persona AS `id_persona`, 
                    a.name_persona AS `name_persona`, 
                    b.name_tipoPersona AS `name_tipoPersona`, 
                    a.direccion AS `direccion`, 
                    a.celular AS `celular`, 
                    a.descripcion AS `descripcion` 
                 FROM 
                    tb_personas AS a 
                 INNER JOIN 
                    tb_tipopersonas AS b 
                 ON 
                    a.id_tipoPersona = b.id_tipoPersona 
                 WHERE 
                    a.id_persona = :id_usuario;";

try {
    $query_personas = $pdo->prepare($sql_personas);
    $query_personas->bindParam(':id_usuario', $id_usuario_get, PDO::PARAM_INT);
    $query_personas->execute();

    // Suponiendo que solo habrá un registro para cada ID
    if ($personas_dato = $query_personas->fetch(PDO::FETCH_ASSOC)) {
        $id_persona = $personas_dato['id_persona'];
        $name_persona = $personas_dato['name_persona'];
        $name_tipoPersona = $personas_dato['name_tipoPersona'];
        $direccion = $personas_dato['direccion'];
        $celular = $personas_dato['celular'];
        $descripcion = $personas_dato['descripcion'];
    } else {
        // Manejar el caso donde no se encuentra el usuario
        echo "No se encontró información para el ID proporcionado.";
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Error al realizar la consulta: ' . $e->getMessage());
}
