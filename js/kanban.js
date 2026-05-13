/**
 * kanban.js — Lógica del tablero Kanban educativo
 *
 * Módulos:
 *  1. Estado global (AppState)
 *  2. Utilidades
 *  3. Renderizado del board
 *  4. Drag & Drop (optimizado con Delegación de eventos)
 *  5. Filtros (equipo / estudiante)
 *  6. Modal de nueva tarea
 *  7. Toast / notificaciones
 *  8. Inicialización
 */

"use strict";

// ══════════════════════════════════════════════════════════════
// 1. ESTADO GLOBAL
// ══════════════════════════════════════════════════════════════
const AppState = {
  equipos: [],
  usuarios: [], // del equipo seleccionado
  allUsuarios: [], // todos
  columnas: [],
  tareas: [], // del equipo activo
  proyecto: null,

  filtros: {
    id_equipo: null,
    id_usuario: null, // null = todos
  },

  drag: {
    taskId: null,
    srcColId: null,
    el: null,
  },
};

// ══════════════════════════════════════════════════════════════
// 2. UTILIDADES
// ══════════════════════════════════════════════════════════════

/** Formatea fecha "YYYY-MM-DD" → "dd MMM" en español */
function formatFecha(dateStr) {
  if (!dateStr) return "—";
  const [y, m, d] = dateStr.split("-").map(Number);
  const meses = [
    "ene",
    "feb",
    "mar",
    "abr",
    "may",
    "jun",
    "jul",
    "ago",
    "sep",
    "oct",
    "nov",
    "dic",
  ];
  return `${d} ${meses[m - 1]}`;
}

/** True si la tarea está vencida y no está terminada */
function isAtrasada(tarea) {
  if (tarea.id_columna === 4) return false;
  if (!tarea.fecha_limite) return false;
  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);
  const limite = new Date(tarea.fecha_limite + "T00:00:00");
  return limite < hoy;
}

/** Iniciales de nombre completo → "AT" */
function iniciales(nombre, apellido) {
  return ((nombre?.[0] || "") + (apellido?.[0] || "")).toUpperCase();
}

/** Retorna el usuario por id */
function findUsuario(id) {
  return AppState.allUsuarios.find((u) => u.id_usuario === id) || null;
}

/** Mapea id_columna → nombre de estado */
function colToEstado(id_columna) {
  const map = {
    1: "pendiente",
    2: "en proceso",
    3: "revision",
    4: "terminado",
  };
  return map[id_columna] || "pendiente";
}

// ══════════════════════════════════════════════════════════════
// 3. RENDERIZADO
// ══════════════════════════════════════════════════════════════

/** Construye el HTML de una tarjeta */
function buildCardHTML(tarea) {
  const atrasada = isAtrasada(tarea);
  const usuario = tarea.id_usuario ? findUsuario(tarea.id_usuario) : null;
  const terminada = tarea.id_columna === 4;

  // Badges dinámicos
  let badges = "";
  if (tarea.bloqueado) {
    badges += `<span class="badge badge-bloqueado">🔒 Bloqueado</span>`;
  }
  if (atrasada) {
    badges += `<span class="badge badge-atrasado">⏰ Atrasado</span>`;
  }
  badges += `<span class="badge badge-prio ${tarea.prioridad}">${tarea.prioridad}</span>`;

  // Responsable
  let responsableHTML = "";
  if (usuario) {
    responsableHTML = `
      <span class="task-responsable">
        <span class="mini-avatar" title="${usuario.nombre} ${usuario.apellido}">
          ${iniciales(usuario.nombre, usuario.apellido)}
        </span>
        <span class="truncate">${usuario.nombre}</span>
      </span>`;
  } else {
    responsableHTML = `<span class="task-unassigned">Sin asignar</span>`;
  }

  // Fecha
  const fechaHTML = tarea.fecha_limite
    ? `<span class="task-fecha ${atrasada ? "text-danger" : ""}">
         <span class="icon">📅</span>${formatFecha(tarea.fecha_limite)}
       </span>`
    : "";

  return `
    <div class="task-card ${atrasada ? "atrasada" : ""} ${terminada ? "terminada" : ""}"
         data-id="${tarea.id_tarea}"
         data-col="${tarea.id_columna}"
         data-prio="${tarea.prioridad}"
         draggable="true">

      <button class="btn-delete-card" data-id="${tarea.id_tarea}" title="Eliminar tarea">✕</button>

      <div class="task-badges">${badges}</div>

      <p class="task-title">${escapeHTML(tarea.titulo)}</p>

      <div class="task-meta">
        ${responsableHTML}
        ${fechaHTML}
      </div>
    </div>`;
}

