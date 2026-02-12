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
require_once __DIR__ . "/api/elementos/_qr_helper.php";

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
    header("Location: usuarios.php");
    exit;
}

$documento = $usuarioQR["documento"];
$doc_hash = $usuarioQR["doc_hash"];
$nombreQR = $usuarioQR["nombre"] . " " . $usuarioQR["apellido"];

// ============================
// Generar QR LOCAL (solo hash)
// ============================
$logoPath = __DIR__ . "/assets/images/Escudo.png";

try {
    $qrBinario = generarQRLocal($doc_hash, $logoPath);
    $qrBase64 = base64_encode($qrBinario);
    $success = true;
} catch (Exception $e) {
    $success = false;
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>QR Usuario - <?= htmlspecialchars($nombreQR) ?></title>
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
            <div class="navigation-buttons">
                <button class="menu-toggle" onclick="window.location.href='usuarios.php'" title="Volver a usuarios">
                    <i class="material-icons">arrow_back</i>
                </button>

                <button class="menu-toggle" onclick="toggleSidebar()" title="Menú">
                    <i class="material-icons">menu</i>
                </button>

                <div class="page-title">
                    <div class="page-title-icon">
                        <i class="material-icons">qr_code_2</i>
                    </div>
                    <div>
                        <h4>Código QR de Usuario</h4>
                        <p><?= htmlspecialchars($nombreQR) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="qr-container">

                <!-- Card del QR -->
                <div class="qr-card">
                    <h5>
                        <i class="material-icons">qr_code_scanner</i>
                        Código QR Personal
                    </h5>

                    <div class="qr-display">
                        <img src="data:image/png;base64,<?= $qrBase64 ?>" alt="QR de <?= htmlspecialchars($nombreQR) ?>"
                            id="qrImage">
                    </div>

                    <button class="btn btn-accion waves-effect waves-light" onclick="descargarQR()">
                        <i class="material-icons">file_download</i>
                        Descargar Código QR
                    </button>
                </div>

                <!-- Card de información -->
                <div class="qr-card">
                    <h5>
                        <i class="material-icons">info</i>
                        Información del Usuario
                    </h5>

                    <div class="info-item">
                        <i class="material-icons">badge</i>
                        <div class="info-content">
                            <div class="info-label">Número de Documento</div>
                            <div class="info-value"><?= htmlspecialchars($documento) ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="material-icons">person</i>
                        <div class="info-content">
                            <div class="info-label">Nombre Completo</div>
                            <div class="info-value"><?= htmlspecialchars($nombreQR) ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="material-icons">fingerprint</i>
                        <div class="info-content">
                            <div class="info-label">Token Único (Hash)</div>
                            <div class="info-value"><?= htmlspecialchars($doc_hash) ?></div>
                        </div>
                    </div>

                    <div class="info-note">
                        <i class="material-icons">info</i>
                        <p>
                            <strong>Nota:</strong> Este código QR es único y personal. Puede ser escaneado para identificar
                            al usuario de forma rápida y segura.
                        </p>
                    </div>
                </div>

            </div>
        <?php else: ?>
            <div class="error-card" style="position: relative;">
                <div class="error-icon">
                    <i class="material-icons">error_outline</i>
                </div>
                <h5>Error al Generar el Código QR</h5>
                <p><?= htmlspecialchars($error) ?></p>
                <button class="btn btn-accion" onclick="window.location.href='usuarios.php'"
                    style="max-width: 300px; margin: 25px auto 0;">
                    <i class="material-icons left">arrow_back</i>
                    Volver a Usuarios
                </button>
            </div>
        <?php endif; ?>

    </div>

    <!-- Badge de descarga exitosa -->
    <div class="download-badge" id="downloadBadge">
        <i class="material-icons">check_circle</i>
        <span>¡QR descargado exitosamente!</span>
    </div>

    <!-- Loading overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loader"></div>
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
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.add('active');

            setTimeout(() => {
                const img = document.querySelector('.qr-display img');
                const a = document.createElement('a');
                a.href = img.src;
                a.download = 'qr-usuario-<?= $documento ?>.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);

                loadingOverlay.classList.remove('active');

                const badge = document.getElementById('downloadBadge');
                badge.classList.add('show');

                setTimeout(() => {
                    badge.classList.remove('show');
                }, 3000);

                M.toast({
                    html: '<i class="material-icons left">check_circle</i>Código QR descargado correctamente',
                    classes: 'green rounded',
                    displayLength: 3000
                });
            }, 400);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const qrImage = document.getElementById('qrImage');
            if (qrImage) {
                qrImage.addEventListener('contextmenu', function (e) {
                    e.preventDefault();
                    M.toast({
                        html: 'Use el botón de descarga para guardar el QR',
                        classes: 'orange rounded'
                    });
                });
            }
        });
    </script>

</body>

</html>