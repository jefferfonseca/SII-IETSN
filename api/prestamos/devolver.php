<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');

// ============================
// Validar sesión
// ============================
if (!isset($_SESSION['usuario'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No autenticado'
    ]);
    exit;
}

$id_operador = $_SESSION['usuario']['id'] ?? $_SESSION['usuario']['id_usuario'] ?? null;

if (!$id_operador) {
    echo json_encode([
        'success' => false,
        'message' => 'Operador no identificado'
    ]);
    exit;
}

// ============================
// Leer JSON
// ============================
$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data)) {
    echo json_encode([
        'success' => false,
        'message' => 'JSON inválido'
    ]);
    exit;
}

$id_elemento = $data['id_elemento'] ?? null;

if (!$id_elemento) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de elemento no recibido'
    ]);
    exit;
}

// ============================
// Buscar préstamo activo
// ============================
$stmt = $pdo->prepare("
    SELECT id
    FROM prestamos
    WHERE id_elemento = ?
      AND estado = 'activo'
    LIMIT 1
");
$stmt->execute([$id_elemento]);
$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prestamo) {
    echo json_encode([
        'success' => false,
        'message' => 'El elemento no tiene un préstamo activo'
    ]);
    exit;
}

$id_prestamo = $prestamo['id'];

try {
    $pdo->beginTransaction();

    // ============================
    // 1. Cerrar préstamo
    // ============================
    $stmt = $pdo->prepare("
        UPDATE prestamos
        SET estado = 'devuelto',
            fecha_devolucion = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$id_prestamo]);

    // ============================
    // 2. Liberar elemento
    // ============================
    $stmt = $pdo->prepare("
        UPDATE elementos
        SET estado = 'disponible'
        WHERE id_elemento = ?
    ");
    $stmt->execute([$id_elemento]);

    // ============================
    // 3. Registrar bitácora
    // ============================
    $stmt = $pdo->prepare("
        INSERT INTO bitacora (
            id_elemento,
            id_usuario,
            accion,
            detalle,
            fecha
        ) VALUES (?, ?, 'devolucion', 'Devolución registrada por QR', NOW())
    ");
    $stmt->execute([$id_elemento, $id_operador]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Elemento devuelto correctamente'
    ]);

} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        'success' => false,
        'message' => 'Error al registrar la devolución'
    ]);
}
