<?php
session_start();

// 🔐 Seguridad
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Admin') {
    http_response_code(403);
    exit('No autorizado');
}

$ruta = $_GET['ruta'] ?? '';
if (!$ruta) {
    http_response_code(400);
    exit('Ruta no especificada');
}

$basePath = realpath(__DIR__ . '/../../' . $ruta);
if (!$basePath || !is_dir($basePath)) {
    http_response_code(404);
    exit('Carpeta no encontrada');
}

// Nombre del ZIP
$zipName = basename($basePath) . '.zip';
$tmpZip = sys_get_temp_dir() . '/' . uniqid('etiquetas_', true) . '.zip';

$zip = new ZipArchive();
if ($zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    exit('No se pudo crear el ZIP');
}

// Agregar archivos
$files = glob($basePath . '/etiqueta_*.png');
foreach ($files as $file) {
    $zip->addFile($file, basename($file));
}

$zip->close();

// Headers de descarga
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipName . '"');
header('Content-Length: ' . filesize($tmpZip));
header('Pragma: public');
header('Cache-Control: must-revalidate');

readfile($tmpZip);
unlink($tmpZip);
exit;
?>