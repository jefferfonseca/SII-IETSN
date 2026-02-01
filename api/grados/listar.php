<?php
session_start();
header("Content-Type: application/json");

// Seguridad básica
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Admin") {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

require_once __DIR__ . "/../config/database.php";

try {
    $sql = "SELECT id_grado, nombre 
            FROM grados 
            ORDER BY `grados`.`id_grado` ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al cargar grados"
    ]);
}
