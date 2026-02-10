<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

/* 🔐 Seguridad */
if (
    !isset($_SESSION["usuario"]) ||
    !in_array($_SESSION["usuario"]["rol"], ["Admin", "Docente"])
) {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

/* 📥 Parámetro */
$ruta = $_GET['ruta'] ?? '';
$ruta = trim($ruta);

if ($ruta === '' || strpos($ruta, '..') !== false) {
    echo json_encode([
        "success" => false,
        "message" => "Ruta no permitida"
    ]);
    exit;
}

/* 📁 Directorio base */
$baseDir = dirname(__DIR__, 2) . "/qr_estudiantes";
$dir = realpath($baseDir . '/' . $ruta);

if (
    $dir === false ||
    !is_dir($dir) ||
    strpos($dir, realpath($baseDir)) !== 0
) {
    echo json_encode([
        "success" => true,
        "data" => [],
        "total" => 0
    ]);
    exit;
}

/* 📂 Leer archivos */
$archivos = scandir($dir);
$lista = [];

foreach ($archivos as $archivo) {

    if (!preg_match('/^(\d+)\.png$/', $archivo, $m)) {
        continue;
    }

    $documento = $m[1];
    $path = $dir . '/' . $archivo;

    if (!is_file($path)) continue;

    /* 🔎 Buscar estudiante */
    $stmt = $pdo->prepare("
        SELECT nombre, apellido, documento
        FROM usuarios
        WHERE documento = :doc
        LIMIT 1
    ");
    $stmt->execute(["doc" => $documento]);
    $est = $stmt->fetch(PDO::FETCH_ASSOC);

    $lista[] = [
        "nombre"    => $est
            ? $est["nombre"] . " " . $est["apellido"]
            : "No encontrado",
        "documento" => $documento,
        "archivo"   => $archivo,
        "fecha"     => date("Y-m-d H:i:s", filemtime($path))
    ];
}

/* 📊 Orden natural por nombre */
usort($lista, fn($a, $b) => strcasecmp($a["nombre"], $b["nombre"]));

echo json_encode([
    "success" => true,
    "data"    => $lista,
    "total"   => count($lista)
]);
