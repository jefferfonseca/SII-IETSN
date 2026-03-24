<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');

// ============================
// VALIDAR SESIÓN
// ============================
if (
    !isset($_SESSION['usuario']) ||
    !isset($_SESSION['usuario']['id_usuario'])
) {
    echo json_encode([
        'success' => false,
        'message' => 'No autenticado'
    ]);
    exit;
}

$id_usuario = $_SESSION['usuario']['id_usuario'];

// ============================
// LEER JSON
// ============================
$data = json_decode(file_get_contents("php://input"), true);

$qr_token = $data['qr_token'] ?? null;

if (!$qr_token) {
    echo json_encode([
        'success' => false,
        'message' => 'QR no recibido'
    ]);
    exit;
}

try {

    $pdo->beginTransaction();

    // ============================
    // 1. BUSCAR ELEMENTO POR QR
    // ============================
    $stmt = $pdo->prepare("
        SELECT id_elemento 
        FROM elementos 
        WHERE qr_token = ?
        LIMIT 1
    ");
    $stmt->execute([$qr_token]);

    $elemento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$elemento) {
        throw new Exception("Elemento no encontrado");
    }

    $id_elemento = $elemento['id_elemento'];

    // ============================
    // 2. BUSCAR PRÉSTAMO ACTIVO
    // ============================
    $stmt = $pdo->prepare("
        SELECT id 
        FROM prestamos 
        WHERE id_elemento = ?
          AND LOWER(TRIM(estado)) = 'activo'
        LIMIT 1
    ");
    $stmt->execute([$id_elemento]);

    $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prestamo) {
        throw new Exception("El elemento no tiene préstamo activo");
    }

    $id_prestamo = $prestamo['id'];

    // ============================
    // 3. ACTUALIZAR PRÉSTAMO
    // ============================
    $stmt = $pdo->prepare("
        UPDATE prestamos 
        SET estado = 'devuelto',
            fecha_devolucion = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$id_prestamo]);

    // ============================
    // 4. ACTUALIZAR ELEMENTO
    // ============================
    $stmt = $pdo->prepare("
        UPDATE elementos 
        SET estado = 'disponible'
        WHERE id_elemento = ?
    ");
    $stmt->execute([$id_elemento]);

    // ============================
    // 5. INSERTAR BITÁCORA
    // ============================
    $stmt = $pdo->prepare("
        INSERT INTO bitacora 
        (id_elemento, id_usuario, accion, detalle, fecha)
        VALUES (?, ?, 'devolucion', 'Devolución registrada por escaneo QR', NOW())
    ");
    $stmt->execute([$id_elemento, $id_usuario]);

    // ============================
    // FINALIZAR
    // ============================
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Devolución registrada correctamente'
    ]);

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}