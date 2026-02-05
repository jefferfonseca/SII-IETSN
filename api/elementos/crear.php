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

$codigo = trim($data['codigo'] ?? '');
$nombre = trim($data['nombre'] ?? '');
$id_categoria = (int) ($data['id_categoria'] ?? 0);
$obs = trim($data['observaciones_generales'] ?? '');

if (!$codigo || !$nombre || !$id_categoria) {
    echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    exit;
}

// Estado inicial
$estado = "Disponible";

// Usuario creador
$id_usuario = $_SESSION['usuario']['id_usuario'];

try {
    // Validar código único
    $check = $pdo->prepare("SELECT id_elemento FROM elementos WHERE codigo = ?");
    $check->execute([$codigo]);

    if ($check->fetch()) {
        echo json_encode(["success" => false, "message" => "El código ya existe"]);
        exit;
    }

    // Insertar elemento
    $stmt = $pdo->prepare("
        INSERT INTO elementos
        (codigo, nombre, estado, id_categoria, observaciones_generales, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $codigo,
        $nombre,
        $estado,
        $id_categoria,
        $obs
    ]);

    $id_elemento = $pdo->lastInsertId();
    // Generar token QR del elemento (opaco)
    $qr_token = bin2hex(random_bytes(16)); // 32 caracteres
    $updateToken = $pdo->prepare("
    UPDATE elementos
    SET qr_token = ?
    WHERE id_elemento = ?
");
    $updateToken->execute([$qr_token, $id_elemento]);


    // Bitácora
    $detalle = "Elemento creado en estado Disponible";

    $pdo->prepare("
        INSERT INTO bitacora (id_elemento, id_usuario, accion, fecha, detalle)
        VALUES (?, ?, 'CREAR_ELEMENTO', NOW(), ?)
    ")->execute([
                $id_elemento,
                $id_usuario,
                $detalle
            ]);

    echo json_encode([
        "success" => true,
        "qr_token" => $qr_token
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al crear elemento"
    ]);
}
