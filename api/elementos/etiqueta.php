<?php
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Admin") {
    header("Location: /SII-IETSN/index.html");
    exit;
}

require_once __DIR__ . '/../config/database.php';

$id_elemento = (int) ($_GET["id"] ?? 0);
if (!$id_elemento) {
    die("Elemento no especificado");
}

/* ===============================
   CONSULTAR ELEMENTO + CATEGORÍA
================================ */
$sql = "
SELECT 
    e.id_elemento,
    e.estado,
    e.qr_token,
    c.codigo AS codigo_categoria
FROM elementos e
INNER JOIN categorias c ON c.id_categoria = e.id_categoria
WHERE e.id_elemento = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_elemento]);
$elemento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$elemento) {
    die("Elemento no encontrado");
}

/* ===============================
   VALIDAR ESTADO
================================ */
if (strtolower(trim($elemento["estado"])) !== "disponible") {
    die("El elemento no está disponible para generar QR");
}

/* ===============================
   VALIDAR TOKEN
================================ */
if (!$elemento["qr_token"]) {
    die("El elemento no tiene QR token asignado");
}

/* ===============================
   DATOS VISUALES (NO QR)
================================ */
$codigoCategoria = $elemento["codigo_categoria"];
$numero = str_pad($elemento["id_elemento"], 2, "0", STR_PAD_LEFT);

/* ===============================
   CONFIG QR CODE MONKEY
================================ */
$logoUrl = "https://ietsannicolas.edu.co/images/Escudo.png";

$data = [
    "data" => $elemento["qr_token"], // 🔑 SOLO EL TOKEN
    "config" => [
        "body" => "circular",
        "eye" => "frame6",
        "eyeBall" => "ball6",
        "bodyColor" => "#ffffff",
        "bgColor" => "#ffffff",
        "eye1Color" => "#191938",
        "eye2Color" => "#a3071a",
        "eye3Color" => "#a3071a",
        "eyeBall1Color" => "#a3071a",
        "eyeBall2Color" => "#191938",
        "eyeBall3Color" => "#191938",
        "gradientColor1" => "#191938",
        "gradientColor2" => "#191938",
        "gradientType" => "radial",
        "gradientOnEyes" => false,
        "logo" => $logoUrl
    ],
    "size" => 300,
    "download" => false,
    "file" => "svg"
];

/* ===============================
   FUNCIÓN GENERAR QR (TOKEN)
================================ */
function generarQR(string $qrToken, array $data): string
{
    $url = "https://api.qrcode-monkey.com/qr/custom";

    $jsonData = json_encode($data);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_POSTFIELDS => $jsonData
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        die("Error al generar QR: " . $error);
    }

    curl_close($ch);
    return base64_encode($response);
}

$qrBase64 = generarQR($elemento["qr_token"], $data);

/* ===============================
   RESPUESTA
================================ */
echo json_encode([
    "success" => true,
    "data" => [
        "id_elemento" => $elemento["id_elemento"],
        "codigo_categoria" => $codigoCategoria,
        "numero" => $numero,
        "qr_token" => $elemento["qr_token"],
        "qr_base64" => $qrBase64
    ]
]);
exit;
