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

// 📥 Parámetro
$ruta = $_GET['ruta'] ?? '';
$ruta = trim($ruta);

// ❌ Evitar traversal
if ($ruta === '' || strpos($ruta, '..') !== false) {
    echo json_encode([
        "success" => false,
        "message" => "Ruta no permitida"
    ]);
    exit;
}

// 📁 Directorio base real
$root = realpath(__DIR__ . '/../../etiquetas_generadas');
if ($root === false) {
    echo json_encode([
        "success" => false,
        "message" => "Directorio base no encontrado"
    ]);
    exit;
}

// 📁 Directorio solicitado
$basePath = realpath($root . '/' . $ruta);

// 🛡️ Validación final
if (
    $basePath === false ||
    !is_dir($basePath) ||
    strpos($basePath, $root) !== 0
) {
    echo json_encode([
        "success" => true,
        "data" => [],
        "total" => 0
    ]);
    exit;
}

$lista = [];

// 📂 Leer archivos
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
        "numero" => (int) preg_replace('/\D/', '', $archivo),
        "fecha" => date("Y-m-d H:i:s", filemtime($path))
    ];
}
usort($lista, function ($a, $b) {
    return $a['numero'] <=> $b['numero'];
});

// ✅ Respuesta
echo json_encode([
    "success" => true,
    "data" => $lista,
    "total" => count($lista)
]);
