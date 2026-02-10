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

    <link rel="stylesheet" href="/SII-IETSN/css/sidebar.css">
    <link rel="stylesheet" href="/SII-IETSN/css/usuario.css">
    <link rel="stylesheet" href="/SII-IETSN/css/prestamos.css">
    <link rel="stylesheet" href="/SII-IETSN/css/prestamos-tabla-styles.css">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="main-content" id="mainContent">

    <!-- TOP BAR -->
    <div class="top-bar">
        <div style="display:flex;align-items:center;gap:20px;">
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

        <div class="usuarios-filtros">
            <div class="input-field">
                <select id="filtroEstado">
                    <option value="activo" selected>Activos</option>
                    <option value="devuelto">Devueltos</option>
                </select>
                <label>Estado</label>
            </div>

            <div class="input-field">
                <input id="busquedaPrestamo" type="text" placeholder="Buscar por tomador o elemento">
                <label for="busquedaPrestamo">Buscar</label>
            </div>
        </div>

        <div id="contadorVencidos" class="contador-vencidos" style="display:none;">
            🚨 Préstamos vencidos: <strong id="numeroVencidos">0</strong>
        </div>

        <div class="center">
            <a href="#modalNuevoPrestamo" class="btn btn-accion btn-nuevo-usuario modal-trigger">
                <i class="material-icons left">add_circle</i>
                Nuevo Préstamo
            </a>
            <br>
            <a href="#modalDevolucion" class="btn btn-accion teal modal-trigger">
                <i class="material-icons left">assignment_return</i>
                Devolver
            </a>
        </div>
    </div>

    <!-- TABLA -->
    <div class="prestamos-tabla-container">
        <table class="highlight responsive-table" id="tablaPrestamos">
            <thead>
                <tr>
                    <th>Tomador</th>
                    <th>Elemento</th>
                    <th>Fecha Préstamo</th>
                    <th>Fecha Devolución</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody id="prestamosContainer"></tbody>
        </table>
    </div>

</div>

<!-- MODAL NUEVO PRÉSTAMO -->
<div id="modalNuevoPrestamo" class="modal modal-prestamo">
    <div class="modal-content">

        <div class="modal-header">
            <div class="modal-icon"><i class="material-icons">add_circle</i></div>
            <div class="modal-title">
                <h5>Nuevo Préstamo</h5>
                <p>Escaneo con pistola lectora</p>
            </div>
        </div>

        <!-- TOMADOR -->
        <div class="prestamo-seccion">
            <h6><i class="material-icons">person</i> 1. Tomador</h6>

            <div class="input-field">
                <input id="inputQrTomador" type="text" autocomplete="off"
                       placeholder="Escanee el QR del tomador">
            </div>

            <div id="infoTomador" class="info-escaneado" style="display:none;">
                <div class="chip-info">
                    <i class="material-icons">check_circle</i>
                    <span id="nombreTomador"></span>
                </div>
            </div>
        </div>

        <!-- ELEMENTOS -->
        <div class="prestamo-seccion">
            <h6><i class="material-icons">devices</i> 2. Elementos</h6>

            <div class="input-field">
                <input id="inputQrElemento" type="text" autocomplete="off"
                       placeholder="Escanee los elementos">
            </div>

            <div id="listaElementos" class="elementos-lista"></div>
        </div>

        <!-- FECHA -->
        <div class="prestamo-seccion">
            <h6><i class="material-icons">event</i> 3. Fecha devolución</h6>

            <div class="input-field">
                <input id="fecha_devolucion" type="date">
                <label for="fecha_devolucion" class="active">Fecha estimada</label>
            </div>
        </div>

        <!-- OBS -->
        <div class="prestamo-seccion">
            <h6><i class="material-icons">notes</i> 4. Observaciones</h6>

            <div class="input-field">
                <textarea id="observaciones" class="materialize-textarea"></textarea>
                <label for="observaciones">Observaciones</label>
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <a href="#!" class="modal-close btn btn-accion btn-cancelar">Cancelar</a>
        <a href="#!" id="btnGuardarPrestamo" class="btn btn-guardar btn-accion">
            <i class="material-icons left">save</i>
            Registrar Préstamo
        </a>
    </div>
</div>

