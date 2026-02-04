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
                const response = await fetch('/SII-IETSN/api/bitacora/listar.php');
                const result = await response.json();

                if (!result.success) {
                    M.toast({ html: 'Error al cargar bitácora', classes: 'red' });
                    return;
                }

                bitacoraCompleta = result.data;
                bitacoraFiltrada = [...bitacoraCompleta];

                actualizarEstadisticas();
                renderizarTimeline();
                renderizarPaginacion();

            } catch (error) {
                console.error(error);
                M.toast({ html: 'Error de conexión', classes: 'red' });
            }
        }

        /* =====================================================
           CARGAR FILTROS DINÁMICOS
           ===================================================== */
        async function cargarFiltros() {
            try {
                // Cargar usuarios
                const respUsuarios = await fetch('/SII-IETSN/api/usuarios/listar.php');
                const usuarios = await respUsuarios.json();

                const selectUsuario = document.getElementById('filtroUsuario');
                usuarios.data.forEach(u => {
                    const option = document.createElement('option');
                    option.value = u.id_usuario;
                    option.textContent = `${u.nombre} ${u.apellido}`;
                    selectUsuario.appendChild(option);
                });

                // Cargar elementos
                const respElementos = await fetch('/SII-IETSN/api/elementos/listar.php');
                const elementos = await respElementos.json();

                const selectElemento = document.getElementById('filtroElemento');
                elementos.data.forEach(e => {
                    const option = document.createElement('option');
                    option.value = e.id_elemento;
                    option.textContent = `${e.nombre} (${e.codigo})`;
                    selectElemento.appendChild(option);
                });

                M.FormSelect.init(document.querySelectorAll('select'));

            } catch (error) {
                console.error(error);
            }
        }

        /* =====================================================
           APLICAR FILTROS
           ===================================================== */
        function aplicarFiltros() {
            const accion = document.getElementById('filtroAccion').value;
            const usuario = document.getElementById('filtroUsuario').value;
            const elemento = document.getElementById('filtroElemento').value;
            const fechaInicio = document.getElementById('fechaInicio').value;
            const fechaFin = document.getElementById('fechaFin').value;

            bitacoraFiltrada = bitacoraCompleta.filter(registro => {
                let cumple = true;

                if (accion && registro.accion !== accion) cumple = false;
                if (usuario && registro.id_usuario != usuario) cumple = false;
                if (elemento && registro.id_elemento != elemento) cumple = false;

                if (fechaInicio) {
                    const fechaRegistro = new Date(registro.fecha);
                    const fechaInicioObj = new Date(fechaInicio);
                    if (fechaRegistro < fechaInicioObj) cumple = false;
                }

                if (fechaFin) {
                    const fechaRegistro = new Date(registro.fecha);
                    const fechaFinObj = new Date(fechaFin + ' 23:59:59');
                    if (fechaRegistro > fechaFinObj) cumple = false;
                }

                return cumple;
            });

            paginaActual = 1;
            actualizarEstadisticas();
            renderizarTimeline();
            renderizarPaginacion();

            M.toast({ html: `${bitacoraFiltrada.length} registros encontrados`, classes: 'blue' });
        }

        /* =====================================================
           LIMPIAR FILTROS
           ===================================================== */
        function limpiarFiltros() {
            document.getElementById('filtroAccion').value = '';
            document.getElementById('filtroUsuario').value = '';
            document.getElementById('filtroElemento').value = '';
            document.getElementById('fechaInicio').value = '';
            document.getElementById('fechaFin').value = new Date().toISOString().split('T')[0];

            M.FormSelect.init(document.querySelectorAll('select'));

            bitacoraFiltrada = [...bitacoraCompleta];
            paginaActual = 1;

            actualizarEstadisticas();
            renderizarTimeline();
            renderizarPaginacion();
        }

        /* =====================================================
           ACTUALIZAR ESTADÍSTICAS
           ===================================================== */
        function actualizarEstadisticas() {
            const stats = {
                prestamo: 0,
                devolucion: 0,
                mantenimiento: 0,
                observacion: 0
            };

            bitacoraFiltrada.forEach(r => {
                if (stats[r.accion] !== undefined) {
                    stats[r.accion]++;
                }
            });

            document.getElementById('totalPrestamos').textContent = stats.prestamo;
            document.getElementById('totalDevoluciones').textContent = stats.devolucion;
            document.getElementById('totalMantenimientos').textContent = stats.mantenimiento;
            document.getElementById('totalObservaciones').textContent = stats.observacion;
        }

        /* =====================================================
           RENDERIZAR TIMELINE
           ===================================================== */
        function renderizarTimeline() {
            const timeline = document.getElementById('timelineBitacora');
            timeline.innerHTML = '';

            const inicio = (paginaActual - 1) * registrosPorPagina;
            const fin = inicio + registrosPorPagina;
            const registrosPagina = bitacoraFiltrada.slice(inicio, fin);

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
                    <div class="timeline-item" style="animation-delay: ${index * 0.05}s" onclick="verDetalleEvento(${evento.id})">
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
            const totalPaginas = Math.ceil(bitacoraFiltrada.length / registrosPorPagina);
            const paginacion = document.getElementById('paginacion');
            paginacion.innerHTML = '';

            if (totalPaginas <= 1) return;

            // Botón anterior
            paginacion.innerHTML += `
                <li class="${paginaActual === 1 ? 'disabled' : 'waves-effect'}">
                    <a href="#!" onclick="${paginaActual > 1 ? 'cambiarPagina(' + (paginaActual - 1) + ')' : 'return false'}">
                        <i class="material-icons">chevron_left</i>
                    </a>
                </li>
            `;

            // Números de página
            for (let i = 1; i <= totalPaginas; i++) {
                paginacion.innerHTML += `
                    <li class="${i === paginaActual ? 'active' : 'waves-effect'}">
                        <a href="#!" onclick="cambiarPagina(${i})">${i}</a>
                    </li>
                `;
            }

            // Botón siguiente
            paginacion.innerHTML += `
                <li class="${paginaActual === totalPaginas ? 'disabled' : 'waves-effect'}">
                    <a href="#!" onclick="${paginaActual < totalPaginas ? 'cambiarPagina(' + (paginaActual + 1) + ')' : 'return false'}">
                        <i class="material-icons">chevron_right</i>
                    </a>
                </li>
            `;
        }

        function cambiarPagina(pagina) {
            paginaActual = pagina;
            renderizarTimeline();
            renderizarPaginacion();
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
                'prestamo': 'swap_horiz',
                'devolucion': 'assignment_return',
                'mantenimiento': 'build',
                'observacion': 'comment'
            };
            return iconos[accion] || 'info';
        }

        function obtenerColorAccion(accion) {
            const colores = {
                'prestamo': 'color-prestamo',
                'devolucion': 'color-devolucion',
                'mantenimiento': 'color-mantenimiento',
                'observacion': 'color-observacion'
            };
            return colores[accion] || 'color-default';
        }

        function obtenerNombreAccion(accion) {
            const nombres = {
                'prestamo': 'Préstamo',
                'devolucion': 'Devolución',
                'mantenimiento': 'Mantenimiento',
                'observacion': 'Observación'
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
                a.download = `bitacora_${new Date().getTime()}.xlsx`;
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