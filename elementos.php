<?php
session_start();

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
  <title>Elementos - Sistema de Préstamos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/SII-IETSN/css/sidebar.css">
  <link rel="stylesheet" href="/SII-IETSN/css/usuario.css">
  <link rel="stylesheet" href="/SII-IETSN/css/elementos.css">
    <!-- Favicon principal -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">

    <!-- Navegadores modernos (prefieren SVG) -->
    <link rel="icon" type="image/svg+xml" href="assets/images/qr-icon.svg">

    <!-- Ícono para móviles / PWA -->
    <link rel="apple-touch-icon" href="assets/images/icon-192.png">
</head>

<body>

  <!-- SIDEBAR -->
  <?php
  include 'sidebar.php';
  ?>

  <!-- CONTENIDO -->
  <div class="main-content" id="mainContent">
    <div class="top-bar">
      <div class="page-title">
        <button class="menu-toggle" onclick="toggleSidebar()">
          <i class="material-icons">menu</i>
        </button>
        <div class="page-title-icon">
          <i class="material-icons">inventory_2</i>
        </div>
        <div>
          <h4>Inventario de Elementos</h4>
          <p>Gestión de elementos prestables</p>
        </div>
      </div>

      <div class="elementos-filtros" style="display:flex; gap:15px; flex-wrap:wrap; margin-bottom:20px">

        <div class="input-field">
          <input id="buscarElemento" type="text">
          <label for="buscarElemento">Buscar</label>
        </div>

        <div class="input-field" style="fle:1">
          <select id="filtroCategoria" style=" height: 20px; width:90%;" >
            <option style=" height: 20px; width:90%;" value="">Todas las categorías</option>
          </select>
          <label>Categoría</label>
        </div>

        <div class="input-field" style="flex:1">
          <select id="filtroEstado" style=" height: fit-content; width:90%;" >
            <option value="" selected>Todos</option>
            <option value="Disponible">Disponible</option>
            <option value="Prestado">Prestado</option>
            <option value="Mantenimiento">Mantenimiento</option>
            <option value="Fuera de servicio">Fuera de servicio</option>
          </select>
          <label>Estado</label>
        </div>

      </div>

      <a class="btn btn-guardar waves-effect" onclick="abrirModalCrearElemento()">
        <i class="material-icons left">add</i>
        Agregar Elemento
      </a>

    </div>

    <!-- CONTENEDOR DE ELEMENTOS -->
    <div class="elementos-lista" id="elementosContainer"></div>

    <!-- Modal Crear / Editar Elemento -->
    <div id="modalElemento" class="modal">
      <form id="formElemento">

        <div class="modal-content">

          <!-- Header -->
          <div class="modal-header"
            style="display:flex; justify-content:space-between; align-items:flex-start; gap:20px">

            <div style="display:flex; gap:15px; align-items:flex-start">
              <div class="modal-icon">
                <i class="material-icons">inventory_2</i>
              </div>
              <div class="modal-title">
                <h5 id="tituloModalElemento">Editar Elemento</h5>
                <p id="subtituloModalElemento">Actualiza la información del elemento</p>
              </div>
            </div>

            <!-- BADGE ESTADO -->
            <span id="badgeEstadoElemento" class="badge-estado-modal">
              Disponible
            </span>

          </div>

          <!-- ID oculto -->
          <input type="hidden" id="id_elemento">

          <!-- Código -->
          <div class="row">
            <div class="input-field col s12">
              <input id="codigo" type="text" required>
              <label for="codigo">Código del elemento *</label>
            </div>
          </div>
          <!-- Serial -->
          <div class="row">
            <div class="input-field col s12">
              <input id="serial" type="text">
              <label for="serial">Serial del equipo</label>
            </div>
          </div>

          <!-- Nombre -->
          <div class="row">
            <div class="input-field col s12">
              <input id="nombre" type="text" required>
              <label for="nombre">Nombre del elemento *</label>
            </div>
          </div>

          <!-- Categoría -->
          <div class="row">
            <div class="input-field col s12">
              <select id="id_categoria" required>
                <option value="" disabled selected>Seleccione una categoría</option>
              </select>
              <label>Categoría *</label>
            </div>
          </div>


          <!-- Observaciones -->
          <div class="row">
            <div class="input-field col s12">
              <textarea id="observaciones_generales" class="materialize-textarea"></textarea>
              <label for="observaciones_generales">Observaciones generales</label>
            </div>
          </div>

        </div>

        <!-- Footer (DENTRO del form) -->
        <div class="modal-footer">
          <a href="#!" class="modal-close btn btn-accion btn-cancelar">Cancelar</a>

          <button type="submit" id="btnGuardarElemento" class="btn btn-guardar">
            <i class="material-icons left">save</i>
            Guardar Elemento
          </button>
        </div>

      </form>
    </div>

    <!-- Modal Confirmación Acción -->
    <div id="modalConfirmacion" class="modal">
      <div class="modal-content">
        <h5 id="confirmTitulo">Confirmar acción</h5>
        <p id="confirmTexto"></p>
      </div>
      <div class="modal-footer">
        <a href="#!" class="modal-close btn btn-cancelar">Cancelar</a>
        <a href="#!" id="btnConfirmarAccion" class="btn btn-guardar">
          Confirmar
        </a>
      </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
      let elementosCache = {};
      let debounceTimer = null;

      document.addEventListener("DOMContentLoaded", () => {
        cargarCategorias();
        cargarElementos();

        M.FormSelect.init(document.querySelectorAll('select'));
        M.Modal.init(document.querySelectorAll('.modal'));

        document.getElementById('buscarElemento')?.addEventListener('input', () => {
          clearTimeout(debounceTimer);
          debounceTimer = setTimeout(cargarElementos, 300);
        });

        document.getElementById('filtroCategoria')?.addEventListener('change', cargarElementos);
        document.getElementById('filtroEstado')?.addEventListener('change', cargarElementos);

        document.getElementById('buscarElemento')
          ?.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(cargarElementos, 300);
          });
      });


      /* ===============================
         LISTAR ELEMENTOS
      =============================== */
  function cargarElementos() {
  const buscar = document.getElementById("buscarElemento")?.value.trim() || "";
  const id_categoria = document.getElementById("filtroCategoria")?.value || "";
  const estado = document.getElementById("filtroEstado")?.value || "";

  const params = new URLSearchParams();
  if (buscar) params.append("buscar", buscar);
  if (id_categoria) params.append("id_categoria", id_categoria);
  if (estado) params.append("estado", estado);

  fetch("/SII-IETSN/api/elementos/listar.php?" + params.toString(), {
    credentials: "same-origin"
  })
    .then(res => res.json())
    .then(res => {
      if (!res.success) {
        M.toast({ html: "Error cargando elementos", classes: "red" });
        return;
      }

      const cont = document.getElementById("elementosContainer");
      
      // ===== ESTADO VACÍO =====
      if (!res.data || res.data.length === 0) {
        cont.innerHTML = `
          <div class="empty-state">
            <i class="material-icons">inventory_2</i>
            <h5>No se encontraron elementos</h5>
            <p>Intenta ajustar los filtros o agrega un nuevo elemento</p>
          </div>
        `;
        return;
      }

      // ===== CABECERA DE LA TABLA =====
      let html = `
        <div class="elementos-header">
          <div>Elemento</div>
          <div>Código / Serial</div>
          <div>Categoría</div>
          <div>Estado</div>
          <div>Acciones</div>
        </div>
      `;

      // Limpiar caché
      elementosCache = {};

      // ===== FILAS DE ELEMENTOS =====
      res.data.forEach((el, index) => {
        elementosCache[el.id_elemento] = el;

        const estado = el.estado;
        const qrHabilitado = estado === "Disponible";

        // Determinar clase del badge
        let badgeClass = "badge-disponible";
        if (estado === "Prestado") badgeClass = "badge-prestado";
        else if (estado === "Mantenimiento") badgeClass = "badge-mantenimiento";
        else if (estado === "Fuera de servicio") badgeClass = "badge-fuera";

        // Botones según estado
        let btnMantenimiento = "";
        let btnToggle = "";

        if (estado === "Disponible") {
          btnMantenimiento = `
            <button class="btn-accion btn-mantenimiento" 
                    onclick="event.stopPropagation(); confirmarAccion(
                      'Enviar a mantenimiento',
                      'El elemento quedará no disponible hasta que se reactive. ¿Continuar?',
                      () => ponerMantenimiento(${el.id_elemento})
                    )"
                    title="Marcar como en mantenimiento">
              <i class="material-icons icon-mant">build</i>
            </button>`;
        }

        if (estado === "Disponible") {
          btnToggle = `
            <button class="btn-accion btn-eliminar" 
                    onclick="event.stopPropagation(); confirmarAccion(
                      'Confirmar cambio de estado',
                      '¿Seguro que deseas cambiar el estado de este elemento?',
                      () => toggleEstado(${el.id_elemento})
                    )"
                    title="Desactivar elemento">
              <i class="material-icons">block</i>
            </button>`;
        } else if (estado === "Mantenimiento" || estado === "Fuera de servicio") {
          btnToggle = `
            <button class="btn-accion btn-ver" 
                    onclick="event.stopPropagation(); confirmarAccion(
                      'Confirmar cambio de estado',
                      '¿Seguro que deseas cambiar el estado de este elemento?',
                      () => toggleEstado(${el.id_elemento})
                    )"
                    title="Reactivar elemento">
              <i class="material-icons icon-ok">check_circle</i>
            </button>`;
        }

        html += `
          <div class="elemento-item" onclick="editarElemento(${el.id_elemento})" style="animation-delay: ${index * 0.05}s">
            
            <!-- COLUMNA 1: Nombre -->
            <div data-label="Elemento">
              <div class="elemento-nombre">${el.nombre}</div>
              <span class="elemento-meta">${el.observaciones_generales || 'Sin observaciones'}</span>
            </div>

            <!-- COLUMNA 2: Código/Serial -->
            <div data-label="Código / Serial">
              <div class="elemento-codigo">${el.codigo}</div>
              ${el.serial ? `<span class="elemento-meta" style="margin-top: 6px; display: block;">SN: ${el.serial}</span>` : ''}
            </div>

            <!-- COLUMNA 3: Categoría -->
            <div class="elemento-categoria" data-label="Categoría">
              <span class="categoria-nombre">${el.categoria_nombre}</span>
              <span class="categoria-codigo">${el.categoria_codigo}</span>
            </div>

            <!-- COLUMNA 4: Estado -->
            <div data-label="Estado">
              <span class="elemento-badge ${badgeClass}">
                ${estado}
              </span>
            </div>

            <!-- COLUMNA 5: Acciones -->
            <div class="elemento-acciones" data-label="Acciones" onclick="event.stopPropagation()">
              ${btnMantenimiento}
              ${btnToggle}
              
              <a href="/SII-IETSN/qr-elementos.php?id=${el.id_elemento}"
                 class="btn-accion ${qrHabilitado ? 'btn-ver' : 'disabled'}"
                 style="text-decoration: none;"
                 ${!qrHabilitado ? 'onclick="return false;"' : ''}
                 title="Ver código QR">
                <i class="material-icons">qr_code_2</i>
              </a>
            </div>

          </div>
        `;
      });

      cont.innerHTML = html;
    })
    .catch(err => {
      console.error(err);
      M.toast({ html: "Error de conexión", classes: "red" });
    });
}
      /* ===============================
         TOGGLE ESTADO
      =============================== */
      function toggleEstado(id) {
        fetch("/SII-IETSN/api/elementos/toggle_estado.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "same-origin",
          body: JSON.stringify({ id_elemento: id })
        })
          .then(res => res.json())
          .then(res => {
            if (res.success) {
              M.toast({ html: "Estado actualizado", classes: "blue" });
              cargarElementos();
            } else {
              M.toast({ html: res.message || "No se pudo cambiar el estado", classes: "red" });
            }
          });
      }

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

      /* ===============================
         ABRIR MODAL - MODO CREAR
      =============================== */
      function abrirModalCrearElemento() {
        const modalEl = document.getElementById("modalElemento");
        if (!modalEl) {
          console.error("modalElemento no existe");
          return;
        }

        // 🔹 Habilitar código (por si venía deshabilitado en edición)
        const inputCodigo = document.getElementById("codigo");
        inputCodigo.removeAttribute("disabled");

        // 🔹 Limpiar formulario
        const form = document.getElementById("formElemento");
        form.reset();
        document.getElementById("id_elemento").value = "";
        // 🔹 Serial editable en creación
        const inputSerial = document.getElementById("serial");
        inputSerial.removeAttribute("disabled");
        inputSerial.value = "";

        // 🔹 Textos de la modal
        document.getElementById("tituloModalElemento").innerText = "Nuevo Elemento";
        document.getElementById("subtituloModalElemento").innerText =
          "Completa la información del elemento";

        // 🔹 Botón en modo CREAR
        const btn = document.getElementById("btnGuardarElemento");
        btn.innerHTML = `
        <i class="material-icons left">save</i>
        Guardar Elemento
    `;

        // 🔹 Cargar categorías (select)
        cargarCategoriasModal();

        // 🔹 Inicializar / obtener modal
        let instancia = M.Modal.getInstance(modalEl);
        if (!instancia) {
          instancia = M.Modal.init(modalEl);
        }

        instancia.open();

        // ⏳ Esperar a que Materialize renderice selects
        setTimeout(() => {
          M.updateTextFields();
          M.FormSelect.init(modalEl.querySelectorAll("select"));

          // 🔥 Obtener categoría seleccionada
          const selectCategoria = document.getElementById("id_categoria");
          if (selectCategoria && selectCategoria.value) {
            sugerirCodigoPorCategoria(selectCategoria.value);
          }

        }, 100);
      }
      document.getElementById("id_categoria").addEventListener("change", function () {
        document.getElementById("codigo").value = "";
        sugerirCodigoPorCategoria(this.value);
      });



      /* ===============================
            SUBMIT FORM - CREAR ELEMENTO
      ================================ */
      document.getElementById("formElemento").addEventListener("submit", function (e) {
        e.preventDefault();

        const id_elemento = document.getElementById("id_elemento").value || null;
        const codigo = document.getElementById("codigo").value.trim();
        const nombre = document.getElementById("nombre").value.trim();
        const id_categoria = document.getElementById("id_categoria").value;
        const observaciones = document.getElementById("observaciones_generales").value.trim();
        const serial = document.getElementById("serial").value.trim() || null;

        if (!codigo || !nombre || !id_categoria) {
          M.toast({ html: "Completa los campos obligatorios", classes: "red" });
          return;
        }

        const url = id_elemento
          ? "/SII-IETSN/api/elementos/actualizar.php"
          : "/SII-IETSN/api/elementos/crear.php";

        const payload = {
          id_elemento,
          codigo,
          serial,
          nombre,
          id_categoria,
          observaciones_generales: observaciones
        };

        const btn = this.querySelector("button[type='submit']");
        const txt = btn.innerHTML;
        btn.classList.add("disabled");
        btn.innerHTML = "Guardando...";

        fetch(url, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "same-origin",
          body: JSON.stringify(payload)
        })
          .then(r => r.json())
          .then(r => {
            if (!r.success) {
              M.toast({ html: r.message || "Error", classes: "red" });
              return;
            }

            M.toast({
              html: id_elemento
                ? "Elemento actualizado correctamente"
                : "Elemento creado correctamente",
              classes: "green"
            });

            M.Modal.getInstance(document.getElementById("modalElemento")).close();
            this.reset();
            cargarElementos();
          })
          .finally(() => {
            btn.classList.remove("disabled");
            btn.innerHTML = txt;
          });
      });

      /* ===============================
         ABRIR MODAL - MODO EDITAR  
      =============================== */
      function editarElemento(id) {


        const modalEl = document.getElementById("modalElemento");
        if (!modalEl) return;

        const el = elementosCache[id];
        if (!el) return;
        // ===== ESTADO ACTUAL (BADGE) =====
        const badge = document.getElementById("badgeEstadoElemento");

        // Limpiar clases previas
        badge.className = "badge-estado-modal";

        if (el.estado === "Disponible") {
          badge.innerText = "Disponible";
          badge.classList.add("badge-estado-disponible");
        } else if (el.estado === "Prestado") {
          badge.innerText = "Prestado";
          badge.classList.add("badge-estado-prestado");
        } else if (el.estado === "Mantenimiento") {
          badge.innerText = "Mantenimiento";
          badge.classList.add("badge-estado-mantenimiento");
        } else {
          badge.innerText = "Fuera de servicio";
          badge.classList.add("badge-estado-fuera");
        }

        document.getElementById("tituloModalElemento").innerText = "Editar Elemento";
        document.getElementById("subtituloModalElemento").innerText =
          "Actualiza la información del elemento";
        //document.getElementById("serial").value = el.serial || "";
        //document.getElementById("serial").setAttribute("disabled", true);
        // 🔒 Bloquear código en edición
        document.getElementById("codigo").setAttribute("disabled", true);

        // 🔒 Bloquear serial en edición
        //  document.getElementById("serial").setAttribute("disabled", true);

        document.getElementById("id_elemento").value = el.id_elemento;
        document.getElementById("codigo").value = el.codigo;
        document.getElementById("nombre").value = el.nombre;
        cargarCategoriasModal(el.id_categoria);

        document.getElementById("observaciones_generales").value =
          el.observaciones_generales || "";
        // Botón modo EDITAR
        const btn = document.getElementById("btnGuardarElemento");
        btn.innerHTML = `
  <i class="material-icons left">edit</i>
  Actualizar Elemento
`;

        // 🔒 Bloquear código en edición
        document.getElementById("codigo").setAttribute("disabled", true);

        M.updateTextFields();

        let modal = M.Modal.getInstance(modalEl);
        if (!modal) modal = M.Modal.init(modalEl);
        modal.open();
      }

      function cargarCategorias() {
        fetch("/SII-IETSN/api/categorias/listar.php", {
          credentials: "same-origin"
        })
          .then(r => r.json())
          .then(r => {
            if (!r.success) return;

            const select = document.getElementById("filtroCategoria");
            select.innerHTML = `<option value="">Todas las categorías</option>`;

            r.data.forEach(c => {
              select.innerHTML += `
          <option value="${c.id_categoria}">
            ${c.codigo} - ${c.nombre}
          </option>`;
            });

            M.FormSelect.init(select);
          });
      }
      function cargarCategoriasModal(idSeleccionado = null) {
        fetch("/SII-IETSN/api/categorias/listar.php", {
          credentials: "same-origin"
        })
          .then(r => r.json())
          .then(r => {
            if (!r.success) return;

            const select = document.getElementById("id_categoria");
            select.innerHTML = `<option value="" disabled>Seleccione una categoría</option>`;

            r.data.forEach(c => {
              select.innerHTML += `
          <option value="${c.id_categoria}">
            ${c.codigo} - ${c.nombre}
          </option>`;
            });

            if (idSeleccionado) {
              select.value = idSeleccionado;
            }

            M.FormSelect.init(select);
          });
      }
      let accionConfirmada = null;

      function confirmarAccion(titulo, mensaje, callback) {
        document.getElementById("confirmTitulo").innerText = titulo;
        document.getElementById("confirmTexto").innerText = mensaje;

        accionConfirmada = callback;

        const btn = document.getElementById("btnConfirmarAccion");
        btn.onclick = () => {
          if (accionConfirmada) accionConfirmada();
          accionConfirmada = null;
          M.Modal.getInstance(document.getElementById("modalConfirmacion")).close();
        };

        let modal = M.Modal.getInstance(document.getElementById("modalConfirmacion"));
        if (!modal) modal = M.Modal.init(document.getElementById("modalConfirmacion"));
        modal.open();
      }
      function ponerMantenimiento(id) {
        fetch("/SII-IETSN/api/elementos/mantenimiento.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "same-origin",
          body: JSON.stringify({ id_elemento: id })
        })
          .then(r => r.json())
          .then(r => {
            if (r.success) {
              M.toast({ html: "Elemento enviado a mantenimiento", classes: "orange" });
              cargarElementos();
            } else {
              M.toast({ html: r.message || "No se pudo actualizar", classes: "red" });
            }
          });
      }
      document.getElementById("id_categoria")?.addEventListener("change", async function () {
        const idCategoria = this.value;
        if (!idCategoria) return;

        try {
          const resp = await fetch(
            `/SII-IETSN/api/elementos/sugerir_codigo.php?id_categoria=${idCategoria}`,
            { credentials: "same-origin" }
          );

          const res = await resp.json();
          if (!res.success) return;

          const inputCodigo = document.getElementById("codigo");

          // Solo autocompletar si está vacío o en modo crear
          if (!inputCodigo.value || inputCodigo.dataset.autogen === "true") {
            inputCodigo.value = res.codigo;
            inputCodigo.dataset.autogen = "true";
            M.updateTextFields();
          }

        } catch (e) {
          console.error("Error generando código:", e);
        }
      });
      async function sugerirCodigoPorCategoria(idCategoria) {
        if (!idCategoria) return;

        try {
          const resp = await fetch(
            `/SII-IETSN/api/elementos/sugerir_codigo.php?id_categoria=${idCategoria}`,
            { credentials: 'same-origin' }
          );

          const res = await resp.json();
          if (!res.success) return;

          const inputCodigo = document.getElementById("codigo");
          if (inputCodigo && !inputCodigo.value) {
            inputCodigo.value = res.codigo;
            M.updateTextFields();
          }

        } catch (e) {
          console.error("Error sugiriendo código", e);
        }
      }

    </script>
</body>

</html>