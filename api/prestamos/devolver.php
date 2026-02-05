<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// ============================
// Validar sesión
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

$id_operador = $_SESSION['usuario']['id_usuario'];

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

try {

    $pdo->beginTransaction();

    // ============================
    // Buscar préstamo activo (con bloqueo)
    // ============================
    $stmt = $pdo->prepare("
        SELECT id
        FROM prestamos
        WHERE id_elemento = ?
          AND LOWER(TRIM(estado)) = 'activo'
        FOR UPDATE
        LIMIT 1
    ");
    $stmt->execute([$id_elemento]);
    $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prestamo) {
        throw new Exception('El elemento no tiene un préstamo activo');
    }

    $id_prestamo = $prestamo['id'];

    // ============================
    // Cerrar préstamo
    // ============================
    $stmt = $pdo->prepare("
        UPDATE prestamos
        SET estado = 'devuelto',
            fecha_devolucion = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$id_prestamo]);

    // ============================
    // Liberar elemento
    // ============================
    $stmt = $pdo->prepare("
        UPDATE elementos
        SET estado = 'disponible'
        WHERE id_elemento = ?
    ");
    $stmt->execute([$id_elemento]);

    // ============================
    // Bitácora
    // ============================
    $stmt = $pdo->prepare("
        INSERT INTO bitacora (
            id_elemento,
            id_usuario,
            accion,
            detalle,
            fecha
        ) VALUES (?, ?, 'devolucion', ?, NOW())
    ");
    $stmt->execute([
        $id_elemento,
        $id_operador,
        'Devolución registrada'
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Elemento devuelto correctamente'
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