<!-- MODAL DEVOLUCIÓN -->
<div id="modalDevolucion" class="modal modal-prestamo">
    <div class="modal-content">

        <div class="modal-header">
            <div class="modal-icon"><i class="material-icons">assignment_return</i></div>
            <div class="modal-title">
                <h5>Devolver elemento</h5>
            </div>
        </div>

        <div class="input-field">
            <input id="inputQrDevolucion" type="text" autocomplete="off"
                   placeholder="Escanee el QR del elemento">
        </div>

        <div id="infoDevolucion" class="info-escaneado" style="display:none">
            <div class="chip-info">
                <i class="material-icons">check_circle</i>
                <span id="textoDevolucion"></span>
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <a href="#!" class="modal-close btn btn-accion btn-cancelar">Cerrar</a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

</body>
</html>

   <script>
/* =====================================================
   ESTADO GLOBAL
===================================================== */
let tomadorActual = null;
let elementosEscaneados = [];
let prestamosGlobales = [];

/* =====================================================
   INIT
===================================================== */
document.addEventListener('DOMContentLoaded', () => {

    cargarPrestamos();

    M.Modal.init(document.querySelectorAll('.modal'), {
        dismissible: true,
        onOpenEnd: () => {
            resetearModalPrestamo();
            setFechaHoy();
            document.getElementById('inputQrTomador')?.focus();
        }
    });

    M.FormSelect.init(document.querySelectorAll('select'));

    document.getElementById('filtroEstado')
        .addEventListener('change', aplicarFiltros);

    document.getElementById('busquedaPrestamo')
        .addEventListener('input', aplicarFiltros);

    document.getElementById('inputQrTomador')
        ?.addEventListener('keydown', manejarTomador);

    document.getElementById('inputQrElemento')
        ?.addEventListener('keydown', manejarElemento);

    document.getElementById('inputQrDevolucion')
        ?.addEventListener('keydown', manejarDevolucion);

    document.getElementById('btnGuardarPrestamo')
        ?.addEventListener('click', guardarPrestamo);
});

/* =====================================================
   UTILIDADES
===================================================== */
function setFechaHoy() {
    const f = document.getElementById('fecha_devolucion');
    if (!f) return;
    const d = new Date();
    f.value = d.toISOString().slice(0, 10);
}

function limpiarInput(el) {
    if (el) el.value = '';
}

/* =====================================================
   TOMADOR
===================================================== */
async function manejarTomador(e) {
    if (e.key !== 'Enter') return;

    const qr_token = e.target.value.trim();
    if (!qr_token) return;

    limpiarInput(e.target);

    try {
        const res = await fetch('/SII-IETSN/api/usuarios/por-token.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr_token })
        });

        const r = await res.json();

        if (!r.success) {
            M.toast({ html: r.message, classes: 'red' });
            return;
        }

        tomadorActual = r.data;

        document.getElementById('nombreTomador').innerText =
            `${tomadorActual.nombre} ${tomadorActual.apellido} (${tomadorActual.rol})`;

        document.getElementById('infoTomador').style.display = 'block';

        M.toast({ html: 'Tomador validado', classes: 'green' });

        document.getElementById('inputQrElemento')?.focus();

    } catch {
        M.toast({ html: 'Error validando tomador', classes: 'red' });
    }
}

/* =====================================================
   ELEMENTOS
===================================================== */
async function manejarElemento(e) {
    if (e.key !== 'Enter') return;

    const qr_token = e.target.value.trim();
    if (!qr_token) return;

    limpiarInput(e.target);

    if (elementosEscaneados.some(el => el.qr_token === qr_token)) {
        M.toast({ html: 'Elemento ya escaneado', classes: 'orange' });
        return;
    }

    try {
        const res = await fetch('/SII-IETSN/api/elementos/validar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr_token })
        });

        const r = await res.json();

        if (!r.success) {
            M.toast({ html: r.message, classes: 'red' });
            return;
        }

        elementosEscaneados.push({ ...r.data, qr_token });
        renderizarElementos();

        M.toast({ html: 'Elemento agregado', classes: 'green' });

    } catch {
        M.toast({ html: 'Error validando elemento', classes: 'red' });
    }
}

/* =====================================================
   DEVOLUCIÓN
===================================================== */
async function manejarDevolucion(e) {
    if (e.key !== 'Enter') return;

    const qr_token = e.target.value.trim();
    if (!qr_token) return;

    limpiarInput(e.target);

    try {
        const res = await fetch('/SII-IETSN/api/prestamos/devolver.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr_token })
        });

        const r = await res.json();

        if (!r.success) {
            M.toast({ html: r.message, classes: 'red' });
            return;
        }

        document.getElementById('textoDevolucion').innerText =
            'Elemento devuelto correctamente';
        document.getElementById('infoDevolucion').style.display = 'block';

        M.toast({ html: 'Devolución registrada', classes: 'green' });

        cargarPrestamos();

    } catch {
        M.toast({ html: 'Error en devolución', classes: 'red' });
    }
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
   GUARDAR PRÉSTAMO
