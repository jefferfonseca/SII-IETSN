<?php
header("Content-Type: application/json");
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Admin") {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

require_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);

$id_usuario = $data["id_usuario"] ?? null;
$nombre     = $data["nombre"] ?? null;
$apellido   = $data["apellido"] ?? null;
$rol        = $data["rol"] ?? null;
$activo     = isset($data["activo"]) ? (int)$data["activo"] : 1;
$id_grado   = $data["id_grado"] ?? null;

if (!$id_usuario || !$nombre || !$apellido || !$rol) {
    echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    exit;
}

try {

    if ($rol === "Estudiante") {
        $sql = "UPDATE usuarios 
                SET nombre = :nombre,
                    apellido = :apellido,
                    rol = :rol,
                    activo = :activo,
                    id_grado = :id_grado
                WHERE id_usuario = :id";

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
        // 🔥 NO estudiante → grado se limpia
        $sql = "UPDATE usuarios 
                SET nombre = :nombre,
                    apellido = :apellido,
                    rol = :rol,
                    activo = :activo,
                    id_grado = NULL
                WHERE id_usuario = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            "nombre"   => $nombre,
            "apellido" => $apellido,
            "rol"      => $rol,
            "activo"   => $activo,
            "id"       => $id_usuario
        ]);
    }

    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al actualizar usuario"
    ]);
}
