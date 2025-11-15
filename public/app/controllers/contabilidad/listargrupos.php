<?php

// Intenta ejecutar la consulta y manejar cualquier error.
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql_grupos = "SELECT * FROM tb_grupos ORDER BY id_grupo ASC";
    $query_grupos = $pdo->prepare($sql_grupos);
    $query_grupos->execute();
    $grupos_datos = $query_grupos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("No se pudo realizar la consulta para listar los grupos: " . $e->getMessage());
}