===================================================== */
async function guardarPrestamo() {

    if (!tomadorActual || elementosEscaneados.length === 0) {
        M.toast({ html: 'Faltan datos', classes: 'red' });
        return;
    }

    const fecha = document.getElementById('fecha_devolucion').value;
    const observacion = document.getElementById('observaciones').value;

    for (const el of elementosEscaneados) {
        const res = await fetch('/SII-IETSN/api/prestamos/crear.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_tomador: tomadorActual.id_usuario,
                qr_token: el.qr_token,
                fecha_devolucion: fecha,
                observacion
            })
        });

        const r = await res.json();
        if (!r.success) {
            M.toast({ html: r.message, classes: 'red' });
            return;
        }
    }

    M.toast({ html: 'Préstamo registrado', classes: 'green' });
    cargarPrestamos();

    M.Modal.getInstance(
        document.getElementById('modalNuevoPrestamo')
    ).close();
}

/* =====================================================
   RESET MODAL
===================================================== */
function resetearModalPrestamo() {
    tomadorActual = null;
    elementosEscaneados = [];

    document.getElementById('infoTomador').style.display = 'none';
    document.getElementById('listaElementos').innerHTML = '';

    limpiarInput(document.getElementById('inputQrTomador'));
    limpiarInput(document.getElementById('inputQrElemento'));
}

/* =====================================================
   LISTADO + FILTROS + ALARMAS
===================================================== */
async function cargarPrestamos() {

    const res = await fetch('/SII-IETSN/api/prestamos/listar.php');
    const r = await res.json();

    if (!r.success) {
        M.toast({ html: 'Error al cargar préstamos', classes: 'red' });
        return;
    }

    prestamosGlobales = r.data;
    aplicarFiltros();
    actualizarContadorVencidos(prestamosGlobales);
}

function aplicarFiltros() {

    const estado = document.getElementById('filtroEstado').value;
    const texto = document.getElementById('busquedaPrestamo').value.toLowerCase();

    let lista = [...prestamosGlobales];

    if (estado) lista = lista.filter(p => p.estado === estado);

    if (texto) {
        lista = lista.filter(p =>
            p.tomador_nombre.toLowerCase().includes(texto) ||
            p.elemento_nombre.toLowerCase().includes(texto) ||
            p.elemento_codigo.toLowerCase().includes(texto)
        );
    }

    renderizarPrestamos(lista);
}

/* =====================================================
   ALARMAS DE VENCIDOS (RESTAURADO)
===================================================== */
function actualizarContadorVencidos(prestamos) {

    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    let vencidos = 0;

    prestamos.forEach(p => {
        if (p.estado !== 'activo' || !p.fecha_devolucion) return;

        const f = new Date(p.fecha_devolucion);
        f.setHours(0, 0, 0, 0);

        if (f < hoy) vencidos++;
    });

    const cont = document.getElementById('contadorVencidos');
    const num = document.getElementById('numeroVencidos');

    if (vencidos > 0) {
        num.innerText = vencidos;
        cont.style.display = 'inline-flex';
    } else {
        cont.style.display = 'none';
    }
}

/* =====================================================
   RENDER PRESTAMOS (CON ALERTAS VISUALES)
===================================================== */
function renderizarPrestamos(prestamos) {

    const cont = document.getElementById('prestamosContainer');
    cont.innerHTML = '';

    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    prestamos.forEach(p => {

        let claseFila = '';
        let fechaHTML = p.fecha_devolucion ?? '—';

        if (p.estado === 'activo' && p.fecha_devolucion) {
            const f = new Date(p.fecha_devolucion);
            f.setHours(0, 0, 0, 0);

            if (f < hoy) {
                claseFila = 'fila-alerta';
                fechaHTML = `
                    <span class="fecha-alerta">${p.fecha_devolucion}</span>
                    <span class="badge-alerta">ALERTA</span>
                `;
            }
        }

        cont.innerHTML += `
            <tr class="${claseFila}">
                <td>${p.tomador_nombre}</td>
                <td>${p.elemento_nombre}<br><small>${p.elemento_codigo}</small></td>
                <td>${p.fecha_prestamo}</td>
                <td>${fechaHTML}</td>
                <td>
                    <span class="badge-estado badge-${p.estado}">
                        ${p.estado.toUpperCase()}
                    </span>
                </td>
            </tr>
        `;
    });
}

/* =====================================================
   SIDEBAR
===================================================== */
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('hidden');
    document.getElementById('mainContent').classList.toggle('expanded');
}
</script>


</body>

</html>