<?php
session_start();
header('Content-Type: application/json');

$response = [
    "success" => true,
    "data" => []
];

$data = json_decode(file_get_contents("php://input"), true);
$ruta = basename($data['ruta'] ?? '');

$base = __DIR__ . "/../../etiquetas_generadas/$ruta";

if (!is_dir($base)) {
    echo json_encode(["success"=>false,"message"=>"Carpeta no existe"]);
    exit;
}

foreach (glob("$base/*") as $f) unlink($f);
rmdir($base);

echo json_encode($response);
exit;