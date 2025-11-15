<?php
// Incluir el archivo de conexión a la base de datos
include('../../config.php');

// Función para insertar un nuevo comprobante y sus detalles
function insertarComprobante($id_tipocomprobante, $num_comprobante, $fecha_comprobante, $hora_comprobante, $descripcion, $id_usuario, $detalles_venta, $pdo)
{
    // Iniciar una transacción para asegurar la integridad de los datos
    $pdo->beginTransaction();

    try {
        // Insertar el comprobante en la tabla comprobantes
        $sql_comprobante = "INSERT INTO tb_comprobantes (id_tipocomprobante, num_comprobante, fecha_comprobante, hora_comprobante, descripcion, id_usuario) 
                            VALUES (:id_tipocomprobante, :num_comprobante, :fecha_comprobante, :hora_comprobante, :descripcion, :id_usuario)";
        $stmt_comprobante = $pdo->prepare($sql_comprobante);
        $stmt_comprobante->bindParam(":id_tipocomprobante", $id_tipocomprobante);
        $stmt_comprobante->bindParam(":num_comprobante", $num_comprobante);
        $stmt_comprobante->bindParam(":fecha_comprobante", $fecha_comprobante);
        $stmt_comprobante->bindParam(":hora_comprobante", $hora_comprobante);
        $stmt_comprobante->bindParam(":descripcion", $descripcion);
        $stmt_comprobante->bindParam(":id_usuario", $id_usuario);
        $stmt_comprobante->execute();

        // Obtener el ID del comprobante recién insertado
        $id_comprobante = $pdo->lastInsertId();

        // Insertar los detalles del comprobante en la tabla detalles
        foreach ($detalles_venta as $detalle) {
            $id_subCuenta = $detalle['id_subCuenta'];
            $debe = $detalle['debe'];
            $haber = $detalle['haber'];
            $descripcion_detalle = $detalle['descripcion'];
            $id_persona = $detalle['id_persona'];

            $sql_detalle = "INSERT INTO tb_detallecomprobantes (id_comprobante, id_subCuenta, debe, haber, descripcion, id_persona) 
                            VALUES (:id_comprobante, :id_subCuenta, :debe, :haber, :descripcion, :id_persona)";
            $stmt_detalle = $pdo->prepare($sql_detalle);
            $stmt_detalle->bindParam(":id_comprobante", $id_comprobante);
            $stmt_detalle->bindParam(":id_subCuenta", $id_subCuenta);
            $stmt_detalle->bindParam(":debe", $debe);
            $stmt_detalle->bindParam(":haber", $haber);
            $stmt_detalle->bindParam(":descripcion", $descripcion_detalle);
            $stmt_detalle->bindParam(":id_persona", $id_persona);
            $stmt_detalle->execute();
        }

        // Confirmar la transacción
        $pdo->commit();

        // Retorna true si la transacción fue exitosa
        return true;
    } catch (PDOException $e) {
        // Si ocurre algún error, revertir la transacción y retornar false
        $pdo->rollback();
        return false;
    }
}

// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar que se recibieron todos los datos necesarios
    if (isset($data['id_tipocomprobante'], $data['num_comprobante'], $data['fecha_comprobante'], $data['hora_comprobante'], $data['descripcion'], $data['id_usuario'], $data['detalles_venta'])) {
        // Extraer los datos del array
        $id_tipocomprobante = $data['id_tipocomprobante'];
        $num_comprobante = $data['num_comprobante'];
        $fecha_comprobante = $data['fecha_comprobante'];
        $hora_comprobante = $data['hora_comprobante'];
        $descripcion = $data['descripcion'];
        $id_usuario = $data['id_usuario'];
        $detalles_venta = $data['detalles_venta'];

        // Llamar a la función para insertar el comprobante y sus detalles
        $resultado = insertarComprobante($id_tipocomprobante, $num_comprobante, $fecha_comprobante, $hora_comprobante, $descripcion, $id_usuario, $detalles_venta, $pdo);

        // Preparar la respuesta JSON
        $response = ['success' => $resultado];
    } else {
        // Si faltan datos, preparar una respuesta de error
        $response = ['error' => 'Faltan datos en la solicitud'];
    }

    // Enviar la respuesta JSON al cliente
    header('Content-Type: application/json');
    echo json_encode($response);
}
