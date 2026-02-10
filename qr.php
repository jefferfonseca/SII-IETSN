<?php
session_start();

// ============================
// Validar sesión
// ============================
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Admin") {
    header("Location: index.html");
    exit();
}

require_once __DIR__ . "/api/config/database.php";

// ============================
// Parámetro esperado (TOKEN)
// ============================
$doc_hash = $_GET["hash"] ?? null;

if (!$doc_hash) {
    die("Usuario no especificado");
}

// ============================
// Consultar usuario POR HASH
// ============================
$sql = "
    SELECT documento, nombre, apellido, doc_hash
    FROM usuarios
    WHERE doc_hash = :hash
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute(["hash" => $doc_hash]);
$usuarioQR = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuarioQR) {
    header("Location: ./../usuarios.php");
    exit;
}

$documento = $usuarioQR["documento"];
$doc_hash = $usuarioQR["doc_hash"];
$nombreQR = $usuarioQR["nombre"] . " " . $usuarioQR["apellido"];

// ============================
// QR DATA → SOLO TOKEN PLANO
// ============================
$qrData = $doc_hash;

// ============================
// Logo institucional
// ============================
$logoUrl = "https://ietsannicolas.edu.co/images/Escudo.png";

// ============================
// Config QR Code Monkey
// ============================
$data = [
    "data" => $qrData,
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

// ============================
// Generar QR
// ============================
function generarQR($data)
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
        return ["success" => false, "error" => $error];
    }

    curl_close($ch);
    return ["success" => true, "data" => base64_encode($response)];
}

$resultado = generarQR($data);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>QR Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/SII-IETSN/css/sidebar.css">
    <link rel="stylesheet" href="/SII-IETSN/css/qr.css">

</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content" id="mainContent">

        <div class="top-bar">
            <div style="display:flex;align-items:center;gap:20px;">
                <button class="menu-toggle" onclick="window.location.href='usuarios.php'">
                    <i class="material-icons">arrow_back</i>
                </button>

                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="material-icons">menu</i>
                </button>

                <div class="page-title">
                    <div class="page-title-icon">
                        <i class="material-icons">qr_code_2</i>
                    </div>
                    <div>
                        <h4>QR de Usuario</h4>
                        <p>
                            <?= htmlspecialchars($nombreQR) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($resultado['success']): ?>
            <div class="qr-container">

                <div class="qr-card">
                    <h5><i class="material-icons">qr_code</i> Código QR</h5>

                    <div class="qr-display">
                        <img src="data:image/svg+xml;base64,<?= $resultado['data'] ?>" alt="QR Usuario">
                    </div>

                    <button class="btn btn-accion" onclick="descargarQR()">
                        <i class="material-icons left">download</i>
                        Descargar QR
                    </button>
                </div>

                <div class="qr-card">
                    <h5><i class="material-icons">info</i> Información</h5>

                    <div class="info-item">
                        <i class="material-icons">badge</i>
                        <div>
                            <div class="info-label">Documento</div>
                            <div class="info-value"><?= htmlspecialchars($documento) ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="material-icons">fingerprint</i>
                        <div>
                            <div class="info-label">Token (doc_hash)</div>
                            <div class="info-value"><?= htmlspecialchars($doc_hash) ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="material-icons">qr_code</i>
                        <div>
                            <div class="info-label">Contenido del QR</div>
                            <div class="info-value"><-<?= htmlspecialchars($doc_hash) ?>-></div>
                        </div>
                    </div>
                </div>

            </div>
        <?php else: ?>
            <div class="qr-card red white-text">
                <h5>Error al generar QR</h5>
                <p><?= htmlspecialchars($resultado['error']) ?></p>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('mainContent');
            sidebar.classList.toggle('hidden');
            main.classList.toggle('expanded');
        }

        function descargarQR() {
            const img = document.querySelector('.qr-display img');
            const a = document.createElement('a');
            a.href = img.src;
            a.download = 'qr-usuario-<?= $documento ?>.svg';
            a.click();

            M.toast({ html: 'QR descargado', classes: 'green rounded' });
        }
    </script>

</body>

</html>