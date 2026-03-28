<?php
require_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);

foreach ($data as $item) {

    $stmt = $conn->prepare("
        UPDATE tareas_aseo 
        SET actividad=?, orden=? 
        WHERE id=?
    ");

    $stmt->bind_param("sii", $item['actividad'], $item['orden'], $item['id']);
    $stmt->execute();
}

echo json_encode(["success" => true]);