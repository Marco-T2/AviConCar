<?php

// Para obtener el listado de cuentas junto con su información relacionada de subgrupos y grupos
$sql_cuentas = "SELECT c.id_cuenta, c.name_cuenta, c.path as cuenta_path, 
                       sg.id_subgrupo, sg.name_subgrupo, sg.path as subgrupo_path, 
                       g.id_grupo, g.name_grupo, g.path as grupo_path
                FROM tb_cuentas c
                INNER JOIN tb_subgrupos sg ON c.id_subgrupo = sg.id_subgrupo
                INNER JOIN tb_grupos g ON sg.id_grupo = g.id_grupo
                ORDER BY c.id_cuenta ASC";
$query_cuentas = $pdo->prepare($sql_cuentas);
$query_cuentas->execute();
$cuentas_datos = $query_cuentas->fetchAll(PDO::FETCH_ASSOC);
