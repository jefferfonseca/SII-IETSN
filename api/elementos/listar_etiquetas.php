<?php
session_start();
header('Content-Type: application/json');

// 🔐 Validar sesión
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Admin') {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

$ruta = $_GET['ruta'] ?? '';
$ruta = trim($ruta, '/');

// Ruta física base
$basePath = __DIR__ . "/../../$ruta";

if (!is_dir($basePath)) {
    echo json_encode([
        "success" => true,
        "data" => [],
        "total" => 0
    ]);
    exit;
}

$lista = [];

// Leer archivos
$archivos = scandir($basePath);

foreach ($archivos as $archivo) {

    if (!preg_match('/^etiqueta_\d+\.png$/', $archivo)) {
        continue;
    }

    $path = $basePath . '/' . $archivo;

    if (!is_file($path)) {
        continue;
    }

    $lista[] = [
        "archivo" => $archivo,
        "fecha"   => date("Y-m-d H:i:s", filemtime($path))
    ];
}

echo json_encode([
    "success" => true,
    "data"    => $lista,
    "total"   => count($lista)
]);
