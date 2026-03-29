<?php
require_once __DIR__ . "/../config/database.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Datos inválidos"
    ]);
    exit;
}

try {

    foreach ($data as $item) {

        $stmt = $pdo->prepare("
            UPDATE tareas_aseo 
            SET actividad = ?, orden = ? 
            WHERE id = ?
        ");

        $stmt->execute([
            $item['actividad'],
            $item['orden'],
            $item['id']
        ]);
    }

    echo json_encode([
        "success" => true
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}