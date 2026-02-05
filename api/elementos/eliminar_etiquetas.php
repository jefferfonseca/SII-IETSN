<?php
session_start();
header('Content-Type: application/json');

// 🔐 Validar sesión y rol
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Admin') {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

$response = [
    "success" => true,
    "data" => []
];

// 📥 Leer JSON de entrada
$data = json_decode(file_get_contents("php://input"), true);
$ruta = basename($data['ruta'] ?? '');

if (!$ruta) {
    echo json_encode([
        "success" => false,
        "message" => "Ruta no especificada"
    ]);
    exit;
}

// 📁 Ruta base esperada
$base = __DIR__ . "/../../etiquetas_generadas/$ruta";

// 🛡️ Blindaje de ruta (evita borrados fuera del directorio permitido)
$root = realpath(__DIR__ . "/../../etiquetas_generadas");
$realBase = realpath($base);

if ($realBase === false || strpos($realBase, $root) !== 0 || !is_dir($realBase)) {
    echo json_encode([
        "success" => false,
        "message" => "Carpeta no existe o ruta no permitida"
    ]);
    exit;
}

// 🧹 Eliminar archivos y carpeta
foreach (glob($realBase . "/*") as $f) {
    if (is_file($f)) {
        unlink($f);
    }
}

rmdir($realBase);

// ✅ Respuesta OK
echo json_encode($response);
exit;
