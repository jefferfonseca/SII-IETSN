<!DOCTYPE html>
<html lang="es">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Etiqueta QR - IETSN</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<?php
$imagen_path = 'etiqueta-base.png';
$imagen_data = base64_encode(file_get_contents($imagen_path));
$imagen_base64 = 'data:image/png;base64,' . $imagen_data;
?>
<link rel="stylesheet" href="/SII-IETSN/css/qr-elementos.css">

<style>
    .label {
        background-image: url("<?php echo $imagen_base64; ?>");
    }
</style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🏷️ Generador de Etiquetas QR</h1>
            <p>Institución Educativa Técnica San Nicolás</p>
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
    <img 
      src="/SII-IETSN/etiqueta-base.png" 
      class="label-bg"
      alt="Fondo etiqueta"
    >

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
  `data:image/svg+xml;base64,${datos.qr_base64}`;

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

</script>



</body>

</html>