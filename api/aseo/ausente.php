<?php
require_once __DIR__ . "/../config/database.php";
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

$id_usuario = $data['id_usuario'];
$id_grado = $data['id_grado'];
$fecha = date("Y-m-d");

if (!$id_usuario || !$id_grado) {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos"
    ]);
    exit;
}

// 🔥 SOLO AFECTA ASEO
$stmt = $pdo->prepare("
    UPDATE tareas_aseo 
    SET estado='ausente' 
    WHERE id_usuario=? 
    AND id_grado=? 
    ORDER BY fecha DESC
    LIMIT 1
");

$stmt->execute([$id_usuario, $id_grado]);

echo json_encode([
    "success" => true,
    "message" => "Tarea marcada como ausente."
]);