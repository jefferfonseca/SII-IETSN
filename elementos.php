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
  <link rel="stylesheet" href="/SII-IETSN/css/usuario.css">
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

        <div class="input-field" style="flex:2">
          <input id="buscarElemento" type="text">
          <label for="buscarElemento">Buscar</label>
        </div>

        <div class="input-field" style="flex:1">
          <select id="filtroCategoria">
            <option value="">Todas las categorías</option>
          </select>
          <label>Categoría</label>
        </div>

        <div class="input-field" style="flex:1">
          <select id="filtroEstado">
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
    <div class="usuarios-container" id="elementosContainer"></div>

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
          <a href="#!" class="modal-close btn btn-cancelar">Cancelar</a>

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
            cont.innerHTML = "";
            elementosCache = {};

            if (res.data.length === 0) {
              cont.innerHTML = `<p style="opacity:.6">No hay elementos para mostrar</p>`;
              return;
            }




            res.data.forEach(el => {
              elementosCache[el.id_elemento] = el;

              const estado = el.estado; // estado real desde DB
              const puedeCambiar = estado !== "Prestado";
              /* ===============================
                 ACCIÓN (Activar / Desactivar)
              =============================== */
              let accionTexto = "";
              let accionIcono = "";

              if (estado === "Disponible") {
                accionTexto = "Desactivar";
                accionIcono = "sync";
              } else if (estado === "Fuera de servicio" || estado === "Mantenimiento") {
                accionTexto = "Activar";
                accionIcono = "lock_open";
              }
              /* ===============================
                 BADGE SUPERIOR (Disponible / No disponible)
              =============================== */
              let disponibilidadTexto = "";
              let disponibilidadClase = "";

              if (estado === "Disponible") {
                disponibilidadTexto = "Disponible";
                disponibilidadClase = "badge-activo";
              } else {
                disponibilidadTexto = "No disponible";
                disponibilidadClase = "badge-inactivo";
              }

              /* ===============================
                 BADGE INFERIOR (Condición)
              =============================== */
              let condicionTexto = "";
              let condicionClase = "";

              if (estado === "Disponible") {
                condicionTexto = "Almacenado";
                condicionClase = "badge-almacenado";
              } else if (estado === "Prestado") {
                condicionTexto = "Prestado";
                condicionClase = "badge-prestado";
              } else if (estado === "Mantenimiento") {
                condicionTexto = "Mantenimiento";
                condicionClase = "badge-mantenimiento";
              } else {
                condicionTexto = "Fuera de servicio";
                condicionClase = "badge-inactivo";
              }


              const qrHabilitado = disponibilidadTexto === "Disponible";

              let btnMantenimiento = "";
              let btnToggle = "";

              // ===== BOTÓN MANTENIMIENTO =====
              if (estado === "Disponible") {
                btnMantenimiento = `
    <a href="#"
      class="btn btn-accion orange"
      onclick="event.stopPropagation(); confirmarAccion(
  'Enviar a mantenimiento',
  'El elemento quedará no disponible hasta que se reactive. ¿Continuar?',
  () => ponerMantenimiento(${el.id_elemento})
)">
      <i class="material-icons">build</i>
      Mantenim.
    </a>`;
              }

              // ===== BOTÓN ACTIVAR / DESACTIVAR =====
              if (estado === "Disponible") {
                btnToggle = `
    <a href="#"
      class="btn btn-accion red"
      onclick="event.stopPropagation(); confirmarAccion(
  'Confirmar cambio de estado',
  '¿Seguro que deseas cambiar el estado de este elemento?',
  () => toggleEstado(${el.id_elemento})
)">
      <i class="material-icons">sync</i>
      Desactivar
    </a>`;
              }

              if (estado === "Mantenimiento" || estado === "Fuera de servicio") {
                btnToggle = `
    <a href="#"
      class="btn btn-accion green"
      onclick="event.stopPropagation(); confirmarAccion(
  'Confirmar cambio de estado',
  '¿Seguro que deseas cambiar el estado de este elemento?',
  () => toggleEstado(${el.id_elemento})
)">
      <i class="material-icons">lock_open</i>
      Activar
    </a>`;
              }



              cont.innerHTML += `
              <div class="usuario-card clickable-card"
              onclick="editarElemento(${el.id_elemento})">

            <div class="usuario-header">
              <div class="usuario-info">
                <h3 class="usuario-nombre">${el.nombre}</h3>

                <div class="usuario-documento">
                  <i class="material-icons">qr_code</i>
                  ${el.codigo}
                </div>

                <div class="usuario-documento">
                  <i class="material-icons">category</i>
                  ${el.categoria_codigo} - ${el.categoria_nombre}
                </div>


              </div>

              <div class="usuario-badges">
                <span class="badge-rol ${disponibilidadClase}">
                  ${disponibilidadTexto}
                </span>

                <span class="badge-estado ${condicionClase}">
                  ${condicionTexto}
                </span>

              </div>
            </div>

            <div class="usuario-acciones">
              ${btnMantenimiento}
              ${btnToggle}

             <a href="/SII-IETSN/qr-elementos.php?id=${el.id_elemento}"
              class="btn btn-accion teal ${qrHabilitado ? '' : 'disabled'}"
              ${qrHabilitado ? 'target=""' : 'onclick="event.stopPropagation(); return false;"'}>
              <i class="material-icons">qr_code_2</i>
              QR
            </a>


            </div>
          </div>`;
            });
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
         LOGOUT
      =============================== */
      function cerrarSesion() {
        fetch('/SII-IETSN/api/auth/logout.php', {
          method: 'POST',
          credentials: 'same-origin'
        })
          .then(() => location.href = "/SII-IETSN/index.html");
      }

      /* ===============================
         ABRIR MODAL - MODO CREAR
      =============================== */
      function abrirModalCrearElemento() {
        document.getElementById("codigo").removeAttribute("disabled");
        const modalEl = document.getElementById("modalElemento");

        if (!modalEl) {
          console.error("modalElemento no existe");
          return;
        }

        // Limpiar formulario
        document.getElementById("formElemento").reset();
        document.getElementById("id_elemento").value = "";

        // Textos
        document.getElementById("tituloModalElemento").innerText = "Nuevo Elemento";
        document.getElementById("subtituloModalElemento").innerText =
          "Completa la información del elemento";

        cargarCategoriasModal();

        // Botón modo CREAR
        const btn = document.getElementById("btnGuardarElemento");
        btn.innerHTML = `
  <i class="material-icons left">save</i>
  Guardar Elemento
`;

        M.updateTextFields();
        M.FormSelect.init(modalEl.querySelectorAll("select"));

        // Obtener o crear instancia
        let instancia = M.Modal.getInstance(modalEl);
        if (!instancia) {
          instancia = M.Modal.init(modalEl);
        }

        instancia.open();
      }


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