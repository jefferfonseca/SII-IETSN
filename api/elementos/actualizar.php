<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

// 🔐 Validar sesión
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Admin') {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$id_elemento  = (int) ($data['id_elemento'] ?? 0);
$nombre       = trim($data['nombre'] ?? '');
$serial       = trim($data['serial'] ?? '');
$id_categoria = (int) ($data['id_categoria'] ?? 0);
$obs          = trim($data['observaciones_generales'] ?? '');

if (!$id_elemento || !$nombre || !$id_categoria) {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos"
    ]);
    exit;
}

$id_usuario = $_SESSION['usuario']['id_usuario'];

try {
    $pdo->beginTransaction();

    // ============================
    // Obtener elemento actual
    // ============================
    $stmt = $pdo->prepare("
        SELECT estado, serial
        FROM elementos
        WHERE id_elemento = ?
        FOR UPDATE
    ");
    $stmt->execute([$id_elemento]);
    $actual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$actual) {
        throw new Exception("Elemento no encontrado");
    }

    // ============================
    // Regla: no editar si está prestado
    // ============================
    if (strtolower(trim($actual['estado'])) === 'prestado') {
        throw new Exception("No se puede editar un elemento prestado");
    }

    // ============================
    // Validar serial único (si cambia)
    // ============================
    if ($serial !== '') {
        $stmt = $pdo->prepare("
            SELECT 1
            FROM elementos
            WHERE serial = ?
              AND id_elemento <> ?
        ");
        $stmt->execute([$serial, $id_elemento]);

        if ($stmt->fetch()) {
            throw new Exception("El serial ya está registrado");
        }
    }

    // ============================
    // Actualizar elemento
    // ============================
    $stmt = $pdo->prepare("
        UPDATE elementos
        SET
            nombre = ?,
            serial = ?,
            id_categoria = ?,
            observaciones_generales = ?
        WHERE id_elemento = ?
    ");

    $stmt->execute([
        $nombre,
        $serial ?: null,
        $id_categoria,
        $obs,
        $id_elemento
    ]);

    $pdo->commit();

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
