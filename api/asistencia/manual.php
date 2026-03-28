<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../helpers/asistencia.php";

$data = json_decode(file_get_contents("php://input"), true);

$id_usuario = $data['id_usuario'];
$id_grado = $data['id_grado'];

registrarAsistencia($pdo, $id_usuario, $id_grado, 'manual');

echo json_encode([
    "success" => true
]);