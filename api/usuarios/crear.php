<?php
header("Content-Type: application/json");
session_start();

// ============================
// Validar sesión
// ============================
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Admin") {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

require_once "../config/database.php";

// ============================
// Leer JSON
// ============================
$data = json_decode(file_get_contents("php://input"), true);

$documento = trim($data["documento"] ?? '');
$nombre    = trim($data["nombre"] ?? '');
$apellido  = trim($data["apellido"] ?? '');
$rol       = trim($data["rol"] ?? '');
$id_grado  = $data["id_grado"] ?? null;
$activo    = isset($data["activo"]) ? (int)$data["activo"] : 1;

// ============================
// Validaciones
// ============================
if (!$documento || !$nombre || !$apellido || !$rol) {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos"
    ]);
    exit;
}

if ($rol === "Estudiante" && !$id_grado) {
    echo json_encode([
        "success" => false,
        "message" => "El grado es obligatorio para estudiantes"
    ]);
    exit;
}

// ============================
// Generar HASH del documento
// ============================
// 64 caracteres, seguro para QR
$doc_hash = hash('sha256', $documento);

// ============================
// Verificar duplicado
// ============================
$check = $pdo->prepare("
    SELECT id_usuario 
    FROM usuarios 
    WHERE doc_hash = ?
");
$check->execute([$doc_hash]);

if ($check->fetch()) {
    echo json_encode([
        "success" => false,
        "message" => "El usuario ya existe"
    ]);
    exit;
}

// ============================
// Insertar usuario
// ============================
$sql = "
    INSERT INTO usuarios (
        documento,
        doc_hash,
        nombre,
        apellido,
        rol,
        id_grado,
        activo,
        created_at
    ) VALUES (
        :documento,
        :doc_hash,
        :nombre,
        :apellido,
        :rol,
        :id_grado,
        :activo,
        NOW()
    )
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    "documento" => $documento,
    "doc_hash"  => $doc_hash,
    "nombre"    => $nombre,
    "apellido"  => $apellido,
    "rol"       => $rol,
    "id_grado"  => $id_grado,
    "activo"    => $activo
]);

// ============================
// OK
// ============================
echo json_encode([
    "success"  => true,
    "message"  => "Usuario creado correctamente",
    "doc_hash" => $doc_hash
]);
