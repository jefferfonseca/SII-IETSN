<?php
require_once __DIR__ . "/../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(["success" => false]);
    exit;
}

$stmt = $pdo->prepare("
    UPDATE tareas_aseo 
    SET estado='ausente' 
    WHERE id=?
");

$stmt->execute([$id]);

echo json_encode([
    "success" => true,
    "message" => "Estudiante marcado como ausente"
]);