<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/_qr_helper.php';

/*************************************************
 * CONFIGURACIÓN GENERAL
 *************************************************/
$backgroundPath = __DIR__ . "/../../etiqueta-base.png";
$logoPath = __DIR__ . "/../../assets/images/Escudo.png";

/*************************************************
 * RESPUESTA BASE
 *************************************************/
$response = [
    "success" => false,
    "message" => "",
    "data" => []
];

/*************************************************
 * VALIDAR PARÁMETROS
 *************************************************/
if (!isset($_GET['id_categoria'])) {
    $response["message"] = "La categoría es obligatoria";
    echo json_encode($response);
    exit;
}

$idCategoria = (int) $_GET['id_categoria'];

try {

    /*************************************************
     * 1️⃣ OBTENER ELEMENTOS + CATEGORÍA
     *************************************************/
    $stmt = $pdo->prepare("
        SELECT 
            e.id_elemento,
            e.codigo,
            e.qr_token,
            c.codigo AS tipo,
            c.nombre AS nombre_categoria
        FROM elementos e
        INNER JOIN categorias c ON c.id_categoria = e.id_categoria
        WHERE e.id_categoria = ?
        ORDER BY e.codigo ASC
    ");
    $stmt->execute([$idCategoria]);
    $elementos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$elementos) {
        throw new Exception("No hay elementos en la categoría");
    }

    /*************************************************
     * 2️⃣ CREAR CARPETA DESTINO
     *************************************************/
    $nombreCategoria = $elementos[0]['nombre_categoria'];
    $nombreCategoriaLimpio = preg_replace(
        '/[^a-zA-Z0-9_-]/',
        '',
        str_replace(' ', '_', $nombreCategoria)
    );

    $nombreCarpeta = "QR-" . $nombreCategoriaLimpio;
    $outputDir = __DIR__ . "/../../etiquetas_generadas/$nombreCarpeta";

    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    /*************************************************
     * 3️⃣ PROGRESO
     *************************************************/
    $progresoFile = __DIR__ . '/_progreso_etiquetas.json';

    file_put_contents($progresoFile, json_encode([
        "activo" => true,
        "total" => count($elementos),
        "actual" => 0,
        "completado" => false
    ]));

    /*************************************************
     * 4️⃣ GENERAR ETIQUETAS
     *************************************************/
    $nuevos = 0;
    $existentes = 0;
    $total = count($elementos);

    foreach ($elementos as $index => $el) {

        file_put_contents($progresoFile, json_encode([
            "activo" => true,
            "total" => $total,
            "actual" => $index + 1,
            "completado" => false
        ]));

        $filename = $outputDir . "/etiqueta_" . $el['id_elemento'] . ".png";

        if (file_exists($filename) && filesize($filename) > 0) {

            $existentes++;

        } else {

            $qrToken = $el['qr_token'];
            if (!$qrToken) {
                throw new Exception("Elemento {$el['codigo']} no tiene qr_token");
            }

            $numero = str_pad($index + 1, 2, "0", STR_PAD_LEFT);

            $qrBin = generarQRLocal($qrToken, $logoPath);

            generarEtiquetaImagick(
                $el['id_elemento'],
                $el['tipo'],
                $numero,
                $qrBin,
                $backgroundPath,
                $outputDir
            );

            if (!file_exists($filename) || filesize($filename) === 0) {
                throw new Exception("Error generando etiqueta {$el['codigo']}");
            }

            $nuevos++;
        }
    }


    file_put_contents($progresoFile, json_encode([
        "activo" => false,
        "total" => count($elementos),
        "actual" => count($elementos),
        "completado" => true
    ]));

    $response["success"] = true;
    $response["message"] = "Etiquetas procesadas correctamente";
    $response["data"] = [
        "total" => $total,
        "nuevos" => $nuevos,
        "existentes" => $existentes,
        "ruta" => "etiquetas_generadas/$nombreCarpeta"
    ];


} catch (Exception $e) {
    $response["message"] = $e->getMessage();
}

echo json_encode($response);


/*************************************************
 * FUNCIÓN: GENERAR ETIQUETA CON IMAGICK
 *************************************************/
function generarEtiquetaImagick(
    int $idElemento,
    string $tipo,
    string $numero,
    string $qrBin,
    string $backgroundPath,
    string $outputDir
): void {

    $base = new Imagick($backgroundPath);

    // TEXTO TIPO
    $drawTipo = new ImagickDraw();
    $drawTipo->setFillColor('#203154');
    $drawTipo->setFont('Arial-Black');
    $drawTipo->setFontSize(580);
    $base->annotateImage($drawTipo, 750, 820, 0, $tipo);

    // TEXTO NÚMERO
    $drawNum = new ImagickDraw();
    $drawNum->setFillColor('#203154');
    $drawNum->setFont('Arial-Black');
    $drawNum->setFontSize(520);
    $base->annotateImage($drawNum, 1100, 1250, 0, $numero);

    // QR
    $qr = new Imagick();
    $qr->readImageBlob($qrBin);
    $qr->resizeImage(920, 920, Imagick::FILTER_LANCZOS, 1);
    $base->compositeImage($qr, Imagick::COMPOSITE_OVER, 1820, 430);

    // Guardar
    $filename = $outputDir . "/etiqueta_" . $idElemento . ".png";
    $base->writeImage($filename);

    $base->clear();
    $qr->clear();
}
