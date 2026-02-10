<?php
session_start();

require_once __DIR__ . '/../config/database.php';

/* 🔐 Seguridad */
if (
    !isset($_SESSION["usuario"]) ||
    !in_array($_SESSION["usuario"]["rol"], ["Admin", "Docente"])
) {
    http_response_code(403);
    exit("No autorizado");
}

/* 📥 Parámetro */
$id_grado = $_GET["id_grado"] ?? null;
if (!$id_grado) {
    exit("Grado no especificado");
}

/* 🧹 Limpiar nombre del grado para carpetas/archivos */
function limpiarNombreGrado($texto) {
    $texto = trim($texto);
    $texto = str_replace(' ', '_', $texto);
    return preg_replace('/[^A-Za-z0-9_-]/', '', $texto);
}

/* 📁 Carpeta base QR (raíz del proyecto) */
$baseQR = dirname(__DIR__, 2) . "/qr_estudiantes";

/* 📊 Obtener nombre del grado */
$sqlGrado = "SELECT nombre FROM grados WHERE id_grado = :grado LIMIT 1";
$stmtG = $pdo->prepare($sqlGrado);
$stmtG->execute(["grado" => $id_grado]);
$grado = $stmtG->fetch(PDO::FETCH_ASSOC);

if (!$grado) {
    exit("Grado no encontrado");
}

$nombreGrado = limpiarNombreGrado($grado["nombre"]);

/* 📁 Carpeta por NOMBRE del grado */
$dirQR = $baseQR . "/grado_" . $nombreGrado;

if (!is_dir($dirQR)) {
    exit("No existen códigos QR para este grado");
}

/* 📊 Estudiantes del grado */
$sql = "
    SELECT documento
    FROM usuarios
    WHERE rol = 'Estudiante'
      AND activo = 1
      AND id_grado = :grado
";
$stmt = $pdo->prepare($sql);
$stmt->execute(["grado" => $id_grado]);
$docs = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!$docs) {
    exit("No hay estudiantes para este grado");
}

/* 📦 Crear ZIP */
$zip = new ZipArchive();
$nombreZip = "QR_Grado_" . $nombreGrado . ".zip";
$rutaZip = sys_get_temp_dir() . "/" . $nombreZip;

if ($zip->open($rutaZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    exit("No se pudo crear el ZIP");
}

/* 📂 Agregar archivos al ZIP */
foreach ($docs as $doc) {
    $archivo = $dirQR . "/" . $doc . ".png";
    if (file_exists($archivo)) {
        $zip->addFile($archivo, $doc . ".png");
    }
}

$zip->close();

/* 📤 Descargar ZIP */
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="'.$nombreZip.'"');
header('Content-Length: ' . filesize($rutaZip));

readfile($rutaZip);
unlink($rutaZip);
exit;
