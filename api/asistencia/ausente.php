<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../helpers/asistencia.php";

$data = json_decode(file_get_contents("php://input"), true);

$id_usuario = $data['id_usuario'];
$id_grado   = $data['id_grado'];
$fecha      = date("Y-m-d");

// 🔥 USAR HELPER (CREA O ACTUALIZA)
registrarAsistencia($pdo, $id_usuario, $id_grado, 'manual');

// 🔥 AHORA SÍ CAMBIAR ESTADO
$stmt = $pdo->prepare("
    UPDATE asistencia 
    SET estado='ausente'
    WHERE id_usuario=? AND id_grado=? AND fecha=?
");

$stmt->execute([$id_usuario, $id_grado, $fecha]);

// 🔁 SINCRONIZAR ASEO
$stmt2 = $pdo->prepare("
    UPDATE tareas_aseo 
    SET estado='ausente' 
    WHERE id_usuario=? AND id_grado=? AND fecha=?
");

$stmt2->execute([$id_usuario, $id_grado, $fecha]);

echo json_encode([
    "success" => true
]);