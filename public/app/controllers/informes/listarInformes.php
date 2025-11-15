<?php
//Para obtener el listado de cuentas
// Asumiendo que GESTION_ACTIVA es una constante y ya está definida.
$gestion_activa = GESTION_ACTIVA;

$sql_listarSaldoClientes = "SELECT
                                p.id_persona,
                                p.name_persona,
                                tp.name_tipoPersona,
                                COALESCE(MAX(c.fecha_comprobante), 'Sin transacciones') AS fecha_comprobante,
                                COALESCE(SUM(t.debe), 0) - COALESCE(SUM(t.haber), 0) AS saldo
                                FROM 
                                tb_personas p
                                INNER JOIN 
                                tb_tipopersonas tp ON p.id_tipoPersona = tp.id_tipoPersona
                                LEFT JOIN 
                                tb_transacciones t ON p.id_persona = t.id_persona
                                LEFT JOIN 
                                tb_comprobantes c ON t.id_comprobante = c.id_comprobante
                                LEFT JOIN 
                                tb_subcuentas s ON t.id_subcuenta = s.id_subcuenta
                                WHERE 
                                LOWER(tp.name_tipoPersona) = 'clientes-ciudad'
                                AND (c.id_gestion = :gestion_activa OR c.id_gestion IS NULL)
                                AND (s.path LIKE '1.1.2.1%' OR s.path IS NULL)
                                GROUP BY 
                                p.id_persona, p.name_persona, tp.name_tipoPersona
                                ORDER BY 
                                p.name_persona;
                            ";

$query_listarSaldoClientes = $pdo->prepare($sql_listarSaldoClientes);
// Aquí se pasa la variable $gestion_activa en lugar de la constante directamente.
$query_listarSaldoClientes->bindParam(':gestion_activa', $gestion_activa, PDO::PARAM_INT);
$query_listarSaldoClientes->execute();
$listarSaldoClientes_datos = $query_listarSaldoClientes->fetchAll(PDO::FETCH_ASSOC);



$sql_listarSaldoClientesElAlto = "SELECT
                                    p.id_persona,
                                    p.name_persona,
                                    tp.name_tipoPersona,
                                    COALESCE(MAX(c.fecha_comprobante), 'Sin transacciones') AS fecha_comprobante,
                                    COALESCE(SUM(t.debe), 0) - COALESCE(SUM(t.haber), 0) AS saldo
                                    FROM 
                                    tb_personas p
                                    INNER JOIN 
                                    tb_tipopersonas tp ON p.id_tipoPersona = tp.id_tipoPersona
                                    LEFT JOIN 
                                    tb_transacciones t ON p.id_persona = t.id_persona
                                    LEFT JOIN 
                                    tb_comprobantes c ON t.id_comprobante = c.id_comprobante
                                    LEFT JOIN 
                                    tb_subcuentas s ON t.id_subcuenta = s.id_subcuenta
                                    WHERE 
                                    LOWER(tp.name_tipoPersona) = 'clientes-elalto'
                                    AND (c.id_gestion = :gestion_activa OR c.id_gestion IS NULL)
                                    AND (s.path LIKE '1.1.2.1%' OR s.path IS NULL)
                                    GROUP BY 
                                    p.id_persona, p.name_persona, tp.name_tipoPersona
                                    ORDER BY 
                                    p.name_persona;
                            ";

$query_listarSaldoClientesElAlto = $pdo->prepare($sql_listarSaldoClientesElAlto);
// Aquí se pasa la variable $gestion_activa en lugar de la constante directamente.
$query_listarSaldoClientesElAlto->bindParam(':gestion_activa', $gestion_activa, PDO::PARAM_INT);
$query_listarSaldoClientesElAlto->execute();
$listarSaldoClientesElAlto_datos = $query_listarSaldoClientesElAlto->fetchAll(PDO::FETCH_ASSOC);


$sql_listarSaldoClientesPasivos = "SELECT
                                        p.id_persona,
                                        p.name_persona,
                                        tp.name_tipoPersona,
                                        COALESCE(MAX(c.fecha_comprobante), 'Sin transacciones') AS fecha_comprobante,
                                        COALESCE(SUM(t.debe), 0) - COALESCE(SUM(t.haber), 0) AS saldo
                                        FROM 
                                        tb_personas p
                                        INNER JOIN 
                                        tb_tipopersonas tp ON p.id_tipoPersona = tp.id_tipoPersona
                                        LEFT JOIN 
                                        tb_transacciones t ON p.id_persona = t.id_persona
                                        LEFT JOIN 
                                        tb_comprobantes c ON t.id_comprobante = c.id_comprobante
                                        LEFT JOIN 
                                        tb_subcuentas s ON t.id_subcuenta = s.id_subcuenta
                                        WHERE 
                                        LOWER(tp.name_tipoPersona) = 'clientes-pasivos'
                                        AND (c.id_gestion = :gestion_activa OR c.id_gestion IS NULL)
                                        AND (s.path LIKE '1.1.2.1%' OR s.path IS NULL)
                                        GROUP BY 
                                        p.id_persona, p.name_persona, tp.name_tipoPersona
                                        ORDER BY 
                                        p.name_persona;
                            ";

