<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../config/database.php";

// Validar sesión
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Admin") {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

try {
    // Préstamos activos
    $sqlPrestamos = "SELECT COUNT(*) AS total FROM prestamos WHERE estado = 'activo'";
    $prestamosActivos = $pdo->query($sqlPrestamos)->fetch()["total"];

    // Elementos disponibles
    $sqlElementos = "SELECT COUNT(*) AS total FROM elementos WHERE estado = 'disponible'";
    $elementosDisponibles = $pdo->query($sqlElementos)->fetch()["total"];

    // Usuarios activos
    $sqlUsuarios = "SELECT COUNT(*) AS total FROM usuarios WHERE activo = 1";
    $usuariosActivos = $pdo->query($sqlUsuarios)->fetch()["total"];

    echo json_encode([
        "success" => true,
        "data" => [
            "prestamos_activos" => $prestamosActivos,
            "elementos_disponibles" => $elementosDisponibles,
            "usuarios_activos" => $usuariosActivos
        ]
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error al obtener resumen del dashboard"
    ]);
    exit;
}
