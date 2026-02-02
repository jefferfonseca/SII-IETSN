<?php
ob_start();
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

// ============================
// Validar sesión
// ============================
if (
    !isset($_SESSION['usuario']) ||
    !isset($_SESSION['usuario']['id_usuario'])
) {
    echo json_encode([
        "success" => false,
        "message" => "Sesión no válida"
    ]);
    exit;
}

$id_operador = $_SESSION['usuario']['id_usuario'];

// ============================
// Leer datos
// ============================
$data = json_decode(file_get_contents('php://input'), true);

$id_tomador = $data['id_tomador'] ?? null;
$id_elemento = $data['id_elemento'] ?? null;
$fecha_devolucion = $data['fecha_devolucion'] ?? null;
$observacion = $data['observacion'] ?? null;

if (!$id_tomador || !$id_elemento) {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos"
    ]);
    exit;
}


try {

    // ============================
    // Iniciar transacción
    // ============================
    $pdo->beginTransaction();

    // ============================
    // Obtener estado del elemento
    // ============================
    $stmt = $pdo->prepare("
        SELECT estado
        FROM elementos
        WHERE id_elemento = ?
        FOR UPDATE
    ");
    $stmt->execute([$id_elemento]);
    $elemento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$elemento) {
        throw new Exception('Elemento no encontrado');
    }

    $estado = strtolower(trim($elemento['estado']));

    if ($estado !== 'disponible') {
        throw new Exception('Elemento no disponible');
    }
    error_log(print_r($_SESSION['usuario'], true));

    // ============================
    // Crear préstamo
    // ============================
    $stmt = $pdo->prepare("
        INSERT INTO prestamos (
            id_tomador,
            id_elemento,
            id_operador,
            fecha_prestamo,
            fecha_devolucion,
            estado,
            observacion
        ) VALUES (
            ?, ?, ?, NOW(), ?, 'activo', ?
        )
    ");
    $stmt->execute([
        $id_tomador,
        $id_elemento,
        $id_operador,
        $fecha_devolucion,
        $observacion
    ]);

    // ============================
    // Actualizar estado del elemento
    // ============================
    $stmt = $pdo->prepare("
        UPDATE elementos
        SET estado = 'prestado'
        WHERE id_elemento = ?
    ");
    $stmt->execute([$id_elemento]);

    // ============================
    // Registrar en bitácora
    // ============================
    $stmt = $pdo->prepare("
        INSERT INTO bitacora (
            id_elemento,
            id_usuario,
            accion,
            detalle,
            fecha
        ) VALUES (
            ?, ?, 'prestamo', ?, NOW()
        )
    ");
    $stmt->execute([
        $id_elemento,
        $id_operador,
        'Préstamo registrado'
    ]);

    // ============================
    // Confirmar transacción
    // ============================
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Préstamo registrado correctamente'
    ]);

} catch (Exception $e) {

    // Rollback si algo falla
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
