<?php
session_start();

// 🔐 Seguridad
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Admin') {
    http_response_code(403);
    exit('No autorizado');
}

// 📥 Parámetro
$ruta = $_GET['ruta'] ?? '';
if (!$ruta) {
    http_response_code(400);
    exit('Ruta no especificada');
}

// 📁 Directorio raíz permitido
$root = realpath(__DIR__ . '/../../etiquetas_generadas');
if ($root === false) {
    http_response_code(500);
    exit('Directorio base no encontrado');
}

// 📁 Directorio solicitado
$basePath = realpath($root . '/' . $ruta);

// 🛡️ Validación de seguridad de ruta
if (
    $basePath === false ||
    !is_dir($basePath) ||
    strpos($basePath, $root) !== 0
) {
    http_response_code(403);
    exit('Ruta no permitida');
}

// 📦 Nombre del ZIP
$zipName = basename($basePath) . '.zip';
$tmpZip = sys_get_temp_dir() . '/' . uniqid('etiquetas_', true) . '.zip';

// 🗜️ Crear ZIP
$zip = new ZipArchive();
if ($zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    exit('No se pudo crear el ZIP');
}

// 📂 Agregar archivos
$files = glob($basePath . '/etiqueta_*.png');
sort($files);

foreach ($files as $file) {
    if (is_file($file)) {
        $zip->addFile($file, basename($file));
    }
}

$zip->close();

// ⬇️ Headers de descarga
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipName . '"');
header('Content-Length: ' . filesize($tmpZip));
header('Pragma: public');
header('Cache-Control: must-revalidate');

// 📤 Enviar archivo
readfile($tmpZip);

// 🧹 Limpiar temporal
unlink($tmpZip);
exit;
