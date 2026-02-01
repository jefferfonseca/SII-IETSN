<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../config/database.php";

// Seguridad
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Admin") {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

// Leer JSON
$data = json_decode(file_get_contents("php://input"), true);
$id_usuario = $data["id_usuario"] ?? null;

if (!$id_usuario) {
    echo json_encode([
        "success" => false,
        "message" => "ID inválido"
    ]);
    exit;
}

try {
    // Alternar estado
    $sql = "UPDATE usuarios
            SET activo = IF(activo = 1, 0, 1)
            WHERE id_usuario = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(["id" => $id_usuario]);

    echo json_encode([
        "success" => true,
        "message" => "Estado del usuario actualizado"
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error al actualizar el estado"
    ]);
    exit;
}
