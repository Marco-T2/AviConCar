<?php

// Intenta ejecutar la consulta y manejar cualquier error.
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql_subgrupos = "SELECT sg.*, 
                        g.id_grupo AS id_grupo,
                        g.name_grupo AS name_grupo 
                      FROM tb_subgrupos sg
                      INNER JOIN tb_grupos g ON sg.id_grupo = g.id_grupo
                      ORDER BY sg.id_subgrupo ASC";
    $query_subgrupos = $pdo->prepare($sql_subgrupos);
    $query_subgrupos->execute();
    $subgrupos_datos = $query_subgrupos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("No se pudo realizar la consulta para listar los subgrupos y sus grupos: " . $e->getMessage());
}
