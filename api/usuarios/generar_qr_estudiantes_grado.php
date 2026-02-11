<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../elementos/_qr_helper.php';
$qrExistentes = 0;
$qrNuevos = 0;

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
$id_grado = $_GET["id_grado"] ?? null;

if (!$id_grado) {
    echo json_encode([
        "success" => false,
        "message" => "Grado no especificado"
    ]);
    exit;
}

/* 🧹 Función para limpiar nombre del grado (seguro para carpetas) */
function limpiarNombreGrado($texto)
{
    $texto = trim($texto);
    $texto = str_replace(' ', '_', $texto);
    return preg_replace('/[^A-Za-z0-9_-]/', '', $texto);
}

/* 📁 Carpeta base (raíz del proyecto) */
$baseQR = dirname(__DIR__, 2) . "/qr_estudiantes";

// Crear carpeta base si no existe
if (!is_dir($baseQR)) {
    mkdir($baseQR, 0775, true);
}

/* 📊 Consulta de estudiantes + nombre del grado */
$sql = "
    SELECT 
        u.id_usuario,
        u.nombre,
        u.apellido,
        u.documento,
        u.doc_hash,
        g.nombre AS nombre_grado
    FROM usuarios u
    INNER JOIN grados g ON g.id_grado = u.id_grado
    WHERE u.rol = 'Estudiante'
      AND u.id_grado = :grado
      AND u.activo = 1
    ORDER BY u.apellido, u.nombre
";

$stmt = $pdo->prepare($sql);
$stmt->execute(["grado" => $id_grado]);
$estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$estudiantes) {
    echo json_encode([
        "success" => true,
        "data" => [],
        "total" => 0
    ]);
    exit;
}

/* 📁 Carpeta por NOMBRE del grado */
$nombreGrado = limpiarNombreGrado($estudiantes[0]["nombre_grado"]);
$dirQR = $baseQR . "/grado_" . $nombreGrado;

if (!is_dir($dirQR)) {
    mkdir($dirQR, 0775, true);
}

/* 🧠 Generación de QR */
$resultado = [];

foreach ($estudiantes as $est) {

    $nombreArchivo = $est["documento"] . ".png";
    $rutaFinal = $dirQR . "/" . $nombreArchivo;

    // Generar QR solo si no existe
    if (file_exists($rutaFinal)) {
        $qrExistentes++;
    } else {
        $qrBinary = generarQRMonkey(
            $est["doc_hash"],
            "https://ietsannicolas.edu.co/images/Escudo.png"
        );
        file_put_contents($rutaFinal, $qrBinary);
        $qrNuevos++;
    }


    $resultado[] = [
        "id" => $est["id_usuario"],
        "nombre" => $est["nombre"] . " " . $est["apellido"],
        "documento" => $est["documento"],
        "qr" => "qr_estudiantes/grado_" . $nombreGrado . "/" . $nombreArchivo
    ];
}

/* ✅ Respuesta final */
echo json_encode([
    "success" => true,
    "data" => $resultado,
    "total" => count($resultado),
    "existentes" => $qrExistentes,
    "nuevos" => $qrNuevos
]);

