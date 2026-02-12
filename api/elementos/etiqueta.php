<?php
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Admin") {
    header("Location: /SII-IETSN/index.html");
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../elementos/_qr_helper.php';

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
   DATOS VISUALES
================================ */
$codigoCategoria = $elemento["codigo_categoria"];
$numero = str_pad($elemento["id_elemento"], 2, "0", STR_PAD_LEFT);

/* ===============================
   GENERAR QR LOCAL
================================ */
$logoPath = __DIR__ . "/../../assets/images/Escudo.png";

try {
    $qrBinario = generarQRLocal($elemento["qr_token"], $logoPath);
    $qrBase64 = base64_encode($qrBinario);
} catch (Exception $e) {
    die("Error al generar QR: " . $e->getMessage());
}

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
