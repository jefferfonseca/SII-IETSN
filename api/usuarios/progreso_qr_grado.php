<?php
header('Content-Type: application/json');

$progresoFile = __DIR__ . '/_progreso_qr_grado.json';

if (!file_exists($progresoFile)) {
    echo json_encode([
        "activo" => false,
        "total" => 0,
        "actual" => 0,
        "completado" => false
    ]);
    exit;
}

echo file_get_contents($progresoFile);
