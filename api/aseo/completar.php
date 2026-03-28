<?php
require_once __DIR__ . "/../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;

if(!$id){
    echo json_encode([
        "success" => false,
        "message" => "ID inválido"
    ]);
    exit;
}

$stmt = $pdo->prepare("
    UPDATE tareas_aseo 
    SET estado='completado' 
    WHERE id=?
");

$stmt->execute([$id]);

echo json_encode([
    "success" => true
]);