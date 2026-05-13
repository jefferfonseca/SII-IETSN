/**
 * data.js — Datos mock para el tablero Kanban educativo
 * Reemplazar las funciones fetchXxx() con llamadas reales a la API REST PHP
 *
 * Convención de endpoints sugeridos:
 *   GET  /api/equipos
 *   GET  /api/equipos/:id/miembros
 *   GET  /api/proyectos?id_equipo=:id
 *   GET  /api/tareas?id_proyecto=:id
 *   POST /api/tareas
 *   PUT  /api/tareas/:id          (actualizar estado, responsable, etc.)
 *   DELETE /api/tareas/:id
 */

// ─── EQUIPOS ──────────────────────────────────────────────────────────────────
const MOCK_EQUIPOS = [
  { id_equipo: 1, nombre: "Equipo Alpha", id_grado: 11 },
  { id_equipo: 2, nombre: "Equipo Beta", id_grado: 11 },
  { id_equipo: 3, nombre: "Equipo Gamma", id_grado: 11 },
];

// ─── USUARIOS (estudiantes) ───────────────────────────────────────────────────
const MOCK_USUARIOS = [
  { id_usuario: 1, nombre: "Ana", apellido: "Torres", id_equipo: 1 },
  { id_usuario: 2, nombre: "Luis", apellido: "Ramírez", id_equipo: 1 },
  { id_usuario: 3, nombre: "Sofía", apellido: "Mendoza", id_equipo: 1 },
  { id_usuario: 4, nombre: "Carlos", apellido: "Jiménez", id_equipo: 2 },
  { id_usuario: 5, nombre: "Valeria", apellido: "Ospina", id_equipo: 2 },
  { id_usuario: 6, nombre: "Diego", apellido: "Vargas", id_equipo: 3 },
  { id_usuario: 7, nombre: "Mariana", apellido: "López", id_equipo: 3 },
  { id_usuario: 8, nombre: "Tomás", apellido: "Herrera", id_equipo: 3 },
];

// ─── PROYECTOS ────────────────────────────────────────────────────────────────
const MOCK_PROYECTOS = [
  { id_proyecto: 1, nombre: "App de Reciclaje", id_equipo: 1 },
  { id_proyecto: 2, nombre: "Sistema de Inventarios", id_equipo: 2 },
  { id_proyecto: 3, nombre: "Plataforma de Tutorías", id_equipo: 3 },
];

// ─── TAREAS ───────────────────────────────────────────────────────────────────
// estado / id_columna: 1=pendiente | 2=en proceso | 3=revision | 4=terminado
// prioridad: alta | media | baja
// badges extra: bloqueado:true | (atrasado se calcula automáticamente)
const MOCK_TAREAS = [
  // ── Equipo Alpha (Proyecto 1) ─────────────────────────────────────────────
  {
    id_tarea: 101,
    titulo: "Diseñar wireframes",
    descripcion:
      "Crear los wireframes en Figma para las pantallas principales.",
    id_proyecto: 1,
    id_equipo: 1,
    id_usuario: 1,
    id_columna: 4,
    estado: "terminado",
    prioridad: "alta",
    fecha_limite: "2025-03-10",
    bloqueado: false,
  },
  {
    id_tarea: 102,
    titulo: "Configurar base de datos",
    descripcion: "Crear las tablas en MySQL según el modelo relacional.",
    id_proyecto: 1,
    id_equipo: 1,
    id_usuario: 2,
    id_columna: 3,
    estado: "revision",
    prioridad: "alta",
    fecha_limite: "2025-04-05",
    bloqueado: false,
  },
  {
    id_tarea: 103,
    titulo: "Módulo de autenticación",
    descripcion: "Login y registro con validación de sesión PHP.",
    id_proyecto: 1,
    id_equipo: 1,
    id_usuario: 3,
    id_columna: 2,
    estado: "en proceso",
    prioridad: "alta",
    fecha_limite: "2025-03-28",
    bloqueado: false,
  },
  {
    id_tarea: 104,
    titulo: "Integrar API de mapas",
    descripcion: "Conectar Leaflet.js para mostrar puntos de reciclaje.",
    id_proyecto: 1,
    id_equipo: 1,
    id_usuario: null,
    id_columna: 1,
    estado: "pendiente",
    prioridad: "media",
    fecha_limite: "2025-04-20",
    bloqueado: true,
  },
  {
    id_tarea: 105,
    titulo: "Pruebas de usabilidad",
    descripcion: "Pruebas con 5 usuarios reales del colegio.",
    id_proyecto: 1,
    id_equipo: 1,
    id_usuario: null,
    id_columna: 1,
    estado: "pendiente",
    prioridad: "baja",
    fecha_limite: "2025-05-01",
    bloqueado: false,
  },

  // ── Equipo Beta (Proyecto 2) ──────────────────────────────────────────────
  {
    id_tarea: 201,
    titulo: "Análisis de requerimientos",
    descripcion: "Levantar requerimientos con el cliente interno.",
    id_proyecto: 2,
    id_equipo: 2,
    id_usuario: 4,
    id_columna: 4,
    estado: "terminado",
    prioridad: "alta",
    fecha_limite: "2025-02-28",
    bloqueado: false,
  },
  {
    id_tarea: 202,
    titulo: "CRUD de productos",
    descripcion: "Crear, editar, eliminar y listar productos del inventario.",
    id_proyecto: 2,
    id_equipo: 2,
    id_usuario: 5,
    id_columna: 2,
    estado: "en proceso",
    prioridad: "alta",
    fecha_limite: "2025-03-20",
    bloqueado: false,
  },
  {
    id_tarea: 203,
    titulo: "Reportes en PDF",
    descripcion: "Generar reportes exportables con TCPDF.",
    id_proyecto: 2,
    id_equipo: 2,
    id_usuario: null,
    id_columna: 1,
    estado: "pendiente",
    prioridad: "media",
    fecha_limite: "2025-04-15",
    bloqueado: true,
  },

  // ── Equipo Gamma (Proyecto 3) ─────────────────────────────────────────────
  {
    id_tarea: 301,
    titulo: "Definir modelo de datos",
    descripcion: "Diseñar el ERD para la plataforma de tutorías.",
    id_proyecto: 3,
    id_equipo: 3,
    id_usuario: 6,
    id_columna: 4,
    estado: "terminado",
    prioridad: "alta",
    fecha_limite: "2025-03-01",
    bloqueado: false,
  },
  {
    id_tarea: 302,
    titulo: "Sistema de agendamiento",
    descripcion: "Permitir a estudiantes reservar sesiones con tutores.",
    id_proyecto: 3,
    id_equipo: 3,
    id_usuario: 7,
    id_columna: 3,
    estado: "revision",
    prioridad: "alta",
    fecha_limite: "2025-04-02",
    bloqueado: false,
  },
  {
    id_tarea: 303,
    titulo: "Chat en tiempo real",
    descripcion: "Implementar WebSockets para mensajería tutor-estudiante.",
    id_proyecto: 3,
    id_equipo: 3,
    id_usuario: 8,
    id_columna: 2,
    estado: "en proceso",
    prioridad: "media",
    fecha_limite: "2025-04-10",
    bloqueado: false,
  },
  {
    id_tarea: 304,
    titulo: "Panel de métricas",
    descripcion: "Dashboard con Chart.js para visualizar asistencia.",
    id_proyecto: 3,
    id_equipo: 3,
    id_usuario: null,
    id_columna: 1,
    estado: "pendiente",
    prioridad: "baja",
    fecha_limite: "2025-05-10",
    bloqueado: false,
  },
];

