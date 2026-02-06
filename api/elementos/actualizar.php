<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Admin') {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

require_once __DIR__ . '/../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

$id_elemento   = (int)($data['id_elemento'] ?? 0);
$nombre        = trim($data['nombre'] ?? '');
$id_categoria  = (int)($data['id_categoria'] ?? 0);
$obs           = trim($data['observaciones_generales'] ?? '');

if (!$id_elemento || !$nombre || !$id_categoria) {
    echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    exit;
}

$id_usuario = $_SESSION['usuario']['id_usuario'];

try {
    // 1️⃣ Obtener elemento actual
    $stmt = $pdo->prepare("
        SELECT estado, nombre, id_categoria, observaciones_generales
        FROM elementos
        WHERE id_elemento = ?
    ");
    $stmt->execute([$id_elemento]);
    $actual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$actual) {
        echo json_encode(["success" => false, "message" => "Elemento no encontrado"]);
        exit;
    }

    // 2️⃣ Regla: no editar si está prestado
    if (strtolower(trim($actual['estado'])) === 'prestado') {
        echo json_encode([
            "success" => false,
            "message" => "No se puede editar un elemento prestado"
        ]);
        exit;
    }

    // 3️⃣ Actualizar (NO código, NO estado)
    $pdo->prepare("
        UPDATE elementos
        SET nombre = ?, id_categoria = ?, observaciones_generales = ?
        WHERE id_elemento = ?
    ")->execute([
        $nombre,
        $id_categoria,
        $obs,
        $id_elemento
    ]);

       echo json_encode(["success" => true]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al actualizar elemento"
    ]);
}
