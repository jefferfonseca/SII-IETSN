<?php
session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// 🔐 Validar sesión
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Admin') {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$codigo       = trim($data['codigo'] ?? '');
$serial       = trim($data['serial'] ?? '');
$nombre       = trim($data['nombre'] ?? '');
$id_categoria = (int) ($data['id_categoria'] ?? 0);
$obs          = trim($data['observaciones_generales'] ?? '');

if (!$codigo || !$nombre || !$id_categoria) {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos"
    ]);
    exit;
}

// Estado inicial
$estado = "Disponible";

// Usuario creador
$id_usuario = $_SESSION['usuario']['id_usuario'];

try {
    $pdo->beginTransaction();

    // ============================
    // Validar código único
    // ============================
    $stmt = $pdo->prepare("
        SELECT 1 FROM elementos WHERE codigo = ?
    ");
    $stmt->execute([$codigo]);

    if ($stmt->fetch()) {
        throw new Exception("El código del elemento ya existe");
    }

    // ============================
    // Validar serial único (si existe)
    // ============================
    if ($serial !== '') {
        $stmt = $pdo->prepare("
            SELECT 1 FROM elementos WHERE serial = ?
        ");
        $stmt->execute([$serial]);

        if ($stmt->fetch()) {
            throw new Exception("El serial ya está registrado");
        }
    }

    // ============================
    // Insertar elemento
    // ============================
    $stmt = $pdo->prepare("
        INSERT INTO elementos
        (codigo, serial, nombre, estado, id_categoria, observaciones_generales, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $codigo,
        $serial ?: null,
        $nombre,
        $estado,
        $id_categoria,
        $obs
    ]);

    $id_elemento = $pdo->lastInsertId();

    // ============================
    // Generar token QR opaco
    // ============================
    $qr_token = bin2hex(random_bytes(16));

    $stmt = $pdo->prepare("
        UPDATE elementos
        SET qr_token = ?
        WHERE id_elemento = ?
    ");
    $stmt->execute([$qr_token, $id_elemento]);

    // ============================
    // Commit
    // ============================
    $pdo->commit();

    echo json_encode([
        "success" => true,
        "qr_token" => $qr_token
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