// ─── COLUMNAS (espejo de la tabla SQL) ───────────────────────────────────────
const MOCK_COLUMNAS = [
  { id_columna: 1, nombre: "Pendiente", orden: 1 },
  { id_columna: 2, nombre: "En Proceso", orden: 2 },
  { id_columna: 3, nombre: "Revisión", orden: 3 },
  { id_columna: 4, nombre: "Terminado", orden: 4 },
];

// ─── API SIMULADA ─────────────────────────────────────────────────────────────
// Sustituir cada función por fetch('/api/...') cuando el backend esté listo.

const API = {
  delay: (ms = 120) => new Promise((r) => setTimeout(r, ms)),

  async getEquipos() {
    await this.delay();
    return structuredClone(MOCK_EQUIPOS);
  },

  async getUsuariosByEquipo(id_equipo) {
    await this.delay();
    return structuredClone(
      MOCK_USUARIOS.filter((u) => u.id_equipo === id_equipo),
    );
  },

  async getAllUsuarios() {
    await this.delay();
    return structuredClone(MOCK_USUARIOS);
  },

  async getProyectoByEquipo(id_equipo) {
    await this.delay();
    return structuredClone(
      MOCK_PROYECTOS.find((p) => p.id_equipo === id_equipo) || null,
    );
  },

  async getTareasByEquipo(id_equipo) {
    await this.delay();
    return structuredClone(
      MOCK_TAREAS.filter((t) => t.id_equipo === id_equipo),
    );
  },

  async updateTareaColumna(id_tarea, id_columna, estado) {
    await this.delay(80);
    const t = MOCK_TAREAS.find((t) => t.id_tarea === id_tarea);
    if (t) {
      t.id_columna = id_columna;
      t.estado = estado;
    }
    return { ok: true };
    // Reemplazar con:
    // return fetch(`/api/tareas/${id_tarea}`, {
    //   method: 'PUT',
    //   headers: { 'Content-Type': 'application/json' },
    //   body: JSON.stringify({ id_columna, estado })
    // }).then(r => r.json());
  },

  async createTarea(data) {
    await this.delay(150);
    const nueva = {
      ...data,
      id_tarea: Date.now(),
      fecha_creacion: new Date().toISOString(),
      bloqueado: false,
    };
    MOCK_TAREAS.push(nueva);
    return structuredClone(nueva);
    // Reemplazar con:
    // return fetch('/api/tareas', {
    //   method: 'POST',
    //   headers: { 'Content-Type': 'application/json' },
    //   body: JSON.stringify(data)
    // }).then(r => r.json());
  },

  async deleteTarea(id_tarea) {
    await this.delay(80);
    const i = MOCK_TAREAS.findIndex((t) => t.id_tarea === id_tarea);
    if (i !== -1) MOCK_TAREAS.splice(i, 1);
    return { ok: true };
  },

  getColumnas() {
    return structuredClone(MOCK_COLUMNAS);
  },
};
