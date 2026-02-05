<?php
require_once "../config/database.php";
header('Content-Type: application/json');

// ============================
// Leer entrada (JSON o texto plano)
// ============================
$raw = trim(file_get_contents('php://input'));

$doc_hash = null;

// Intentar JSON (legacy)
$data = json_decode($raw, true);
if (is_array($data) && !empty($data['doc_hash'])) {
    $doc_hash = trim($data['doc_hash']);
} else {
    // Token plano (pistola / input)
    $doc_hash = $raw;
}

if (!$doc_hash) {
    echo json_encode([
        'success' => false,
        'message' => 'Hash no recibido'
    ]);
    exit;
}

// ============================
// Buscar usuario por hash
// ============================
$stmt = $pdo->prepare("
    SELECT 
        id_usuario,
        nombre,
        apellido,
        rol
    FROM usuarios
    WHERE doc_hash = ?
      AND activo = 1
    LIMIT 1
");
$stmt->execute([$doc_hash]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no encontrado'
    ]);
    exit;
}

// ============================
// OK
// ============================
echo json_encode([
    'success' => true,
    'data' => $usuario
]);
