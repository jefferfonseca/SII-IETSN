<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Admin') {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo json_encode(["success" => false, "message" => "ID inválido"]);
    exit;
}

$sql = "SELECT id_elemento, codigo, nombre, tipo, observaciones_generales
        FROM elementos
        WHERE id_elemento = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$elemento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$elemento) {
    echo json_encode(["success" => false, "message" => "Elemento no encontrado"]);
    exit;
}

echo json_encode([
    "success" => true,
    "data" => $elemento
]);
