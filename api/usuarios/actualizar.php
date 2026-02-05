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

$id_usuario = (int)($data["id_usuario"] ?? 0);
$nombre     = trim($data["nombre"] ?? '');
$apellido   = trim($data["apellido"] ?? '');
$rol        = trim($data["rol"] ?? '');
$activo     = isset($data["activo"]) ? (int)$data["activo"] : 1;
$id_grado   = $data["id_grado"] ?? null;

// ============================
// Validaciones
// ============================
if (!$id_usuario || !$nombre || !$apellido || !$rol) {
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

try {

    // ============================
    // Actualizar usuario
    // ============================
    if ($rol === "Estudiante") {

        $sql = "
            UPDATE usuarios
            SET nombre = :nombre,
                apellido = :apellido,
                rol = :rol,
                activo = :activo,
                id_grado = :id_grado
            WHERE id_usuario = :id
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            "nombre"   => $nombre,
            "apellido" => $apellido,
            "rol"      => $rol,
            "activo"   => $activo,
            "id_grado" => $id_grado,
            "id"       => $id_usuario
        ]);

    } else {

        // 🔥 NO estudiante → limpiar grado
        $sql = "
            UPDATE usuarios
            SET nombre = :nombre,
                apellido = :apellido,
                rol = :rol,
                activo = :activo,
                id_grado = NULL
            WHERE id_usuario = :id
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            "nombre"   => $nombre,
            "apellido" => $apellido,
            "rol"      => $rol,
            "activo"   => $activo,
            "id"       => $id_usuario
        ]);
    }

    echo json_encode([
        "success" => true,
        "message" => "Usuario actualizado correctamente"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al actualizar usuario"
    ]);
}
