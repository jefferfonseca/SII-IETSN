/*************************************************
 * ETIQUETAS POR CATEGORÍA
 *************************************************/
document.addEventListener("DOMContentLoaded", cargarCategorias);

const selectCategoria = document.getElementById("categoria");
const btnGenerar = document.getElementById("btnGenerar");
const btnImprimir = document.getElementById("btnImprimir");
const contenedor = document.getElementById("contenedorEtiquetas");

/*************************************************
 * EVENTOS
 *************************************************/

btnGenerar.addEventListener("click", cargarEtiquetas);
btnImprimir.addEventListener("click", imprimirPrueba);

/*************************************************
 * FUNCIONES
 *************************************************/
function cargarCategorias() {
  fetch("api/elementos/categorias.php")
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        alert(data.message);
        return;
      }

      // Limpiar por si acaso
      selectCategoria.innerHTML =
        '<option value="">-- Seleccione una categoría --</option>';

      data.data.forEach((cat) => {
        const option = document.createElement("option");
        option.value = cat.id_categoria;   // 👈 ID
        option.textContent = cat.nombre;   // 👈 Nombre visible
        selectCategoria.appendChild(option);
      });
    })
    .catch((error) => {
      console.error(error);
      alert("Error al cargar categorías");
    });
}

function cargarEtiquetas() {
  const categoria = selectCategoria.value;

  // Limpieza previa
  contenedor.innerHTML = "";
  btnImprimir.disabled = true;

  if (!categoria) {
    alert("Seleccione una categoría");
    return;
  }

  fetch(
    `api/elementos/etiquetas_por_categoria.php?id_categoria=${encodeURIComponent(categoria)}`,
  )
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        alert(data.message);
        return;
      }

      if (data.data.length === 0) {
        alert("No hay elementos en esta categoría");
        return;
      }

      const grupos = agruparPorCategoria(data.data);

      for (const categoria in grupos) {
        // Encabezado de categoría
        const titulo = document.createElement("h2");
        titulo.className = "titulo-categoria";
        titulo.textContent = categoria;
        contenedor.appendChild(titulo);

        // Contenedor de etiquetas de esa categoría
        const grid = document.createElement("div");
        grid.className = "grupo-etiquetas";

        grupos[categoria].forEach((elemento) => {
          grid.appendChild(crearEtiqueta(elemento));
        });

        contenedor.appendChild(grid);
      }

      btnImprimir.disabled = false;
    })
    .catch((error) => {
      console.error(error);
      alert("Error al cargar las etiquetas");
    });
}

/*************************************************
 * PLANTILLA DE ETIQUETA
 *************************************************/

function crearEtiqueta(elemento) {
  const div = document.createElement("div");
  div.className = "etiqueta";

  div.innerHTML = `
        <div class="etiqueta-header">
            <strong>${elemento.nombre}</strong>
        </div>

        <div class="etiqueta-body">
            <img src="/SII-IETSN/qr-elementos.php?id=${elemento.id_elemento}" alt="QR ${elemento.codigo}">
            <p class="codigo">${elemento.codigo}</p>
            <p class="categoria">${elemento.nombre_categoria}</p>
        </div>
    `;

  return div;
}

function agruparPorCategoria(elementos) {
    const grupos = {};

    elementos.forEach(el => {
        const categoria = el.nombre_categoria || "Sin categoría";

        if (!grupos[categoria]) {
            grupos[categoria] = [];
        }

        grupos[categoria].push(el);
    });

    return grupos;
}

async function imprimirLote(elementos) {
  const contenedor = document.createElement("div");
  contenedor.style.position = "fixed";
  contenedor.style.left = "-9999px";
  document.body.appendChild(contenedor);

  const imagenes = [];

  for (const el of elementos) {
    const etiqueta = document
      .getElementById("plantilla-etiqueta")
      .firstElementChild
      .cloneNode(true);

    etiqueta.querySelector(".codigo").textContent = el.codigo_categoria;
    etiqueta.querySelector(".numero").textContent = el.numero;
    etiqueta.querySelector(".qr-img").src =
      `data:image/svg+xml;base64,${el.qr_base64}`;

    contenedor.appendChild(etiqueta);

    const canvas = await html2canvas(etiqueta, {
      scale: 4,
      backgroundColor: "#ffffff",
      useCORS: true
    });

    imagenes.push(canvas.toDataURL("image/png"));
    etiqueta.remove();
  }

  contenedor.remove();
  abrirVentanaImpresion(imagenes);
}


function abrirVentanaImpresion(imagenes) {
  const win = window.open("", "_blank");

  const imgsHTML = imagenes.map(img => `
    <img src="${img}" style="width:10cm;height:5cm;">
  `).join("");

  win.document.write(`
    <html>
      <head>
        <title>Imprimir etiquetas</title>
        <style>
          body {
            margin: 0;
            display: grid;
            grid-template-columns: repeat(2, 10cm);
            gap: 10px;
            justify-content: center;
          }
          img {
            page-break-inside: avoid;
          }
        </style>
      </head>
      <body onload="window.print(); window.close();">
        ${imgsHTML}
      <script>
const menuElementos = document.getElementById('menu-elementos');
const submenuElementos = document.getElementById('submenu-elementos');

menuElementos.addEventListener('click', () => {
  menuElementos.classList.toggle('open');
  submenuElementos.classList.toggle('open');
});
</script>
</body>    </html>
  `);

  win.document.close();
}

function imprimirPrueba() {
  const categoria = selectCategoria.value;

  if (!categoria) {
    alert("Seleccione una categoría");
    return;
  }

  fetch(`api/elementos/etiquetas_por_categoria.php?id_categoria=${categoria}`)
    .then(r => r.json())
    .then(r => {
      if (!r.success) {
        alert(r.message);
        return;
      }

      // SOLO 2 etiquetas para prueba
      const primerasDos = r.data.slice(0, 2);

      if (primerasDos.length === 0) {
        alert("No hay elementos para imprimir");
        return;
      }

      imprimirLote(primerasDos);
    });
}

