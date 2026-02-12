<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

$id_grado = $_GET["id_grado"] ?? null;

if (!$id_grado) {
    echo json_encode([
        "success" => false,
        "message" => "Grado no especificado"
    ]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM usuarios
    WHERE rol = 'Estudiante'
      AND id_grado = :grado
      AND activo = 1
");

$stmt->execute(["grado" => $id_grado]);

$total = (int) $stmt->fetchColumn();

echo json_encode([
    "success" => true,
    "total" => $total
]);