$query_listarSaldoClientesPasivos = $pdo->prepare($sql_listarSaldoClientesPasivos);
// Aquí se pasa la variable $gestion_activa en lugar de la constante directamente.
$query_listarSaldoClientesPasivos->bindParam(':gestion_activa', $gestion_activa, PDO::PARAM_INT);
$query_listarSaldoClientesPasivos->execute();
$listarSaldoClientesPasivos_datos = $query_listarSaldoClientesPasivos->fetchAll(PDO::FETCH_ASSOC);


$sql_listarSaldoClientesPersonal = "SELECT
                                        p.id_persona,
                                        p.name_persona,
                                        tp.name_tipoPersona,
                                        COALESCE(MAX(c.fecha_comprobante), 'Sin transacciones') AS fecha_comprobante,
                                        COALESCE(SUM(t.debe), 0) - COALESCE(SUM(t.haber), 0) AS saldo
                                        FROM 
                                        tb_personas p
                                        INNER JOIN 
                                        tb_tipopersonas tp ON p.id_tipoPersona = tp.id_tipoPersona
                                        LEFT JOIN 
                                        tb_transacciones t ON p.id_persona = t.id_persona
                                        LEFT JOIN 
                                        tb_comprobantes c ON t.id_comprobante = c.id_comprobante
                                        LEFT JOIN 
                                        tb_subcuentas s ON t.id_subcuenta = s.id_subcuenta
                                        WHERE 
                                        LOWER(tp.name_tipoPersona) = 'clientes-personal'
                                        AND (c.id_gestion = :gestion_activa OR c.id_gestion IS NULL)
                                        AND (s.path LIKE '1.1.2.1%' OR s.path IS NULL)
                                        GROUP BY 
                                        p.id_persona, p.name_persona, tp.name_tipoPersona
                                        ORDER BY 
                                        p.name_persona;
                            ";

$query_listarSaldoClientesPersonal = $pdo->prepare($sql_listarSaldoClientesPersonal);
// Aquí se pasa la variable $gestion_activa en lugar de la constante directamente.
$query_listarSaldoClientesPersonal->bindParam(':gestion_activa', $gestion_activa, PDO::PARAM_INT);
$query_listarSaldoClientesPersonal->execute();
$listarSaldoClientesPersonal_datos = $query_listarSaldoClientesPersonal->fetchAll(PDO::FETCH_ASSOC);



$sql_listarSaldoClientesRecuperarSaldos = "SELECT
                                        p.id_persona,
                                        p.name_persona,
                                        tp.name_tipoPersona,
                                        COALESCE(MAX(c.fecha_comprobante), 'Sin transacciones') AS fecha_comprobante,
                                        COALESCE(SUM(t.debe), 0) - COALESCE(SUM(t.haber), 0) AS saldo
                                        FROM 
                                        tb_personas p
                                        INNER JOIN 
                                        tb_tipopersonas tp ON p.id_tipoPersona = tp.id_tipoPersona
                                        LEFT JOIN 
                                        tb_transacciones t ON p.id_persona = t.id_persona
                                        LEFT JOIN 
                                        tb_comprobantes c ON t.id_comprobante = c.id_comprobante
                                        LEFT JOIN 
                                        tb_subcuentas s ON t.id_subcuenta = s.id_subcuenta
                                        WHERE 
                                        LOWER(tp.name_tipoPersona) = 'CuentasRecuperar'
                                        AND (c.id_gestion = :gestion_activa OR c.id_gestion IS NULL)
                                        AND (s.path LIKE '1.1.2.1%' OR s.path IS NULL)
                                        GROUP BY 
                                        p.id_persona, p.name_persona, tp.name_tipoPersona
                                        ORDER BY 
                                        p.name_persona;
                            ";

$query_listarSaldoClientesRecuperarSaldos = $pdo->prepare($sql_listarSaldoClientesRecuperarSaldos);
// Aquí se pasa la variable $gestion_activa en lugar de la constante directamente.
$query_listarSaldoClientesRecuperarSaldos->bindParam(':gestion_activa', $gestion_activa, PDO::PARAM_INT);
$query_listarSaldoClientesRecuperarSaldos->execute();
$listarSaldoClientesRecuperarSaldos_datos = $query_listarSaldoClientesRecuperarSaldos->fetchAll(PDO::FETCH_ASSOC);
