<?php

//Para obtener el listado de cuentas
// Para obtener el listado detallado de subcuentas con su respectiva cuenta y grupo
$sql_subcuentas = "SELECT 
                      sc.id_subcuenta, 
                      sc.name_subcuenta, 
                      sc.path as subcuenta_path, 
                      c.id_cuenta, 
                      c.name_cuenta, 
                      c.path as cuenta_path, 
                      sg.id_subgrupo, 
                      sg.name_subgrupo, 
                      sg.path as subgrupo_path, 
                      g.id_grupo, 
                      g.name_grupo, 
                      g.path as grupo_path
                   FROM tb_subcuentas sc
                   INNER JOIN tb_cuentas c ON sc.id_cuenta = c.id_cuenta
                   INNER JOIN tb_subgrupos sg ON c.id_subgrupo = sg.id_subgrupo
                   INNER JOIN tb_grupos g ON sg.id_grupo = g.id_grupo
                   ORDER BY sc.id_subcuenta ASC";
$query_subcuentas = $pdo->prepare($sql_subcuentas);
$query_subcuentas->execute();
$subcuentas_datos = $query_subcuentas->fetchAll(PDO::FETCH_ASSOC);
