<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
// 🔐 Validar sesión
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Admin') {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

$response = [
    "success" => false,
    "message" => "",
    "data" => []
];

try {
    $sql = "
        SELECT DISTINCT
            c.id_categoria,
            c.nombre
        FROM categorias c
        INNER JOIN elementos e
            ON e.id_categoria = c.id_categoria
        ORDER BY c.nombre ASC
    ";

    $stmt = $pdo->query($sql);
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response["success"] = true;
    $response["message"] = "Categorías obtenidas correctamente";
    $response["data"] = $categorias;

} catch (Exception $e) {
    $response["message"] = "Error al obtener categorías";
}

echo json_encode($response);
