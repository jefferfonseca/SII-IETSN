let qrScanner = null;

document.getElementById("btnScanQR").addEventListener("click", () => {
  const reader = document.getElementById("reader");
  reader.style.display = "block";

  if (!qrScanner) qrScanner = new Html5Qrcode("reader");

  qrScanner.start(
    { facingMode: "environment" },
    { fps: 20, qrbox: 220 },
    (qrTexto) => {
      // Poner el QR en el hidden
      document.getElementById("documentoQR").value = qrTexto;

      // Mostrar en pantalla
      M.toast({ html: `Documento detectado: ${qrTexto}` });

      // Detener scanner
      qrScanner.stop().then(() => reader.style.display = "none");

      // Enviar el formulario automáticamente
      document.getElementById("formIngreso").submit();
    }
  );
});
