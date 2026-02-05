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
        'message' => 'Sesión no válida'
    ]);
    exit;
}

// ============================
// Leer y validar JSON
// ============================
$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data)) {
    echo json_encode([
        'success' => false,
        'message' => 'JSON inválido'
    ]);
    exit;
}

// 🔑 Identificador genérico (lector físico / QR / futuro)
$identificador = trim($data['qr_token'] ?? $data['identificador'] ?? '');

if ($identificador === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Identificador inválido'
    ]);
    exit;
}

try {

    $pdo->beginTransaction();

    // ============================
    // Consultar elemento por token
    // ============================
    $stmt = $pdo->prepare("
        SELECT
            id_elemento,
            nombre,
            codigo,
            LOWER(TRIM(estado)) AS estado
        FROM elementos
        WHERE qr_token = ?
        FOR UPDATE
    ");
    $stmt->execute([$identificador]);
    $elemento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$elemento) {
        throw new Exception('Elemento no encontrado');
    }

    // ============================
    // Validar disponibilidad
    // ============================
    if ($elemento['estado'] !== 'disponible') {
        throw new Exception('Elemento no disponible');
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'data' => $elemento
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
