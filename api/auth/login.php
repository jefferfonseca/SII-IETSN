<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/../config/database.php";

// Leer JSON recibido
$input = json_decode(file_get_contents("php://input"), true);

$documento = $input["documento"] ?? null;
$password  = $input["password"] ?? null;

// Validación básica
if (!$documento || !$password) {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos"
    ]);
    exit;
}

try {
    // Buscar usuario por documento
    $sql = "SELECT id_usuario, nombre, apellido, rol, password, activo
            FROM usuarios
            WHERE documento = :documento
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(["documento" => $documento]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        echo json_encode([
            "success" => false,
            "message" => "Usuario no encontrado"
        ]);
        exit;
    }

    // Usuario activo
    if ((int)$usuario["activo"] !== 1) {
        echo json_encode([
            "success" => false,
            "message" => "Usuario inactivo"
        ]);
        exit;
    }

    // Rol permitido
    if ($usuario["rol"] !== "Admin") {
        echo json_encode([
            "success" => false,
            "message" => "Acceso no autorizado"
        ]);
        exit;
    }

    // 🔐 Verificación de contraseña (SHA-256)
    $hashIngresado = hash('sha256', $password);

    if ($hashIngresado !== $usuario["password"]) {
        echo json_encode([
            "success" => false,
            "message" => "Contraseña incorrecta"
        ]);
        exit;
    }

    // Crear sesión (MISMA estructura que QR)
    $_SESSION["usuario"] = [
        "id_usuario" => $usuario["id_usuario"],
        "nombre"     => $usuario["nombre"],
        "apellido"   => $usuario["apellido"],
        "rol"        => $usuario["rol"],
    ];

    echo json_encode([
        "success" => true,
        "message" => "Login correcto",
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
