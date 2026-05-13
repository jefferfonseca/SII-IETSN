<?php
session_start();

// Validar sesión
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Admin") {
  header("Location: index.html");
  exit();
}

$usuario = $_SESSION["usuario"];
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Etiqueta QR - IETSN</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <?php
  $imagen_path = 'etiqueta-base.png';
  $imagen_data = base64_encode(file_get_contents($imagen_path));
  $imagen_base64 = 'data:image/png;base64,' . $imagen_data;
  ?>
  <link rel="stylesheet" href="/SII-IETSN/css/qr-elementos.css">
  <link rel="stylesheet" href="/SII-IETSN/css/sidebar.css">
  <link rel="stylesheet" href="/SII-IETSN/css/qr.css">

  <style>
    .label {
      background-image: url("<?php echo $imagen_base64; ?>");
    }
  </style>
    <!-- Favicon principal -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">

    <!-- Navegadores modernos (prefieren SVG) -->
    <link rel="icon" type="image/svg+xml" href="assets/images/qr-icon.svg">

    <!-- Ícono para móviles / PWA -->
    <link rel="apple-touch-icon" href="assets/images/icon-192.png">
</head>

<body>
  <!-- Sidebar -->
  <?php
  include 'sidebar.php';
  ?>

  <!-- Main Content -->
  <div class="main-content" id="mainContent">
    <!-- Top Bar -->
    <div class="top-bar">
      <div style="display: flex; align-items: center; gap: 20px;">
        <!-- BOTÓN VOLVER -->
        <button class="menu-toggle" onclick="location.href='/SII-IETSN/elementos.php'">
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

    <div class="card">
      <div class="controls">
        <button class="btn btn-print" onclick="imprimirEtiqueta()">
          <span>🖨️</span>
          <span>Imprimir</span>
        </button>
        <button class="btn btn-download" onclick="descargarEtiqueta()">
          <span>⬇️</span>
          <span>Descargar PNG</span>
        </button>
      </div>

      <div class="label-wrapper">
        <div class="label" id="etiqueta">

          <!-- FONDO REAL (NO background-image) -->
          <img src="/SII-IETSN/etiqueta-base.png" class="label-bg" alt="Fondo etiqueta">

          <!-- CÓDIGO SUPERPUESTO -->
          <div class="label-code">
            <div class="code-text" id="codig">PC</div>
            <div class="code-text" id="numero">01</div>
          </div>

          <!-- QR SUPERPUESTO -->
          <div class="label-qr-container">
            <div class="qr-wrapper">
              <!-- IMPORTANTE: img, no div -->
              <img id="qrcode" alt="QR">
            </div>
          </div>

        </div>
      </div>


      <div class="info-panel">
        <div class="info-row">
          <div class="info-icon">📦</div>
          <span class="info-label">Elemento:</span>
          <span class="info-value" id="info-elemento">PC 01</span>
        </div>
        <div class="info-row">
          <div class="info-icon">🔢</div>
          <span class="info-label">ID:</span>
          <span class="info-value" id="info-id">1</span>
        </div>
        <div class="info-row" id="row-ubicacion" style="display: none;">
          <div class="info-icon">📍</div>
          <span class="info-label">Ubicación:</span>
          <span class="info-value" id="info-ubicacion"></span>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

  <script>
    const params = new URLSearchParams(window.location.search);
    const idElemento = params.get('id');

    fetch(`/SII-IETSN/api/elementos/etiqueta.php?id=${idElemento}`, {
      credentials: "same-origin"
    })
      .then(r => r.json())
      .then(r => {
        if (!r.success) {
          alert("No se pudo cargar la etiqueta");
          return;
        }
        cargarDatos(r.data);
      });

    function cargarDatos(datos) {
      const codigoCompleto = `${datos.codigo_categoria} ${datos.numero}`;

      document.getElementById('codig').textContent = datos.codigo_categoria;
      document.getElementById('numero').textContent = datos.numero;
      document.getElementById('info-elemento').textContent = codigoCompleto;
      document.getElementById('info-id').textContent = datos.id_elemento;

      if (datos.ubicacion) {
        document.getElementById('row-ubicacion').style.display = 'flex';
        document.getElementById('info-ubicacion').textContent = datos.ubicacion;
      }

      // Pintar QR (YA GENERADO)
      document.getElementById("qrcode").src =
        `data:image/png;base64,${datos.qr_base64}`;


    }
    function imprimirEtiqueta() {
      const etiqueta = document.getElementById('etiqueta');

      html2canvas(etiqueta, {
        scale: 4,
        backgroundColor: '#ffffff',
        useCORS: true
      }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');

        const win = window.open('', '_blank');
        win.document.write(`
      <html>
        <head>
          <title>Imprimir etiqueta</title>
          <style>
            body {
              margin: 0;
              display: flex;
              justify-content: center;
              align-items: center;
            }
            img {
              width: 10cm;
              height: 5cm;
            }
          </style>
            <!-- Favicon principal -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">

    <!-- Navegadores modernos (prefieren SVG) -->
    <link rel="icon" type="image/svg+xml" href="assets/images/qr-icon.svg">

    <!-- Ícono para móviles / PWA -->
    <link rel="apple-touch-icon" href="assets/images/icon-192.png">
</head>
        <body onload="window.print(); window.close();">
          <img src="${imgData}">
        </body>
      </html>
    `);
        win.document.close();
      });
    }

    function descargarEtiqueta() {
      const etiqueta = document.getElementById('etiqueta');

      const btn = event.target.closest('.btn-download');
      const originalText = btn.innerHTML;
      btn.innerHTML = '<span>⏳</span><span>Generando...</span>';
      btn.disabled = true;

      html2canvas(etiqueta, { scale: 4 }).then(canvas => {
        const link = document.createElement('a');
        link.download = `etiqueta-${Date.now()}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();

        btn.innerHTML = originalText;
        btn.disabled = false;
      });
    }
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const mainContent = document.getElementById('mainContent');

      if (window.innerWidth <= 992) { sidebar.classList.toggle('active'); } else {
        sidebar.classList.toggle('hidden');
        mainContent.classList.toggle('expanded');
      }
    } 
  </script>
</body>

</html>