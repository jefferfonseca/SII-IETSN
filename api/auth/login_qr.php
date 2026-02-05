<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../config/database.php";

// ============================
// Leer JSON recibido
// ============================
$input = json_decode(file_get_contents("php://input"), true);

// SOLO token plano
$doc_hash = trim($input["doc_hash"] ?? "");

// ============================
// Validar token
// ============================
if (!$doc_hash) {
    echo json_encode([
        "success" => false,
        "message" => "QR inválido"
    ]);
    exit;
}

try {
    // ============================
    // Buscar usuario por doc_hash
    // ============================
    $sql = "
        SELECT id_usuario, nombre, apellido, rol, activo
        FROM usuarios
        WHERE doc_hash = :doc_hash
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(["doc_hash" => $doc_hash]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode([
            "success" => false,
            "message" => "Usuario no encontrado"
        ]);
        exit;
    }

    // ============================
    // Usuario activo
    // ============================
    if ((int)$usuario["activo"] !== 1) {
        echo json_encode([
            "success" => false,
            "message" => "Usuario inactivo"
        ]);
        exit;
    }

    // ============================
    // Crear sesión (MISMA estructura que login normal)
    // ============================
    $_SESSION["usuario"] = [
        "id_usuario" => $usuario["id_usuario"],
        "nombre"     => $usuario["nombre"],
        "apellido"   => $usuario["apellido"],
        "rol"        => $usuario["rol"],
    ];

    // ============================
    // OK
    // ============================
    echo json_encode([
        "success" => true,
        "message" => "Login QR correcto",
        "data" => [
            "nombre"   => $usuario["nombre"],
            "apellido" => $usuario["apellido"],
            "rol"      => $usuario["rol"],
        ]
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error interno del servidor"
    ]);
    exit;
}
