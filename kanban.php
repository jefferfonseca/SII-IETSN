<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Préstamos - Sistema de Préstamos</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="/SII-IETSN/css/sidebar.css">
    <link rel="stylesheet" href="/SII-IETSN/css/kanban.css">
    <!-- Favicon principal -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">

    <!-- Navegadores modernos (prefieren SVG) -->
    <link rel="icon" type="image/svg+xml" href="assets/images/qr-icon.svg">

    <!-- Ícono para móviles / PWA -->
    <link rel="apple-touch-icon" href="assets/images/icon-192.png">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <!-- ═══════════════════════════════════════════════════════
       TOPBAR
  ══════════════════════════════════════════════════════════ -->
    <div class="main-content" id="mainContent">
        <header id="topbar">

            <!-- Marca -->
            <div class="topbar-brand">
                <div class="brand-icon">⬡</div>
                EduKan
            </div>

            <div class="topbar-divider"></div>

            <!-- Controles -->
            <div class="topbar-controls">

                <!-- Selector de equipo -->
                <select id="select-equipo" class="topbar-select" aria-label="Seleccionar equipo">
                    <!-- Opciones inyectadas por JS -->
                </select>

                <!-- Filtro de estudiante -->
                <select id="select-usuario" class="topbar-select" aria-label="Filtrar por estudiante">
                    <option value="">Todos los estudiantes</option>
                </select>

                <!-- Estadísticas rápidas -->
                <div class="topbar-stats">
                    <span class="stat-chip" id="stat-atrasadas">Atrasadas: <span>0</span></span>
                    <span class="stat-chip" id="stat-bloqueadas">Bloqueadas: <span>0</span></span>
                </div>

            </div>

            <!-- Botón nueva tarea -->
            <button id="btn-nueva-tarea" aria-label="Crear nueva tarea">
                + <span>Nueva Tarea</span>
            </button>

        </header>

        <!-- ═══════════════════════════════════════════════════════
       BARRA DE PROYECTO
  ══════════════════════════════════════════════════════════ -->
        <div id="project-bar">
            <span class="project-name" id="project-name">Cargando…</span>
            <span class="project-equipo project-equipo-label" id="project-equipo-label"></span>
            <div class="project-miembros" id="project-miembros"></div>
        </div>

        <!-- ═══════════════════════════════════════════════════════
       TABLERO KANBAN
  ══════════════════════════════════════════════════════════ -->
        <main id="board" role="main" aria-label="Tablero Kanban">
            <!-- Columnas generadas por JS -->
        </main>

        <!-- ═══════════════════════════════════════════════════════
       MODAL — NUEVA TAREA
  ══════════════════════════════════════════════════════════ -->
        <div id="modal-overlay" class="modal-overlay" role="dialog" aria-modal="true"
            aria-labelledby="modal-title-label">
            <div class="modal-box">

                <div class="modal-header">
                    <h2 class="modal-title" id="modal-title-label">Nueva Tarea</h2>
                    <button class="modal-close" id="modal-close" aria-label="Cerrar modal">✕</button>
                </div>

                <form id="form-nueva-tarea" novalidate>

                    <!-- Título -->
                    <div class="form-group">
                        <label class="form-label" for="form-titulo">Título *</label>
                        <input id="form-titulo" name="titulo" type="text" class="form-control"
                            placeholder="Ej. Implementar módulo de login" maxlength="150" required />
                    </div>

                    <!-- Descripción -->
                    <div class="form-group">
                        <label class="form-label" for="form-descripcion">Descripción</label>
                        <textarea id="form-descripcion" name="descripcion" class="form-control"
                            placeholder="Detalla el objetivo de la tarea…" rows="3"></textarea>
                    </div>

                    <!-- Responsable / Prioridad -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="form-responsable">Responsable</label>
                            <select id="form-responsable" name="responsable" class="form-control">
                                <option value="">Sin asignar</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="form-prioridad">Prioridad</label>
                            <select id="form-prioridad" name="prioridad" class="form-control">
                                <option value="alta">🔴 Alta</option>
                                <option value="media" selected>🟡 Media</option>
                                <option value="baja">🟢 Baja</option>
                            </select>
                        </div>
                    </div>

                    <!-- Columna / Fecha límite -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="form-columna">Columna inicial</label>
                            <select id="form-columna" name="columna" class="form-control">
                                <option value="1">Pendiente</option>
                                <option value="2">En Proceso</option>
                                <option value="3">Revisión</option>
                                <option value="4">Terminado</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="form-fecha">Fecha límite</label>
                            <input id="form-fecha" name="fecha_limite" type="date" class="form-control" />
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" id="btn-cancel-modal">Cancelar</button>
                        <button type="submit" class="btn-submit">Crear Tarea</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════
       TOASTS
  ══════════════════════════════════════════════════════════ -->
    <div id="toast-container" aria-live="polite" aria-atomic="true"></div>

    <!-- ═══════════════════════════════════════════════════════
       SCRIPTS
       Orden obligatorio: data.js (API mock) → kanban.js (lógica)
  ══════════════════════════════════════════════════════════ -->
    <script src="js/data.js"></script>
    <script src="js/kanban.js"></script>

</body>

</html>