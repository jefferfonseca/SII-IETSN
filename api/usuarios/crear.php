<?php
header("Content-Type: application/json");
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Admin") {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

require_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);

$documento = $data["documento"] ?? null;
$nombre    = $data["nombre"] ?? null;
$apellido  = $data["apellido"] ?? null;
$rol       = $data["rol"] ?? null;
$id_grado  = $data["id_grado"] ?? null;
$activo    = $data["activo"] ?? 1;

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

$sql = "INSERT INTO usuarios 
(documento, nombre, apellido, rol, id_grado, activo)
VALUES (:documento, :nombre, :apellido, :rol, :id_grado, :activo)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    "documento" => $documento,
    "nombre"    => $nombre,
    "apellido"  => $apellido,
    "rol"       => $rol,
    "id_grado"  => $id_grado,
    "activo"    => $activo
]);

echo json_encode([
    "success" => true,
    "message" => "Usuario creado correctamente"
]);
