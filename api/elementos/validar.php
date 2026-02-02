<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');

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

$id_elemento = $data['id_elemento'] ?? null;

if (!$id_elemento) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de elemento no recibido'
    ]);
    exit;
}

// ============================
// Consultar elemento
// ============================
$stmt = $pdo->prepare("
    SELECT
        id_elemento,
        nombre,
        codigo,
        LOWER(TRIM(estado)) AS estado
    FROM elementos
    WHERE id_elemento = ?
");
$stmt->execute([$id_elemento]);
$elemento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$elemento) {
    echo json_encode([
        'success' => false,
        'message' => 'Elemento no encontrado'
    ]);
    exit;
}


// ============================
// Validar disponibilidad
// ============================
if ($elemento['estado'] !== 'disponible') {
    echo json_encode([
        'success' => false,
        'message' => 'Elemento no disponible'
    ]);
    exit;
}

// ============================
// OK
// ============================
echo json_encode([
    'success' => true,
    'debug_estado' => $elemento['estado'],
    'data' => $elemento
]);
