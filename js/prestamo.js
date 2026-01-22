/*************************************************
 *  ESTADO GLOBAL
 *************************************************/
let tomador = null; // ID del tomador
let tomadorNombre = ""; // Nombre visible

let elementos = []; // [{ id, nombre }]

let qrTomador = null;
let qrElemento = null;
let escaneandoElemento = false;

/*************************************************
 *  UTILIDAD: PARSEAR QR
 *  Acepta:
 *   - JSON → {"id":"PCD01","nombre":"Portátil Dell"}
 *   - Texto → PCD01 - Portátil Dell
 *************************************************/
function parseQR(textoQR) {
  // 1️⃣ Intentar JSON
  try {
    const data = JSON.parse(textoQR);
    return {
      id: data.id || "",
      nombre: data.nombre || textoQR,
    };
  } catch (e) {
    // 2️⃣ Fallback texto plano
    const partes = textoQR.split(" - ");
    return {
      id: partes[0]?.trim() || "",
      nombre: partes[1]?.trim() || textoQR,
    };
  }
}

/*************************************************
 *  ESCANEAR TOMADOR
 *************************************************/
document.getElementById("scanTomador").addEventListener("click", () => {
  const reader = document.getElementById("readerTomador");
  reader.style.display = "block";

  if (!qrTomador) {
    qrTomador = new Html5Qrcode("readerTomador");
  }

  qrTomador.start(
    { facingMode: "environment" },
    {
      fps: 20,
      qrbox: { width: 220, height: 220 },
      aspectRatio: 1.777,
      disableFlip: true,
    },
    (qrTexto) => {
      qrTomador.stop().then(() => {
        reader.style.display = "none";
      });

      const data = parseQR(qrTexto);

      if (!data.id) {
        M.toast({ html: "QR de tomador inválido" });
        return;
      }

      tomador = data.id;
      tomadorNombre = data.nombre;

      document.getElementById("infoTomador").innerText =
        `Tomador: ${tomadorNombre} (${tomador})`;

      M.toast({ html: "Tomador identificado correctamente" });

      // Pasar automáticamente a escaneo de elementos
      iniciarEscaneoElemento();
    },
  );
});

/*************************************************
 *  ESCANEAR ELEMENTOS
 *************************************************/
function iniciarEscaneoElemento() {
  if (escaneandoElemento) return;
  escaneandoElemento = true;

  const reader = document.getElementById("readerElemento");
  reader.style.display = "block";

  if (!qrElemento) {
    qrElemento = new Html5Qrcode("readerElemento");
  }

  qrElemento.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 300 },
    (qrTexto) => {
      const data = parseQR(qrTexto);

      if (!data.id) {
        M.toast({ html: "QR de elemento inválido" });
        return;
      }

      const existe = elementos.some((el) => el.id === data.id);

      if (existe) {
        M.toast({ html: "Elemento ya agregado" });
      } else {
        elementos.push({
          id: data.id,
          nombre: data.nombre,
        });

        renderElementos();
        M.toast({ html: "Elemento agregado" });
      }

      qrElemento.stop().then(() => {
        escaneandoElemento = false;
        reader.style.display = "none";
        document.getElementById("btnAgregarElemento").style.display =
          "inline-block";
      });
    },
  );
}

/*************************************************
 *  BOTÓN AGREGAR MÁS ELEMENTOS
 *************************************************/
document.getElementById("btnAgregarElemento").addEventListener("click", () => {
  iniciarEscaneoElemento();
});

/*************************************************
 *  RENDER LISTA DE ELEMENTOS
 *************************************************/
function renderElementos() {
  const ul = document.getElementById("listaElementos");
  ul.innerHTML = "";

  elementos.forEach((el) => {
    ul.innerHTML += `
      <li class="collection-item">
        <strong>${el.nombre}</strong>
        <span class="grey-text"> (${el.id})</span>
      </li>
    `;
  });
}

/*************************************************
 *  GUARDAR PRÉSTAMO
 *************************************************/
/*************************************************
 *  GUARDAR PRÉSTAMO REAL
 *************************************************/
document.getElementById("guardarPrestamo").addEventListener("click", async () => {
  if (!tomador) {
    M.toast({ html: "No hay tomador seleccionado" });
    return;
  }

  if (elementos.length === 0) {
    M.toast({ html: "No hay elementos en el préstamo" });
    return;
  }

  const data = {
    tomador_id: tomador,
    tomador_nombre: tomadorNombre,
    elementos: elementos, // envia id y nombre
    observaciones: document.getElementById("observaciones").value,
  };

  try {
    const response = await fetch("http://localhost/SII-IETSN/api/prestamos.php?action=registrar", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(data)
    });

    if (!response.ok) throw new Error("Error al guardar el préstamo");

    const result = await response.json();
    console.log("Respuesta backend:", result);

    M.toast({ html: "Préstamo guardado correctamente!" });

    // Limpiar interfaz
    tomador = null;
    tomadorNombre = "";
    elementos = [];
    document.getElementById("infoTomador").innerText = "";
    document.getElementById("listaElementos").innerHTML = "";
    document.getElementById("observaciones").value = "";
    document.getElementById("btnAgregarElemento").style.display = "none";

  } catch (error) {
    console.error(error);
    M.toast({ html: "Ocurrió un error al guardar el préstamo" });
  }
});

