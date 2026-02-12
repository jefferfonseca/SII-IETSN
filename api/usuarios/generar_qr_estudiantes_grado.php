<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../elementos/_qr_helper.php";

/* ================= FUNCION LIMPIAR NOMBRE ================= */
function limpiarNombreArchivo($texto)
{
    $texto = trim($texto);
    $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $texto);
    $texto = preg_replace('/[^a-zA-Z0-9\s_-]/', '', $texto);
    $texto = preg_replace('/\s+/', ' ', $texto);
    return strtolower($texto);
}

$id_grado = $_GET['id_grado'] ?? null;

if (!$id_grado) {
    echo json_encode([
        "success" => false,
        "message" => "ID de grado requerido"
    ]);
    exit;
}

try {

    /* ================= OBTENER GRADO ================= */
    $stmtGrado = $pdo->prepare("SELECT nombre FROM grados WHERE id_grado = ?");
    $stmtGrado->execute([$id_grado]);
    $grado = $stmtGrado->fetch();

    if (!$grado) {
        echo json_encode([
            "success" => false,
            "message" => "Grado no encontrado"
        ]);
        exit;
    }

    $nombreGrado = preg_replace('/[^A-Za-z0-9]/', '', $grado['nombre']);
    $carpetaFisica = __DIR__ . "/../../qr_estudiantes/grado_" . $nombreGrado;
    $carpetaPublica = "qr_estudiantes/grado_" . $nombreGrado;

    if (!is_dir($carpetaFisica)) {
        mkdir($carpetaFisica, 0777, true);
    }

    /* ================= OBTENER ESTUDIANTES ================= */
    $stmt = $pdo->prepare("
        SELECT id_usuario, nombre, apellido, documento, doc_hash
        FROM usuarios
        WHERE id_grado = ? AND activo = 1
    ");
    $stmt->execute([$id_grado]);
    $estudiantes = $stmt->fetchAll();

    if (!$estudiantes || count($estudiantes) === 0) {
        echo json_encode([
            "success" => false,
            "message" => "No hay estudiantes activos en este grado"
        ]);
        exit;
    }

    $logoPath = __DIR__ . "/../../assets/images/Escudo.png";

    $generados = [];
    $total = count($estudiantes);
    $nuevos = 0;
    $existentes = 0;

    /* ================= PROGRESO ================= */
    $progresoFile = __DIR__ . '/_progreso_qr_grado.json';

    file_put_contents($progresoFile, json_encode([
        "activo" => true,
        "total" => $total,
        "actual" => 0,
        "completado" => false
    ]));

    /* ================= GENERAR QR ================= */
    foreach ($estudiantes as $index => $est) {

        $nombreCompleto = $est['nombre'] . " " . $est['apellido'];
        $nombreArchivo = limpiarNombreArchivo($nombreCompleto) . ".png";
        $rutaCompleta = $carpetaFisica . "/" . $nombreArchivo;

        file_put_contents($progresoFile, json_encode([
            "activo" => true,
            "total" => $total,
            "actual" => $index + 1,
            "completado" => false
        ]));

        if (file_exists($rutaCompleta) && filesize($rutaCompleta) > 0) {

            $existentes++;

        } else {

            $token = $est['doc_hash'];

            if (!$token) {
                throw new Exception("Usuario {$nombreCompleto} no tiene doc_hash");
            }

            $qrImage = generarQRLocal($token, $logoPath);

            if (!$qrImage) {
                throw new Exception("No se pudo generar QR para {$nombreCompleto}");
            }

            file_put_contents($rutaCompleta, $qrImage);

            if (!file_exists($rutaCompleta) || filesize($rutaCompleta) === 0) {
                throw new Exception("Archivo QR vacío para {$nombreCompleto}");
            }

            $nuevos++;
        }

        $generados[] = [
            "nombre" => $nombreCompleto,
            "documento" => $est['documento'],
            "archivo" => $nombreArchivo,
            "ruta" => $carpetaPublica
        ];
    }

    /* ================= FINALIZAR PROGRESO ================= */
    file_put_contents($progresoFile, json_encode([
        "activo" => false,
        "total" => $total,
        "actual" => $total,
        "completado" => true
    ]));

    echo json_encode([
        "success" => true,
        "data" => $generados,
        "total" => count($generados),
        "nuevos" => $nuevos,
        "existentes" => $existentes
    ]);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Error interno",
        "error" => $e->getMessage()
    ]);
}
