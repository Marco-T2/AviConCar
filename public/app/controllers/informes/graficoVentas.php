<?php
include('../../config.php');  // Asegúrate de que la ruta a config.php es correcta

// Establece el tipo de contenido a JSON para la respuesta
header('Content-Type: application/json');

$pdo = new PDO($servidor, USUARIO, PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

// Asumiendo que GESTION_ACTIVA está definida y contiene el ID de la gestión actual
$id_gestion = GESTION_ACTIVA;

// Prepara la consulta SQL
$sql = "SELECT 
            ventas.fecha,
            ventas.total_ventas,
            COALESCE(cobranzas.total_cobranzas, 0) as total_cobranzas
        FROM
            (SELECT 
                DATE(c.fecha_comprobante) as fecha,
                SUM(t.debe) as total_ventas
            FROM 
                tb_comprobantes c
            JOIN 
                tb_transacciones t ON c.id_comprobante = t.id_comprobante
            WHERE 
                c.id_gestion = :id_gestion AND
                c.id_tipocomprobante IN (SELECT id_tipocomprobante FROM tb_tipocomprobante WHERE id_categoria = 2)
            GROUP BY 
                DATE(c.fecha_comprobante)) as ventas
        LEFT JOIN
            (SELECT 
                DATE(c.fecha_comprobante) as fecha,
                SUM(t.debe) as total_cobranzas
            FROM 
                tb_comprobantes c
            JOIN 
                tb_transacciones t ON c.id_comprobante = t.id_comprobante
            WHERE 
                c.id_gestion = :id_gestion AND
                c.id_tipocomprobante = 6
            GROUP BY 
                DATE(c.fecha_comprobante)) as cobranzas
        ON ventas.fecha = cobranzas.fecha
        ORDER BY 
            ventas.fecha;";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_gestion', $id_gestion, PDO::PARAM_INT);
$stmt->execute();

// Recoger los resultados
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preparar datos para el gráfico
$fechas = [];
$ventas = [];
$cobranzas = [];

foreach ($resultados as $row) {
    $fechas[] = $row['fecha'];
    $ventas[] = $row['total_ventas'];
    $cobranzas[] = $row['total_cobranzas'];
}

// Enviar los datos en formato JSON
echo json_encode([
    'fechas' => $fechas,
    'ventas' => $ventas,
    'cobranzas' => $cobranzas
]);