/** Escapa HTML para prevenir XSS */
function escapeHTML(str) {
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

/** Renderiza todas las columnas con sus tarjetas */
function renderBoard() {
  const board = document.getElementById("board");
  const { tareas, columnas, filtros } = AppState;

  // Filtro por usuario
  const tareasFiltradas = filtros.id_usuario
    ? tareas.filter((t) => t.id_usuario === filtros.id_usuario)
    : tareas;

  // Actualiza contadores del topbar
  const atrasadas = tareas.filter((t) => isAtrasada(t)).length;
  const bloqueadas = tareas.filter((t) => t.bloqueado).length;
  document.getElementById("stat-atrasadas").innerHTML =
    `Atrasadas: <span>${atrasadas}</span>`;
  document.getElementById("stat-bloqueadas").innerHTML =
    `Bloqueadas: <span>${bloqueadas}</span>`;

  board.innerHTML = columnas
    .map((col) => {
      const tareasCol = tareasFiltradas.filter(
        (t) => t.id_columna === col.id_columna,
      );

      const cardsHTML = tareasCol.length
        ? tareasCol.map(buildCardHTML).join("")
        : `<div class="empty-col">
           <span class="empty-icon">✦</span>
           <span>Sin tareas</span>
         </div>`;

      return `
      <div class="kanban-col" data-col="${col.id_columna}">
        <div class="col-header">
          <span class="col-dot"></span>
          <span class="col-title">${col.nombre}</span>
          <span class="col-count">${tareasCol.length}</span>
        </div>
        <div class="col-cards" data-col="${col.id_columna}">
          ${cardsHTML}
        </div>
      </div>`;
    })
    .join("");

  // Re-enlazar eventos de drag después de re-render
  // (se usa delegación en el board, no necesita re-bind)
}

/** Renderiza la barra de proyecto (nombre + miembros) */
function renderProjectBar() {
  const { proyecto, usuarios } = AppState;
  const nameEl = document.getElementById("project-name");
  const equipoEl = document.getElementById("project-equipo-label");
  const miembrosEl = document.getElementById("project-miembros");

  if (!proyecto) {
    nameEl.textContent = "Sin proyecto";
    miembrosEl.innerHTML = "";
    return;
  }

  nameEl.textContent = proyecto.nombre;
  const equipo = AppState.equipos.find(
    (e) => e.id_equipo === proyecto.id_equipo,
  );
  equipoEl.textContent = equipo?.nombre || "";

  miembrosEl.innerHTML = usuarios
    .map(
      (u) => `
    <div class="avatar" data-name="${u.nombre} ${u.apellido}" title="${u.nombre} ${u.apellido}">
      ${iniciales(u.nombre, u.apellido)}
    </div>`,
    )
    .join("");
}

/** Rellena el select de equipos en el topbar */
function renderEquipoSelect() {
  const sel = document.getElementById("select-equipo");
  sel.innerHTML = AppState.equipos
    .map((e) => `<option value="${e.id_equipo}">${e.nombre}</option>`)
    .join("");
}

/** Rellena el select de filtro por estudiante */
function renderUsuarioFilter() {
  const sel = document.getElementById("select-usuario");
  sel.innerHTML =
    `<option value="">Todos los estudiantes</option>` +
    AppState.usuarios
      .map(
        (u) =>
          `<option value="${u.id_usuario}">${u.nombre} ${u.apellido}</option>`,
      )
      .join("");
}

/** Rellena select de responsable dentro del modal */
function renderResponsableSelect() {
  const sel = document.getElementById("form-responsable");
  sel.innerHTML =
    `<option value="">Sin asignar</option>` +
    AppState.usuarios
      .map(
        (u) =>
          `<option value="${u.id_usuario}">${u.nombre} ${u.apellido}</option>`,
      )
      .join("");
}

// ══════════════════════════════════════════════════════════════
// 4. DRAG & DROP — Delegación de eventos (rendimiento óptimo)
// ══════════════════════════════════════════════════════════════

function initDragAndDrop() {
  const board = document.getElementById("board");

  /* ── dragstart: disparado en la tarjeta ── */
  board.addEventListener("dragstart", (e) => {
    const card = e.target.closest(".task-card");
    if (!card) return;

    AppState.drag.taskId = Number(card.dataset.id);
    AppState.drag.srcColId = Number(card.dataset.col);
    AppState.drag.el = card;

    e.dataTransfer.effectAllowed = "move";
    // Necesario para Firefox
    e.dataTransfer.setData("text/plain", card.dataset.id);

    // Diferir el estilo "ghost" para que el navegador capture el snapshot primero
    requestAnimationFrame(() => card.classList.add("dragging"));
  });

  /* ── dragend ── */
  board.addEventListener("dragend", (e) => {
    const card = e.target.closest(".task-card");
    if (card) card.classList.remove("dragging");
    // Limpiar highlights
    board
      .querySelectorAll(".col-cards.drag-over")
      .forEach((z) => z.classList.remove("drag-over"));
  });

  /* ── dragover: en la zona de drop ── */
  board.addEventListener("dragover", (e) => {
    const zone = e.target.closest(".col-cards");
    if (!zone) return;
    e.preventDefault(); // permite el drop
    e.dataTransfer.dropEffect = "move";

    // Highlight solo si es distinto
    board.querySelectorAll(".col-cards.drag-over").forEach((z) => {
      if (z !== zone) z.classList.remove("drag-over");
    });
    zone.classList.add("drag-over");
  });

  /* ── dragleave ── */
  board.addEventListener("dragleave", (e) => {
    const zone = e.target.closest(".col-cards");
    if (!zone) return;
    // Solo quitar si realmente salimos de la zona (no de un hijo)
    if (!zone.contains(e.relatedTarget)) {
      zone.classList.remove("drag-over");
    }
  });

  /* ── drop ── */
  board.addEventListener("drop", async (e) => {
    e.preventDefault();
    const zone = e.target.closest(".col-cards");
    if (!zone) return;
    zone.classList.remove("drag-over");

    const destColId = Number(zone.dataset.col);
    const { taskId, srcColId } = AppState.drag;

    if (!taskId || destColId === srcColId) return;

    // Actualizar estado local primero (optimistic update)
    const tarea = AppState.tareas.find((t) => t.id_tarea === taskId);
    if (!tarea) return;

    const colAnterior = tarea.id_columna;
    tarea.id_columna = destColId;
    tarea.estado = colToEstado(destColId);

    renderBoard();

    // Persistir en el servidor
    try {
      await API.updateTareaColumna(taskId, destColId, tarea.estado);
      showToast(
        `Tarea movida a "${AppState.columnas.find((c) => c.id_columna === destColId)?.nombre}"`,
        "success",
      );
    } catch (err) {
      // Rollback si falla
      tarea.id_columna = colAnterior;
      tarea.estado = colToEstado(colAnterior);
      renderBoard();
      showToast("Error al mover la tarea", "error");
    }
  });
}

// ══════════════════════════════════════════════════════════════
// 5. FILTROS
// ══════════════════════════════════════════════════════════════

async function cambiarEquipo(id_equipo) {
  id_equipo = Number(id_equipo);
  AppState.filtros.id_equipo = id_equipo;
  AppState.filtros.id_usuario = null;

  // Mostrar skeleton mientras carga
  showSkeleton();

  const [proyecto, usuarios, tareas] = await Promise.all([
    API.getProyectoByEquipo(id_equipo),
    API.getUsuariosByEquipo(id_equipo),
    API.getTareasByEquipo(id_equipo),
  ]);

  AppState.proyecto = proyecto;
  AppState.usuarios = usuarios;
  AppState.tareas = tareas;

  renderProjectBar();
  renderUsuarioFilter();
  renderResponsableSelect();
  renderBoard();

  // Sync filtro de usuario
  document.getElementById("select-usuario").value = "";
}

function filtrarUsuario(id_usuario) {
  AppState.filtros.id_usuario = id_usuario ? Number(id_usuario) : null;
  renderBoard();
}

// ══════════════════════════════════════════════════════════════
// 6. MODAL — Nueva tarea
// ══════════════════════════════════════════════════════════════

function openModal() {
  document.getElementById("modal-overlay").classList.add("active");
  document.getElementById("form-titulo").focus();
}

function closeModal() {
  document.getElementById("modal-overlay").classList.remove("active");
  document.getElementById("form-nueva-tarea").reset();
}

async function submitNuevaTarea(e) {
  e.preventDefault();
  const form = e.target;

  const titulo = form["titulo"].value.trim();
  const descripcion = form["descripcion"].value.trim();
  const id_usuario = form["responsable"].value
    ? Number(form["responsable"].value)
    : null;
  const prioridad = form["prioridad"].value;
  const fecha_limite = form["fecha_limite"].value || null;
  const id_columna = Number(form["columna"].value);

  if (!titulo) {
    showToast("El título es obligatorio", "error");
    return;
  }

  const proyecto = AppState.proyecto;
  if (!proyecto) {
    showToast("Selecciona un equipo primero", "error");
    return;
  }

  const btnSubmit = form.querySelector(".btn-submit");
  btnSubmit.disabled = true;
  btnSubmit.textContent = "Guardando…";

  try {
    const nueva = await API.createTarea({
      titulo,
      descripcion,
      id_usuario,
      prioridad,
      fecha_limite,
      id_columna,
      estado: colToEstado(id_columna),
      id_proyecto: proyecto.id_proyecto,
      id_equipo: AppState.filtros.id_equipo,
    });

    AppState.tareas.push(nueva);
    renderBoard();
    closeModal();
    showToast("Tarea creada ✓", "success");
  } catch {
    showToast("Error al crear la tarea", "error");
  } finally {
    btnSubmit.disabled = false;
    btnSubmit.textContent = "Crear Tarea";
  }
}

// ══════════════════════════════════════════════════════════════
// 7. ELIMINAR TAREA
// ══════════════════════════════════════════════════════════════

function initDeleteDelegation() {
  document.getElementById("board").addEventListener("click", async (e) => {
    const btn = e.target.closest(".btn-delete-card");
    if (!btn) return;

    const id = Number(btn.dataset.id);
    if (!confirm("¿Eliminar esta tarea?")) return;

    try {
      await API.deleteTarea(id);
      AppState.tareas = AppState.tareas.filter((t) => t.id_tarea !== id);
      renderBoard();
      showToast("Tarea eliminada", "success");
    } catch {
      showToast("Error al eliminar", "error");
    }
  });
}

// ══════════════════════════════════════════════════════════════
// 8. TOAST
// ══════════════════════════════════════════════════════════════

function showToast(msg, type = "success") {
  const container = document.getElementById("toast-container");
  const el = document.createElement("div");
  el.className = `toast ${type}`;
  const icon = type === "success" ? "✓" : "✕";
  el.innerHTML = `<span>${icon}</span> ${escapeHTML(msg)}`;
  container.appendChild(el);
  setTimeout(() => el.remove(), 3200);
}

// ══════════════════════════════════════════════════════════════
// SKELETON (loading)
// ══════════════════════════════════════════════════════════════

function showSkeleton() {
  const board = document.getElementById("board");
  board.innerHTML = AppState.columnas
    .map(
      (col) => `
    <div class="kanban-col" data-col="${col.id_columna}">
      <div class="col-header">
        <span class="col-dot"></span>
        <span class="col-title">${col.nombre}</span>
        <span class="col-count">—</span>
      </div>
      <div class="col-cards" data-col="${col.id_columna}">
        <div class="skeleton sk-card"></div>
        <div class="skeleton sk-card"></div>
      </div>
    </div>`,
    )
    .join("");
}

// ══════════════════════════════════════════════════════════════
// 9. INICIALIZACIÓN
// ══════════════════════════════════════════════════════════════

async function init() {
  // Cargar datos base
  const [equipos, allUsuarios, columnas] = await Promise.all([
    API.getEquipos(),
    API.getAllUsuarios(),
    Promise.resolve(API.getColumnas()),
  ]);

  AppState.equipos = equipos;
  AppState.allUsuarios = allUsuarios;
  AppState.columnas = columnas;

  // Render selectores de equipo
  renderEquipoSelect();

  // Seleccionar el primer equipo por defecto
  if (equipos.length) await cambiarEquipo(equipos[0].id_equipo);

  // ── Eventos ──────────────────────────────────────────────

  // Cambio de equipo
  document.getElementById("select-equipo").addEventListener("change", (e) => {
    cambiarEquipo(e.target.value);
  });

  // Filtro de estudiante
  document.getElementById("select-usuario").addEventListener("change", (e) => {
    filtrarUsuario(e.target.value);
  });

  // Modal nueva tarea
  document
    .getElementById("btn-nueva-tarea")
    .addEventListener("click", openModal);
  document.getElementById("modal-close").addEventListener("click", closeModal);
  document
    .getElementById("btn-cancel-modal")
    .addEventListener("click", closeModal);
  document.getElementById("modal-overlay").addEventListener("click", (e) => {
    if (e.target === e.currentTarget) closeModal();
  });

  // Submit del formulario
  document
    .getElementById("form-nueva-tarea")
    .addEventListener("submit", submitNuevaTarea);

  // Inicializar Drag & Drop (delegación en el board)
  initDragAndDrop();

  // Eliminar con delegación
  initDeleteDelegation();

  // Cerrar modal con Escape
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeModal();
  });
}

// Arrancar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", init);
