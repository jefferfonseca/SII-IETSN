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
    <title>Gestión de Préstamos - Sistema de Préstamos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/SII-IETSN/css/usuario.css">
    <link rel="stylesheet" href="/SII-IETSN/css/prestamos.css">
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
                        <i class="material-icons">swap_horiz</i>
                    </div>
                    <div>
                        <h4>Gestión de Préstamos</h4>
                        <p>Administra los préstamos de elementos</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col s8 center">
                    <!-- Filtros -->
                    <div class="usuarios-filtros">
                        <!-- Estado -->
                        <div class="input-field">
                            <select id="filtroEstado">
                                <option value="" selected>Todos</option>
                                <option value="activo">Activos</option>
                                <option value="devuelto">Devueltos</option>
                            </select>
                            <label>Estado</label>
                        </div>

                        <!-- Buscador -->
                        <div class="input-field">
                            <input id="busquedaPrestamo" type="text" placeholder="Buscar por tomador o elemento">
                            <label for="busquedaPrestamo">Buscar</label>
                        </div>
                    </div>
                </div>

                <div class="col s4 center">
                    <a href="#modalNuevoPrestamo" class="btn btn-nuevo-prestamo waves-effect waves-light modal-trigger">
                        <i class="material-icons left">add_circle</i>
                        Nuevo Préstamo
                    </a>

                    <div class="col s4 center"><a href="#modalDevolucion"
                            class="btn btn-devolver waves-effect waves-light red modal-trigger "
                            onclick="abrirModalDevolucion()">
                            <i class="material-icons left">assignment_return</i>
                            Devolver
                        </a></div>

                </div>
            </div>
        </div>

        <!-- Préstamos Cards -->
        <div class="usuarios-container" id="prestamosContainer">

            <!-- EJEMPLO DE CARD - Este será generado dinámicamente -->
            <div class="usuario-card">
                <div class="usuario-header">
                    <div class="usuario-info">
                        <div class="usuario-avatar">JD</div>
                        <h3 class="usuario-nombre">Juan Pérez</h3>

                        <div class="usuario-documento">
                            <i class="material-icons">devices</i>
                            Laptop HP Pavilion
                        </div>

                        <div class="usuario-grado">
                            <i class="material-icons">event</i>
                            Prestado: 28/01/2026
                        </div>

                        <div class="usuario-grado">
                            <i class="material-icons">schedule</i>
                            Devolución: 04/02/2026
                        </div>
                    </div>

                    <div class="usuario-badges">
                        <span class="badge-estado badge-activo">
                            <i class="material-icons">pending</i>
                            Activo
                        </span>
                    </div>
                </div>

                <div class="usuario-acciones">
                    <a href="#modalDetallePrestamo" class="btn btn-accion btn-editar modal-trigger">
                        <i class="material-icons">visibility</i>
                        Ver Detalle
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Nuevo Préstamo -->
    <div id="modalNuevoPrestamo" class="modal modal-prestamo">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="material-icons">add_circle</i>
                </div>
                <div class="modal-title">
                    <h5>Nuevo Préstamo</h5>
                    <p>Registra un nuevo préstamo de elementos</p>
                </div>
            </div>

            <!-- PASO 1: Escanear Tomador -->
            <div class="prestamo-seccion">
                <div class="seccion-header">
                    <i class="material-icons">person</i>
                    <h6>1. Escanear Tomador</h6>
                </div>

                <div class="qr-scanner-container">

                    <!-- Placeholder -->
                    <div class="qr-placeholder" id="placeholderTomador">
                        <i class="material-icons">qr_code_scanner</i>
                        <p>Haz clic en el botón para escanear el QR del tomador</p>
                    </div>

                    <!-- Scanner real -->
                    <div id="readerTomador"></div>

                </div>

                <!-- Info Tomador Escaneado -->
                <div id="infoTomador" class="info-escaneado" style="display: none;">
                    <div class="chip-info">
                        <i class="material-icons">check_circle</i>
                        <span id="nombreTomador">Juan Pérez - 1234567890</span>
                    </div>
                </div>
            </div>

            <!-- PASO 2: Escanear Elementos -->
            <div class="prestamo-seccion" id="seccionElementos">
                <div class="seccion-header">
                    <i class="material-icons">devices</i>
                    <h6>2. Escanear Elementos</h6>
                </div>

                <div class="qr-scanner-container" id="scannerElementoContainer">
                    <div class="qr-placeholder" id="placeholderElemento">
                        <i class="material-icons">qr_code_scanner</i>
                        <p>Escanea los elementos que se van a prestar</p>
                    </div>
                    <div id="readerElemento"></div>
                </div>

                <!-- Lista de Elementos Escaneados -->
                <div id="listaElementos" class="elementos-lista">
                    <!-- Se llenará dinámicamente -->
                </div>
            </div>

            <!-- PASO 3: Fecha de Devolución -->
            <div class="prestamo-seccion">
                <div class="seccion-header">
                    <i class="material-icons">event</i>
                    <h6>3. Fecha de Devolución</h6>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <input id="fecha_devolucion" type="date" class="validate">
                        <label for="fecha_devolucion" class="active">Fecha estimada de devolución</label>
                    </div>
                </div>
            </div>

            <!-- PASO 4: Observaciones -->
            <div class="prestamo-seccion">
                <div class="seccion-header">
                    <i class="material-icons">notes</i>
                    <h6>4. Observaciones</h6>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <textarea id="observaciones" class="materialize-textarea"></textarea>
                        <label for="observaciones">Estado al entregar, notas adicionales...</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect btn btn-cancelar">Cancelar</a>
            <a href="#!" class="waves-effect waves-light btn btn-guardar" id="btnGuardarPrestamo">
                <i class="material-icons left">save</i>
                Registrar Préstamo
            </a>
        </div>
    </div>

    <!-- Modal Detalle Préstamo -->
    <div id="modalDetallePrestamo" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="material-icons">receipt_long</i>
                </div>
                <div class="modal-title">
                    <h5>Detalle del Préstamo</h5>
                    <p>Información completa del préstamo</p>
                </div>
            </div>

            <!-- Información del Tomador -->
            <div class="detalle-seccion">
                <h6 class="detalle-titulo">
                    <i class="material-icons">person</i>
                    Tomador
                </h6>

                <div class="detalle-contenido">
                    <p><strong>Nombre:</strong> <span id="detalle_nombre_tomador">Juan Pérez</span></p>
                    <p><strong>Documento:</strong> <span id="detalle_doc_tomador">1234567890</span></p>
                    <p><strong>Rol:</strong> <span id="detalle_rol_tomador">Estudiante</span></p>
                </div>
            </div>

            <!-- Información del Elemento -->
            <div class="detalle-seccion">
                <h6 class="detalle-titulo">
                    <i class="material-icons">devices</i>
                    Elemento Prestado
                </h6>
                <div class="detalle-contenido">
                    <p><strong>Nombre:</strong> <span id="detalle_nombre_elemento">Laptop HP Pavilion</span></p>
                    <p><strong>Código:</strong> <span id="detalle_codigo_elemento">LAP-001</span></p>
                    <p><strong>Categoría:</strong> <span id="detalle_categoria_elemento">Equipos de Cómputo</span>
                    </p>
                </div>
            </div>

            <!-- Información del Préstamo -->
            <div class="detalle-seccion">
                <h6 class="detalle-titulo">
                    <i class="material-icons">event</i>
                    Fechas
                </h6>
                <div class="detalle-contenido">
                    <p><strong>Fecha de Préstamo:</strong> <span id="detalle_fecha_prestamo">28/01/2026</span></p>
                    <p><strong>Fecha de Devolución Estimada:</strong> <span
                            id="detalle_fecha_devolucion">04/02/2026</span></p>
                    <p><strong>Estado:</strong> <span id="detalle_estado"
                            class="badge-estado badge-activo">Activo</span></p>
                </div>
            </div>

            <!-- Observaciones -->
            <div class="detalle-seccion">
                <h6 class="detalle-titulo">
                    <i class="material-icons">notes</i>
                    Observaciones
                </h6>
                <div class="detalle-contenido">
                    <p id="detalle_observaciones">Equipo en buen estado, incluye cargador.</p>
                </div>
            </div>

            <!-- Operador -->
            <div class="detalle-seccion">
                <h6 class="detalle-titulo">
                    <i class="material-icons">person_outline</i>
                    Registrado por
                </h6>
                <div class="detalle-contenido">
                    <p id="detalle_operador">Admin - María García</p>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect btn btn-cancelar">Cerrar</a>
            <a href="#!" class="waves-effect waves-light btn teal" id="btnMarcarDevuelto">
                <i class="material-icons left">assignment_return</i>
                Marcar como Devuelto
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        // ===== ESTADO DEL MODAL DE PRÉSTAMOS =====
        let tomadorActual = null;
        let elementosEscaneados = [];
        let scannerTomador = null;
        let scannerElemento = null;
        let tomadorProcesado = false;


        document.addEventListener('DOMContentLoaded', () => {
            cargarPrestamos();
            const modalElem = document.getElementById('modalNuevoPrestamo');

            M.Modal.init(modalElem, {
                dismissible: false,
                onOpenEnd: async () => {
                    tomadorProcesado = false;
                    await activarScannerTomador();
                },
                onCloseEnd: async () => {
                    await detenerScanner(scannerTomador);
                    await detenerScanner(scannerElemento);
                    resetearModalPrestamo();
                }
            });

            // Estos sí pueden quedarse aquí
            M.FormSelect.init(document.querySelectorAll('select'));
            M.updateTextFields();

            const obs = document.getElementById('observaciones');
            if (obs) M.textareaAutoResize(obs);
        });

        /* =====================================================
           FUNCIÓN SEGURA PARA DETENER SCANNERS
           ===================================================== */
        async function detenerScanner(scanner) {
            if (scanner && scanner.isScanning) {
                await scanner.stop();
            }
        }

        /* =====================================================
           ESCANEAR TOMADOR (AUTO AL ABRIR MODAL)
           ===================================================== */
        async function activarScannerTomador() {

            await detenerScanner(scannerElemento);

            const reader = document.getElementById('readerTomador');
            const placeholder = document.getElementById('placeholderTomador');
            document.getElementById('readerTomador').style.maxWidth = '320px';

            placeholder.style.display = 'none';
            reader.style.display = 'block';

            if (!scannerTomador) {
                scannerTomador = new Html5Qrcode("readerTomador");
            }

            if (scannerTomador.isScanning) return;

            setTimeout(() => {
                scannerTomador.start(
                    { facingMode: "environment" },
                    {
                        fps: 15,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    },
                    async (decodedText) => {
                        if (tomadorProcesado) return; // 🔒 BLOQUEO CRÍTICO
                        tomadorProcesado = true;
                        try {
                            const data = JSON.parse(decodedText);

                            if (data.tipo !== 'usuario' || !data.doc_hash) {
                                M.toast({ html: 'QR de usuario inválido', classes: 'red' });
                                return;
                            }

                            const response = await fetch('/SII-IETSN/api/usuarios/por-hash.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ doc_hash: data.doc_hash })
                            });

                            const result = await response.json();

                            if (!result.success) {
                                M.toast({ html: result.message, classes: 'red' });
                                return;
                            }

                            tomadorActual = {
                                id_tomador: result.data.id ?? result.data.id_usuario,
                                nombre: result.data.nombre,
                                apellido: result.data.apellido,
                                rol: result.data.rol
                            };

                            document.getElementById('nombreTomador').innerText =
                                `${tomadorActual.nombre} ${tomadorActual.apellido} (${tomadorActual.rol})`;

                            document.getElementById('infoTomador').style.display = 'block';

                            await detenerScanner(scannerTomador);

                            reader.style.display = 'none';
                            placeholder.style.display = 'block';

                            M.toast({
                                html: 'Tomador validado. Escanee los elementos.',
                                classes: 'green'
                            });

                            iniciarEscanerElemento();

                        } catch (err) {
                            console.error(err);
                            if (!tomadorProcesado) {
                                M.toast({ html: 'Error al leer el QR del tomador', classes: 'red' });
                            }
                        }
                    }
                );
            }, 300);
        }

        /* =====================================================
           ESCANEAR ELEMENTOS
           ===================================================== */
        async function iniciarEscanerElemento() {

            const reader = document.getElementById('readerElemento');
            const placeholder = document.getElementById('placeholderElemento');
            document.getElementById('readerElemento').style.maxWidth = '320px';

            document.getElementById('seccionElementos')
                .scrollIntoView({ behavior: 'smooth', block: 'center' });

            placeholder.style.display = 'none';
            reader.style.display = 'block';

            if (!scannerElemento) {
                scannerElemento = new Html5Qrcode("readerElemento");
            }

            if (scannerElemento.isScanning) return;

            setTimeout(() => {
                scannerElemento.start(
                    { facingMode: "environment" },
                    {
                        fps: 15,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    },
                    async (decodedText) => {
                        try {
                            const data = JSON.parse(decodedText);

                            if (data.tipo !== 'elemento' || typeof data.id_elemento === 'undefined') {
                                M.toast({ html: 'QR de elemento inválido', classes: 'red' });
                                return;
                            }


                            if (elementosEscaneados.some(el => el.id_elemento === data.id_elemento)) {
                                M.toast({ html: 'Elemento ya escaneado', classes: 'orange' });
                                return;
                            }


                            const response = await fetch('/SII-IETSN/api/elementos/validar.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ id_elemento: data.id_elemento })
                            });

                            const result = await response.json();
                            console.log('ESTADO ELEMENTO:', result.debug_estado);


                            if (!result.success) {
                                M.toast({ html: result.message, classes: 'red' });
                                return;
                            }

                            elementosEscaneados.push(result.data);
                            renderizarElementos();

                            M.toast({ html: 'Elemento agregado', classes: 'green' });

                        } catch (err) {
                            console.error(err);
                            M.toast({ html: 'Error al leer el QR del elemento', classes: 'red' });
                        }
                    }
                );
            }, 300);
        }

        /* =====================================================
           RENDER ELEMENTOS
           ===================================================== */
        function renderizarElementos() {
            const cont = document.getElementById('listaElementos');
            cont.innerHTML = '';

            elementosEscaneados.forEach((el, i) => {
                cont.innerHTML += `
            <div class="chip">
                ${el.nombre} (${el.codigo})
                <i class="close material-icons" onclick="quitarElemento(${i})">close</i>
            </div>
        `;
            });
        }

        function quitarElemento(i) {
            elementosEscaneados.splice(i, 1);
            renderizarElementos();
        }

        /* =====================================================
           GUARDAR PRÉSTAMOS
           ===================================================== */
        document.getElementById('btnGuardarPrestamo').addEventListener('click', async () => {

            if (!tomadorActual || !tomadorActual.id_tomador || elementosEscaneados.length === 0) {
                M.toast({ html: 'Faltan datos para registrar', classes: 'red' });
                return;
            }

            const fecha = document.getElementById('fecha_devolucion').value;
            const observacion = document.getElementById('observaciones').value;

            for (const elemento of elementosEscaneados) {

                const payload = {
                    id_tomador: tomadorActual.id_tomador,          // ✅ CORRECTO
                    id_elemento: elemento.id_elemento,             // ✅ CORRECTO
                    fecha_devolucion: fecha,
                    observacion
                };


                const response = await fetch('/SII-IETSN/api/prestamos/crear.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (!result.success) {
                    M.toast({
                        html: `Error con ${elemento.nombre}: ${result.message}`,
                        classes: 'red'
                    });
                    return;
                }
            }

            M.toast({
                html: 'Préstamos registrados correctamente',
                classes: 'green'
            });

            const modal = M.Modal.getInstance(
                document.getElementById('modalNuevoPrestamo')
            );
            modal.close();
        });
        /* =====================================================
           RESET MODAL
           ===================================================== */
        function resetearModalPrestamo() {
            tomadorActual = null;
            elementosEscaneados = [];

            document.getElementById('listaElementos').innerHTML = '';
            document.getElementById('infoTomador').style.display = 'none';

            const btnElemento = document.getElementById('btnScanElemento');
            btnElemento.setAttribute('disabled', true);
            btnElemento.classList.add('disabled');
        }
        async function cargarPrestamos() {

            const response = await fetch('/SII-IETSN/api/prestamos/listar.php');
            const result = await response.json();

            if (!result.success) {
                M.toast({ html: 'Error al cargar préstamos', classes: 'red' });
                return;
            }

            renderizarPrestamos(result.data);
        }

        function renderizarPrestamos(prestamos) {

            const cont = document.getElementById('prestamosContainer');
            cont.innerHTML = '';

            prestamos.forEach(p => {

                const badgeEstado = p.estado === 'activo'
                    ? 'badge-activo'
                    : 'badge-devuelto';

                cont.innerHTML += `
        <div class="usuario-card">
            <div class="usuario-header">
                <div class="usuario-info">
                    <div class="usuario-avatar">
                        ${p.tomador_nombre.substring(0, 2).toUpperCase()}
                    </div>

                    <h3 class="usuario-nombre">${p.tomador_nombre}</h3>

                    <div class="usuario-documento">
                        <i class="material-icons">devices</i>
                        ${p.elemento_nombre} (${p.elemento_codigo})
                    </div>

                    <div class="usuario-grado">
                        <i class="material-icons">event</i>
                        Prestado: ${p.fecha_prestamo}
                    </div>

                    <div class="usuario-grado">
                        <i class="material-icons">schedule</i>
                        Devolución: ${p.fecha_devolucion ?? '—'}
                    </div>
                </div>

                <div class="usuario-badges">
                    <span class="badge-estado ${badgeEstado}">
                        ${p.estado.toUpperCase()}
                    </span>
                </div>
            </div>

            <div class="usuario-acciones">
                <a href="#modalDetallePrestamo"
                   class="btn btn-accion btn-editar modal-trigger"
                   onclick="verDetallePrestamo(${p.id})">
                    <i class="material-icons">visibility</i>
                    Ver Detalle
                </a>

                
            </div>
        </div>
        `;
            });
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

        // ===== SUBMENÚ ELEMENTOS =====
        const menuElementos = document.getElementById('menu-elementos');
        const submenuElementos = document.getElementById('submenu-elementos');

        if (menuElementos && submenuElementos) {
            menuElementos.addEventListener('click', () => {
                menuElementos.classList.toggle('open');
                submenuElementos.classList.toggle('open');
            });
        }
    </script>

</body>

</html>