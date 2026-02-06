<?php
session_start();

// Validar sesión
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora del Sistema - Sistema de Préstamos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/SII-IETSN/css/usuario.css">
    <link rel="stylesheet" href="/SII-IETSN/css/bitacora.css">
</head>

<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Bar -->
        <div class="top-bar">
            <div style="display: flex; align-items: center; gap: 20px;">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="material-icons">menu</i>
                </button>

                <div class="page-title">
                    <div class="page-title-icon">
                        <i class="material-icons">history</i>
                    </div>
                    <div>
                        <h4>Bitácora del Sistema</h4>
                        <p>Historial completo de acciones y eventos</p>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="usuarios-filtros">
                <!-- Filtro por acción -->
                <div class="input-field">
                    <select id="filtroAccion">
                        <option value="" selected>Todas</option>
                        <option value="prestamo">Préstamos</option>
                        <option value="devolucion">Devoluciones</option>
                        <option value="mantenimiento">Mantenimiento</option>
                        <option value="observacion">Observaciones</option>
                    </select>
                    <label>Tipo de Acción</label>
                </div>

                <!-- Filtro por usuario -->
                <div class="input-field">
                    <select id="filtroUsuario">
                        <option value="" selected>Todos los usuarios</option>
                        <!-- Se llenarán dinámicamente -->
                    </select>
                    <label>Usuario</label>
                </div>

                <!-- Filtro por elemento -->
                <div class="input-field">
                    <select id="filtroElemento">
                        <option value="" selected>Todos los elementos</option>
                        <!-- Se llenarán dinámicamente -->
                    </select>
                    <label>Elemento</label>
                </div>

                <!-- Rango de fechas -->
                <div class="input-field">
                    <input id="fechaInicio" type="date">
                    <label for="fechaInicio" class="active">Desde</label>
                </div>

                <div class="input-field">
                    <input id="fechaFin" type="date">
                    <label for="fechaFin" class="active">Hasta</label>
                </div>

                <!-- Botones de acción -->
                <button class="btn waves-effect waves-light" onclick="aplicarFiltros()">
                    <i class="material-icons left">search</i>
                    Filtrar
                </button>

                <button class="btn btn-flat" onclick="limpiarFiltros()">
                    <i class="material-icons left">clear</i>
                    Limpiar
                </button>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="stats-container">
            <div class="stat-card stat-prestamos">
                <div class="stat-icon">
                    <i class="material-icons">swap_horiz</i>
                </div>
                <div class="stat-info">
                    <h6>Total Préstamos</h6>
                    <h3 id="totalPrestamos">0</h3>
                </div>
            </div>

            <div class="stat-card stat-devoluciones">
                <div class="stat-icon">
                    <i class="material-icons">assignment_return</i>
                </div>
                <div class="stat-info">
                    <h6>Total Devoluciones</h6>
                    <h3 id="totalDevoluciones">0</h3>
                </div>
            </div>

            <div class="stat-card stat-fuera-servicio">
                <div class="stat-icon">
                    <i class="material-icons">block</i>
                </div>
                <div class="stat-info">
                    <h6>Fuera de servicio</h6>
                    <h3 id="totalFueraServicio">0</h3>
                </div>
            </div>

            <div class="stat-card stat-mantenimientos">
                <div class="stat-icon">
                    <i class="material-icons">build</i>
                </div>
                <div class="stat-info">
                    <h6>Mantenimientos</h6>
                    <h3 id="totalMantenimientos">0</h3>
                </div>
            </div>

            <div class="stat-card stat-observaciones">
                <div class="stat-icon">
                    <i class="material-icons">comment</i>
                </div>
                <div class="stat-info">
                    <h6>Observaciones</h6>
                    <h3 id="totalObservaciones">0</h3>
                </div>
            </div>
        </div>

        <!-- Timeline de Bitácora -->
        <div class="bitacora-timeline-container">
            <div class="timeline-header">
                <h5>
                    <i class="material-icons">timeline</i>
                    Línea de Tiempo de Eventos
                </h5>
                <div class="timeline-actions">
                    <a href="#!" class="btn-flat" onclick="exportarBitacora()">
                        <i class="material-icons left">file_download</i>
                        Exportar
                    </a>
                </div>
            </div>

            <div class="timeline" id="timelineBitacora">
                <!-- Se llenará dinámicamente -->
            </div>
        </div>

        <!-- Paginación -->
        <div class="pagination-container">
            <ul class="pagination" id="paginacion">
                <!-- Se llenará dinámicamente -->
            </ul>
        </div>
    </div>

    <!-- Modal Detalle de Evento -->
    <div id="modalDetalleEvento" class="modal modal-detalle-evento">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon" id="modalIcono">
                    <i class="material-icons">info</i>
                </div>
                <div class="modal-title">
                    <h5 id="modalTitulo">Detalle del Evento</h5>
                    <p id="modalFecha">02/02/2026 14:30:00</p>
                </div>
            </div>

            <!-- Información del Usuario -->
            <div class="detalle-seccion">
                <h6 class="detalle-titulo">
                    <i class="material-icons">person</i>
                    Usuario Responsable
                </h6>
                <div class="detalle-contenido">
                    <p><strong>Nombre:</strong> <span id="detalle_usuario_nombre">María García</span></p>
                    <p><strong>Rol:</strong> <span id="detalle_usuario_rol">Admin</span></p>
                </div>
            </div>

            <!-- Información del Elemento -->
            <div class="detalle-seccion">
                <h6 class="detalle-titulo">
                    <i class="material-icons">devices</i>
                    Elemento Involucrado
                </h6>
                <div class="detalle-contenido">
                    <p><strong>Nombre:</strong> <span id="detalle_elemento_nombre">Laptop HP Pavilion</span></p>
                    <p><strong>Código:</strong> <span id="detalle_elemento_codigo">LAP-001</span></p>
                </div>
            </div>

            <!-- Tipo de Acción -->
            <div class="detalle-seccion">
                <h6 class="detalle-titulo">
                    <i class="material-icons">label</i>
                    Tipo de Acción
                </h6>
                <div class="detalle-contenido">
                    <span id="detalle_accion" class="badge-accion badge-prestamo">Préstamo</span>
                </div>
            </div>

            <!-- Detalles Adicionales -->
            <div class="detalle-seccion">
                <h6 class="detalle-titulo">
                    <i class="material-icons">description</i>
                    Detalles
                </h6>
                <div class="detalle-contenido">
                    <p id="detalle_texto">Elemento prestado en buen estado, incluye cargador y maletín.</p>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect btn btn-cancelar">Cerrar</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <script>
        let paginaActual = 1;
        const registrosPorPagina = 20;
        let bitacoraCompleta = [];
        let bitacoraFiltrada = [];
        let totalPaginas = 1;


        const filtroAccion = document.getElementById('filtroAccion');
        const filtroUsuario = document.getElementById('filtroUsuario');
        const filtroElemento = document.getElementById('filtroElemento');
        const fechaInicio = document.getElementById('fechaInicio');
        const fechaFin = document.getElementById('fechaFin');

        document.addEventListener('DOMContentLoaded', () => {
            // Inicializar componentes
            M.FormSelect.init(document.querySelectorAll('select'));
            M.Modal.init(document.querySelectorAll('.modal'));
            M.updateTextFields();

            // Establecer fecha de hoy como máximo
            const hoy = new Date().toISOString().split('T')[0];
            document.getElementById('fechaFin').value = hoy;

            // Cargar datos iniciales
            cargarFiltros();
            cargarBitacora();
        });

        /* =====================================================
           CARGAR BITÁCORA
           ===================================================== */
        async function cargarBitacora() {
            try {
                const accion = filtroAccion.value;
                const id_usuario = filtroUsuario.value;
                const id_elemento = filtroElemento.value;
                const fecha_inicio = fechaInicio.value;
                const fecha_fin = fechaFin.value;

                const params = new URLSearchParams({
                    page: paginaActual,
                    limit: registrosPorPagina
                });

                if (accion) params.append('accion', accion);
                if (id_usuario) params.append('id_usuario', id_usuario);
                if (id_elemento) params.append('id_elemento', id_elemento);
                if (fecha_inicio) params.append('fecha_inicio', fecha_inicio);
                if (fecha_fin) params.append('fecha_fin', fecha_fin);

                const resp = await fetch(
                    `/SII-IETSN/api/bitacora/listar.php?${params.toString()}`,
                    { credentials: 'same-origin' }
                );

                const res = await resp.json();
                if (!res.success) return;

                bitacoraFiltrada = res.data;
                totalPaginas = res.pagination.pages;

                actualizarEstadisticas()
                renderizarTimeline();
                renderizarPaginacion();

            } catch (e) {
                console.error(e);
                M.toast({ html: 'Error cargando bitácora', classes: 'red' });
            }
        }



        /* =====================================================
           CARGAR FILTROS DINÁMICOS
           ===================================================== */
        async function cargarFiltros() {
            try {
                const resp = await fetch('/SII-IETSN/api/bitacora/filtros.php', {
                    credentials: 'same-origin'
                });
                const data = await resp.json();

                if (!data.success) return;

                const selectUsuario = document.getElementById('filtroUsuario');
                const selectElemento = document.getElementById('filtroElemento');

                // Limpiar
                selectUsuario.innerHTML = `<option value="" selected>Todos los usuarios</option>`;
                selectElemento.innerHTML = `<option value="" selected>Todos los elementos</option>`;

                data.usuarios.forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = u.id;
                    opt.textContent = u.nombre;
                    selectUsuario.appendChild(opt);
                });

                data.elementos.forEach(e => {
                    const opt = document.createElement('option');
                    opt.value = e.id;
                    opt.textContent = `${e.nombre} (${e.codigo})`;
                    selectElemento.appendChild(opt);
                });

                M.FormSelect.init(document.querySelectorAll('select'));

            } catch (err) {
                console.error(err);
            }
        }


        /* =====================================================
           APLICAR FILTROS
           ===================================================== */
        function aplicarFiltros() {
            paginaActual = 1;
            cargarBitacora();
        }
        /* =====================================================
           LIMPIAR FILTROS
           ===================================================== */
        function limpiarFiltros() {
            document.getElementById('filtroAccion').value = '';
            document.getElementById('filtroUsuario').value = '';
            document.getElementById('filtroElemento').value = '';
            document.getElementById('fechaInicio').value = '';
            document.getElementById('fechaFin').value =
                new Date().toISOString().split('T')[0];

            M.FormSelect.init(document.querySelectorAll('select'));

            paginaActual = 1;
            cargarBitacora();
        }

        /* =====================================================
           ACTUALIZAR ESTADÍSTICAS
           ===================================================== */
        async function actualizarEstadisticas() {
            try {
                const params = new URLSearchParams();

                if (filtroAccion.value) params.append('accion', filtroAccion.value);
                if (filtroUsuario.value) params.append('id_usuario', filtroUsuario.value);
                if (filtroElemento.value) params.append('id_elemento', filtroElemento.value);
                if (fechaInicio.value) params.append('fecha_inicio', fechaInicio.value);
                if (fechaFin.value) params.append('fecha_fin', fechaFin.value);

                const resp = await fetch(
                    `/SII-IETSN/api/bitacora/stats.php?${params.toString()}`,
                    { credentials: 'same-origin' }
                );

                const res = await resp.json();
                if (!res.success) return;

                document.getElementById('totalPrestamos').textContent = res.stats.prestamo;
                document.getElementById('totalDevoluciones').textContent = res.stats.devolucion;
                document.getElementById('totalFueraServicio').textContent = res.stats.fuera_servicio;
                document.getElementById('totalMantenimientos').textContent = res.stats.mantenimiento;
                document.getElementById('totalObservaciones').textContent = res.stats.observacion;

            } catch (e) {
                console.error(e);
            }
        }


        /* =====================================================
           RENDERIZAR TIMELINE
           ===================================================== */
        function renderizarTimeline() {
            const timeline = document.getElementById('timelineBitacora');
            timeline.innerHTML = '';
            const registrosPagina = bitacoraFiltrada;

            if (registrosPagina.length === 0) {
                timeline.innerHTML = `
                    <div class="timeline-empty">
                        <i class="material-icons">inbox</i>
                        <p>No hay registros para mostrar</p>
                    </div>
                `;
                return;
            }

            registrosPagina.forEach((evento, index) => {
                const iconoAccion = obtenerIconoAccion(evento.accion);
                const colorAccion = obtenerColorAccion(evento.accion);
                const nombreAccion = obtenerNombreAccion(evento.accion);

                timeline.innerHTML += `
                    <div class="timeline-item ${evento.accion === 'fuera_servicio' ? 'fuera-servicio' : ''}"
                    style="animation-delay: ${index * 0.05}s" onclick="verDetalleEvento(${evento.id})">
                        <div class="timeline-marker ${colorAccion}">
                            <i class="material-icons">${iconoAccion}</i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="badge-accion badge-${evento.accion}">${nombreAccion}</span>
                                <span class="timeline-fecha">${formatearFecha(evento.fecha)}</span>
                            </div>
                            <div class="timeline-body">
                                <p class="timeline-usuario">
                                    <i class="material-icons tiny">person</i>
                                    ${evento.usuario_nombre} ${evento.usuario_apellido}
                                </p>
                                <p class="timeline-elemento">
                                    <i class="material-icons tiny">devices</i>
                                    ${evento.elemento_nombre} (${evento.elemento_codigo})
                                </p>
                                ${evento.detalle ? `<p class="timeline-detalle">${evento.detalle}</p>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        /* =====================================================
           RENDERIZAR PAGINACIÓN
           ===================================================== */
        function renderizarPaginacion() {
            const pag = document.getElementById('paginacion');
            pag.innerHTML = '';

            if (totalPaginas <= 1) return;

            pag.innerHTML += `
        <li class="${paginaActual === 1 ? 'disabled' : 'waves-effect'}">
            <a href="#!" onclick="cambiarPagina(${paginaActual - 1})">
                <i class="material-icons">chevron_left</i>
            </a>
        </li>
    `;

            for (let i = 1; i <= totalPaginas; i++) {
                pag.innerHTML += `
            <li class="${i === paginaActual ? 'active' : 'waves-effect'}">
                <a href="#!" onclick="cambiarPagina(${i})">${i}</a>
            </li>
        `;
            }

            pag.innerHTML += `
        <li class="${paginaActual === totalPaginas ? 'disabled' : 'waves-effect'}">
            <a href="#!" onclick="cambiarPagina(${paginaActual + 1})">
                <i class="material-icons">chevron_right</i>
            </a>
        </li>
    `;
        }

        function cambiarPagina(p) {
            if (p < 1 || p > totalPaginas) return;
            paginaActual = p;
            cargarBitacora();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }


        /* =====================================================
           VER DETALLE DE EVENTO
           ===================================================== */
        async function verDetalleEvento(id) {
            const evento = bitacoraFiltrada.find(e => e.id == id);
            if (!evento) return;

            // Actualizar modal
            document.getElementById('modalTitulo').textContent = `Evento #${evento.id}`;
            document.getElementById('modalFecha').textContent = formatearFecha(evento.fecha);

            document.getElementById('detalle_usuario_nombre').textContent =
                `${evento.usuario_nombre} ${evento.usuario_apellido}`;
            document.getElementById('detalle_usuario_rol').textContent = evento.usuario_rol;

            document.getElementById('detalle_elemento_nombre').textContent = evento.elemento_nombre;
            document.getElementById('detalle_elemento_codigo').textContent = evento.elemento_codigo;

            const badgeAccion = document.getElementById('detalle_accion');
            badgeAccion.className = `badge-accion badge-${evento.accion}`;
            badgeAccion.textContent = obtenerNombreAccion(evento.accion);

            document.getElementById('detalle_texto').textContent = evento.detalle || 'Sin detalles adicionales';

            // Actualizar icono del modal
            const modalIcono = document.getElementById('modalIcono');
            modalIcono.innerHTML = `<i class="material-icons">${obtenerIconoAccion(evento.accion)}</i>`;
            modalIcono.className = `modal-icon ${obtenerColorAccion(evento.accion)}`;

            // Abrir modal
            const modal = M.Modal.getInstance(document.getElementById('modalDetalleEvento'));
            modal.open();
        }

        /* =====================================================
           FUNCIONES AUXILIARES
           ===================================================== */
        function obtenerIconoAccion(accion) {
            const iconos = {
                prestamo: 'swap_horiz',
                devolucion: 'assignment_return',
                mantenimiento: 'build',
                fuera_servicio: 'block',
                observacion: 'comment'
            };

            return iconos[accion] || 'info';
        }
        function obtenerColorAccion(accion) {
            const colores = {
                prestamo: 'color-prestamo',
                devolucion: 'color-devolucion',
                mantenimiento: 'color-mantenimiento',
                fuera_servicio: 'color-fuera-servicio',
                observacion: 'color-observacion'
            };
            return colores[accion] || 'color-default';
        }

        function obtenerNombreAccion(accion) {
            const nombres = {
                prestamo: 'Préstamo',
                devolucion: 'Devolución',
                mantenimiento: 'Mantenimiento',
                fuera_servicio: 'Fuera de servicio',
                observacion: 'Observación'
            };

            return nombres[accion] || accion;
        }

        function formatearFecha(fecha) {
            const f = new Date(fecha);
            const opciones = {
                year: 'numeric',
                month: 'short',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            };
            return f.toLocaleDateString('es-ES', opciones);
        }

        /* =====================================================
           EXPORTAR BITÁCORA
           ===================================================== */
        async function exportarBitacora() {
            M.toast({ html: 'Generando reporte...', classes: 'blue' });

            try {
                const response = await fetch('/SII-IETSN/api/bitacora/exportar.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        filtros: {
                            accion: document.getElementById('filtroAccion').value,
                            usuario: document.getElementById('filtroUsuario').value,
                            elemento: document.getElementById('filtroElemento').value,
                            fechaInicio: document.getElementById('fechaInicio').value,
                            fechaFin: document.getElementById('fechaFin').value
                        }
                    })
                });

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `bitacora_${new Date().getTime()}.csv`;
                a.click();

                M.toast({ html: 'Reporte descargado', classes: 'green' });

            } catch (error) {
                console.error(error);
                M.toast({ html: 'Error al exportar', classes: 'red' });
            }
        }

        // ===== SIDEBAR =====
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
    </script>

</body>

</html>