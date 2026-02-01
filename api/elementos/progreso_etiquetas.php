<?php
header('Content-Type: application/json');

$archivo = __DIR__ . '/_progreso_etiquetas.json';

if (!file_exists($archivo)) {
    echo json_encode(["activo" => false]);
    exit;
}

echo file_get_contents($archivo);
