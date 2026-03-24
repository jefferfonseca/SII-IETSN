<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// ============================
// RESPUESTA
// ============================
function responder($success, $message, $data = []) {
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}

// ============================
// VALIDAR SESIÓN
// ============================
if (
    !isset($_SESSION['usuario']) ||
    !isset($_SESSION['usuario']['id_usuario'])
) {
    responder(false, "Sesión no válida");
}

$id_operador = $_SESSION['usuario']['id_usuario'];

if (!$id_operador) {
    responder(false, "Operador no válido");
}

// ============================
// LEER JSON
// ============================
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    responder(false, "JSON inválido");
}

// ============================
// CAMPOS
// ============================
$qr_token = $input['qr_token'] ?? null;
$id_tomador = $input['id_tomador'] ?? null;
$fecha_devolucion = $input['fecha_devolucion'] ?? null;
$observacion = $input['observacion'] ?? null;

// ============================
// VALIDACIÓN
// ============================
if (!$qr_token || !$id_tomador) {
    responder(false, "Datos incompletos");
}

try {

    $pdo->beginTransaction();

    // ============================
    // OBTENER ELEMENTO
    // ============================
    $stmt = $pdo->prepare("
        SELECT id_elemento, estado
        FROM elementos
        WHERE qr_token = ?
        FOR UPDATE
    ");
    $stmt->execute([$qr_token]);
    $elemento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$elemento) {
        throw new Exception("Elemento no encontrado");
    }

    $id_elemento = $elemento['id_elemento'];
    $estado_actual = trim($elemento['estado']);

    if (strtolower($estado_actual) !== 'disponible') {
        throw new Exception("Elemento no disponible");
    }

    // ============================
    // INSERT PRESTAMO
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
    // UPDATE ELEMENTO
    // ============================
    $stmt = $pdo->prepare("
        UPDATE elementos
        SET estado = 'prestado'
        WHERE id_elemento = ?
    ");
    $stmt->execute([$id_elemento]);

    // ============================
    // BITACORA
    // ============================
    $estado_anterior = $estado_actual;
    $estado_nuevo = 'Prestado';

    $hash_anterior = hash('sha256', $id_elemento . $estado_anterior . microtime(true));
    $hash_actual = hash('sha256', $id_elemento . $estado_nuevo . microtime(true));

    $stmt = $pdo->prepare("
        INSERT INTO bitacora (
            id_elemento,
            id_usuario,
            accion,
            detalle,
            fecha,
            estado_anterior,
            estado_nuevo,
            hash_anterior,
            hash_actual
        ) VALUES (
            ?, ?, ?, ?, NOW(), ?, ?, ?, ?
        )
    ");

    $stmt->execute([
        $id_elemento,
        $id_operador,
        'prestamo',
        'Préstamo registrado',
        $estado_anterior,
        $estado_nuevo,
        $hash_anterior,
        $hash_actual
    ]);

    $pdo->commit();

    responder(true, "Préstamo registrado correctamente");

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    responder(false, $e->getMessage());
}