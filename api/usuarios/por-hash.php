<?php
require_once "../config/database.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$hash = $data['doc_hash'] ?? null;

if (!$hash) {
    echo json_encode(['success' => false, 'message' => 'Hash no recibido']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id_usuario, nombre, apellido, documento, rol
    FROM usuarios
    WHERE doc_hash = ?
    AND activo = 1
");
$stmt->execute([$hash]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => $usuario
]);
