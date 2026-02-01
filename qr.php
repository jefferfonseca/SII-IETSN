<?php
session_start();

// Validar sesión
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Admin") {
    header("Location: index.html");
    exit();
}

$usuario = $_SESSION["usuario"];

// Configuración del QR
require_once __DIR__ . "/api/config/database.php";

$id_usuario = $_GET["id"] ?? null;

if (!$id_usuario) {
    die("Usuario no especificado");
}

// Buscar usuario real
$sql = "SELECT documento, nombre, apellido, doc_hash
        FROM usuarios
        WHERE id_usuario = :id
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute(["id" => $id_usuario]);
$usuarioQR = $stmt->fetch();

if (!$usuarioQR) {
    header("Location: ./../usuarios.php");

}

$documento = $usuarioQR["documento"];
$doc_hash = $usuarioQR["doc_hash"];
$nombreQR = $usuarioQR["nombre"] . " " . $usuarioQR["apellido"];


// Información del QR en formato JSON
$infoqr = json_encode([
    "tipo" => "usuario",
    "doc_hash" => $doc_hash
]);

// URL del logo institucional
$logoUrl = "https://ietsannicolas.edu.co/images/Escudo.png";

// Configuración para la API de QR Code Monkey
$data = [
    "data" => $infoqr,
    "config" => [
        "body" => "circular",
        "eye" => "frame6",
        "eyeBall" => "ball6",
        "erf1" => [],
        "erf2" => ["fh"],
        "erf3" => ["fv"],
        "brf1" => [],
        "brf2" => ["fh"],
        "brf3" => ["fv"],
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

// Generar QR mediante API
function generarQR($data)
{
    $url = "https://api.qrcode-monkey.com/qr/custom";
    $jsonData = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de QR - Sistema de Préstamos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/SII-IETSN/css/qr.css">

</head>

<body>
    <!-- Sidebar -->
    <?php
    include 'sidebar.php';
    ?>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="top-bar">
            <div style="display: flex; align-items: center; gap: 20px;">
                <!-- BOTÓN VOLVER -->
                <button class="menu-toggle" onclick="window.location.href = './../usuarios.php';">
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
                        <h4>Generador de Códigos QR</h4>
                        <p>Genera códigos QR personalizados para usuarios</p>
                    </div>
                </div>
            </div>
        </div>


        <!-- QR Content -->
        <?php if ($resultado['success']): ?>
            <div class="qr-container">
                <!-- Visualización del QR -->
                <div class="qr-card">
                    <h5>
                        <i class="material-icons">qr_code_scanner</i>
                        Código QR Generado
                    </h5>
                    <div class="qr-display">
                        <img src="data:image/svg+xml;base64,<?= $resultado['data'] ?>" alt="QR Code" />
                    </div>
                    <button class="btn btn-accion waves-effect" onclick="descargarQR()">
                        <i class="material-icons left">download</i>
                        Descargar QR
                    </button>
                </div>

                <!-- Información del QR -->
                <div class="qr-card">
                    <h5>
                        <i class="material-icons">info</i>
                        Información del Código
                    </h5>

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
                            <div class="info-label">Hash SHA-256</div>
                            <div class="info-value"><?= htmlspecialchars($doc_hash) ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="material-icons">category</i>
                        <div>
                            <div class="info-label">Tipo</div>
                            <div class="info-value">Usuario</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="material-icons">data_object</i>
                        <div>
                            <div class="info-label">Datos JSON</div>
                            <div class="info-value"><?= htmlspecialchars($infoqr) ?></div>
                        </div>
                    </div>

                    <button class="btn btn-accion waves-effect" onclick="generarNuevo()">
                        <i class="material-icons left">refresh</i>
                        Generar Nuevo QR
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="qr-card">
                <div class="error-message">
                    <i class="material-icons">error</i>
                    <h5 style="color: white; margin: 10px 0;">Error al Generar QR</h5>
                    <p style="margin: 0;"><?= htmlspecialchars($resultado['error']) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            if (window.innerWidth <= 992) {
                sidebar.classList.toggle('active');
            } else {
                sidebar.classList.toggle('hidden');
                mainContent.classList.toggle('expanded');
            }
        }

        // Cerrar sidebar en móvil al hacer click fuera
        document.addEventListener('click', function (e) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.menu-toggle');

            if (window.innerWidth <= 992 &&
                sidebar.classList.contains('active') &&
                !sidebar.contains(e.target) &&
                !menuToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });

        // Descargar QR
        function descargarQR() {
            const img = document.querySelector('.qr-display img');
            const link = document.createElement('a');
            link.href = img.src;
            link.download = 'codigo-qr-<?= $documento ?>.svg';
            link.click();

            M.toast({ html: '<i class="material-icons left">check_circle</i>QR descargado exitosamente', classes: 'green rounded' });
        }

        // Generar nuevo QR
        function generarNuevo() {
            M.toast({ html: '<i class="material-icons left">info</i>Función de generación personalizada próximamente', classes: 'blue rounded' });
        }

        // Función para cerrar sesión
        function cerrarSesion() {
            fetch('/SII-IETSN/api/auth/logout.php', {
                method: 'POST',
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    window.location.href = 'index.html';
                })
                .catch(error => {
                    console.error('Error al cerrar sesión:', error);
                    window.location.href = 'index.html';
                });
        }
    </script>
    <script>
        const menuElementos = document.getElementById('menu-elementos');
        const submenuElementos = document.getElementById('submenu-elementos');

        menuElementos.addEventListener('click', () => {
            menuElementos.classList.toggle('open');
            submenuElementos.classList.toggle('open');
        });
    </script>
</body>

</html>