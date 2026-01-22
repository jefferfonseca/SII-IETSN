function loginIngenieroPorDocumento(doc) {
  // simulación
  if (doc === "123") {
    M.toast({ html: "Bienvenido Ingeniero" });
    // window.location.href = 'prestamo.html';
  } else {
    M.toast({ html: "Acceso denegado" });
  }
}

document.getElementById("btnBuscar").addEventListener("click", () => {
  const doc = document.getElementById("documento").value;
  loginIngenieroPorDocumento(doc);
});

function procesarLoginQR(qrData) {
  M.toast({ html: qrData });

  // Simulación: aquí luego va el backend
  if (qrData === "INGE01") {
    M.toast({ html: "Ingreso autorizado" });
    window.location.href = 'prestamo.html';
  } else {
    M.toast({ html: "QR no autorizado" });
  }
}
