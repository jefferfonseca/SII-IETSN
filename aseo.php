<?php
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Admin") {
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Aseo</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="/SII-IETSN/css/sidebar.css">
    <link rel="stylesheet" href="/SII-IETSN/css/aseo.css">

</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="top-bar">
            <div>
                <h4>Gestión de Aseo</h4>
                <p>Asignación automática y control</p>
            </div>

            <div style="display:flex; gap:10px;">

                <select id="grupo" class="browser-default">
                    <option value="">Seleccione grupo</option>
                </select>

                <button class="btn btn-guardar" onclick="generarAseo()">
                    Generar
                </button>
                <button class="btn blue" onclick="abrirTablero()">
                    Ver Tablero
                </button>
            </div>
        </div>

        <ul class="tabs">
            <li class="tab col s3">
                <a class="active" href="#tab-aseo">🧹 Aseo</a>
            </li>
            <li class="tab col s3">
                <a href="#tab-metricas">📊 Métricas</a>
            </li>
        </ul>


        <div id="tab-aseo">
            <div class="tareas-container" id="contenedor"></div>
            <div class="asistencia-container">

                <div class="asistencia-header">
                    <h5>Asistencia del grupo</h5>
                    <span id="contadorAsistencia"></span>
                </div>

                <!-- ENCABEZADO DE COLUMNAS -->
                <div class="at-col-header">
                    <span class="at-col-label">#</span>
                    <span class="at-col-label">Estudiante</span>
                    <span class="at-col-label">Estado</span>
                </div>

                <!-- CADA FILA (generada dinámicamente) -->
                <div id="lista-asistencia">


                </div>
            </div>
        </div>
        <!-- TODO lo que ya tienes (tarjetas, asistencia, etc) -->
    </div>

    <div id="tab-metricas" class="section">

        <!-- RESUMEN -->
        <div class="row">

            <div class="col s12 m4">
                <div class="card green lighten-4">
                    <div class="card-content">
                        <span class="card-title">🏆 <b>Mejor estudiante</b></span>
                        <p id="top-estudiante">-</p>
                    </div>
                </div>
            </div>

            <div class="col s12 m4">
                <div class="card red lighten-4">
                    <div class="card-content">
                        <span class="card-title">🚨 <b>En riesgo</b></span>
                        <p id="riesgo-estudiante">-</p>
                    </div>
                </div>
            </div>

            <div class="col s12 m4">
                <div class="card blue lighten-4">
                    <div class="card-content">
                        <span class="card-title">🔁 <b>Ciclo</b></span>
                        <p id="info-ciclo">-</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- BARRA DE PROGRESO -->
        <div class="section">
            <h6>Progreso del ciclo</h6>
            <div class="progress">
                <div id="barra-ciclo" class="determinate" style="width: 0%"></div>
            </div>
        </div>

        <!-- RANKING -->
        <div class="section">
            <h5>📊 Ranking</h5>
            <div id="ranking"></div>
        </div>

    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <script>

        const CONFIG = {
            barrer: 2,
            ordenar_mesas: 2,
            ordenar_sillas: 1,
            vaciar_canecas: 1,
            trapear: 2
        };
        document.addEventListener("DOMContentLoaded", () => {

            /* ===============================
               INIT TABS (CON EVENTO)
            =============================== */
            const elems = document.querySelectorAll('.tabs');

            M.Tabs.init(elems, {
                onShow: function (tab) {

                    const grado = document.getElementById("grupo").value;

                    // 🔥 SOLO cuando abre métricas
                    if (tab.id === "tab-metricas") {
                        cargarMetricas(grado);
                    }

                    // opcional: si quieres refrescar aseo al volver
                    if (tab.id === "tab-aseo") {
                        cargarAsistencia();
                        cargarAseo();
                    }
                }
            });

            /* ===============================
               CARGA INICIAL
            =============================== */
            cargarGrupos();

            /* ===============================
               CAMBIO DE GRUPO
            =============================== */
            document.getElementById("grupo").addEventListener("change", (e) => {

                const grado = e.target.value;

                // siempre actualiza aseo
                cargarAsistencia();
                cargarAseo();

                // 🔥 SOLO si estás en métricas, recarga métricas
                const tabActiva = document.querySelector(".tabs .active");
                if (tabActiva && tabActiva.getAttribute("href") === "#tab-metricas") {
                    cargarMetricas(grado);
                }
            });

        });

        function cargarGrupos() {
            fetch("/SII-IETSN/api/grados/listar.php", {
                credentials: "same-origin"
            })
                .then(r => r.json())
                .then(r => {

                    if (!r.success) {
                        M.toast({ html: "Error cargando grados", classes: "red" });
                        return;
                    }

                    const select = document.getElementById("grupo");

                    select.innerHTML = `<option value="">Seleccione grupo</option>`;

                    r.data.forEach(g => {
                        select.innerHTML += `<option value="${g.id_grado}">${g.nombre}</option>`;
                    });

                })
                .catch(err => {
                    console.error(err);
                    M.toast({ html: "Error de conexión", classes: "red" });
                });
        }

        function cargarAsistencia() {

            const id_grado = document.getElementById("grupo").value;
            if (!id_grado) return;

            fetch(`/SII-IETSN/api/asistencia/listar.php?id_grado=${id_grado}`)
                .then(r => r.json())
                .then(r => {

                    const cont = document.getElementById("lista-asistencia");
                    const contador = document.getElementById("contadorAsistencia");

                    cont.innerHTML = "";

                    let presentes = 0;
                    let ausentes = 0;
                    const marcados = [];
                    const pendientes = [];

                    const avatarColors = [
                        { bg: "#EAF3DE", color: "#3B6D11" },
                        { bg: "#E6F1FB", color: "#185FA5" },
                        { bg: "#EEEDFE", color: "#534AB7" },
                        { bg: "#FAEEDA", color: "#854F0B" },
                        { bg: "#FAECE7", color: "#993C1D" },
                        { bg: "#E1F5EE", color: "#0F6E56" },
                    ];

                    // 🔥 SEPARAR CORRECTAMENTE
                    r.data.forEach((est, index) => {

                        const metodo = (est.metodo || "").toLowerCase().trim();
                        const estado = (est.estado || "").toLowerCase().trim();

                        // 🔥 AUSENTE (marcado pero NO presente)
                        if (estado === "ausente") {
                            ausentes++;
                            marcados.push({ est, index });
                        }
                        else if (metodo === "prestamo" || metodo === "manual") {
                            presentes++;
                            marcados.push({ est, index });
                        }

                        // ⚪ PENDIENTES
                        else {
                            pendientes.push({ est, index });
                        }
                    });

                    // 🔥 RENDER
                    function renderRow(est, colorIndex) {

                        const metodo = (est.metodo || "").toLowerCase().trim();
                        const estado = (est.estado || "").toLowerCase().trim();

                        const { bg, color } = avatarColors[colorIndex % avatarColors.length];

                        const iniciales = `${est.nombre.charAt(0)}${est.apellido.charAt(0)}`.toUpperCase();
                        const num = String(colorIndex + 1).padStart(2, "0");

                        let estadoHTML = "";
                        let nameStyle = "";

                        // 🔴 AUSENTE (PRIORIDAD)
                        if (estado === "ausente") {
                            estadoHTML = `<span class="pill pill-ausente">Ausente</span>`;
                            nameStyle = `style="color:#9ca3af;"`;
                        }

                        // 🟢 PRÉSTAMO
                        else if (metodo === "prestamo") {
                            estadoHTML = `<span class="pill pill-present">Préstamo</span>`;
                            nameStyle = `style="color:#9ca3af;"`;
                        }

                        // 🔵 MANUAL
                        else if (metodo === "manual") {
                            estadoHTML = `<span class="pill pill-manual">Manual</span>`;
                            nameStyle = `style="color:#9ca3af;"`;
                        }

                        // ⚪ PENDIENTE
                        else {
                            estadoHTML = `
                        <button class="at-btn btn-p" onclick="marcarAsistencia(${est.id_usuario})" title="Presente">✓</button>
                        <button class="at-btn btn-a" onclick="marcarAusente(${est.id_usuario})" title="Ausente">✕</button>
                    `;
                        }

                        return `
                    <div class="at-row">
                        <span class="at-num">${num}</span>

                        <div class="at-student">
                            <div class="at-avatar" style="background:${bg}; color:${color};">
                                ${iniciales}
                            </div>
                            <span class="at-name" ${nameStyle}>
                                ${est.nombre} ${est.apellido}
                            </span>
                        </div>

                        <div class="at-actions">
                            ${estadoHTML}
                        </div>
                    </div>
                `;
                    }

                    // 🔥 MARCADOS
                    if (marcados.length > 0) {
                        cont.innerHTML += `<div class="at-sep">Marcados (${marcados.length})</div>`;
                        marcados.forEach(({ est }, i) => {
                            cont.innerHTML += renderRow(est, i);
                        });
                    }

                    // 🔥 PENDIENTES
                    if (pendientes.length > 0) {
                        cont.innerHTML += `<div class="at-sep">Pendientes (${pendientes.length})</div>`;
                        pendientes.forEach(({ est }, i) => {
                            cont.innerHTML += renderRow(est, marcados.length + i);
                        });
                    }

                    // 🔥 VACÍO
                    if (marcados.length === 0 && pendientes.length === 0) {
                        cont.innerHTML = `<p style="padding:12px; color:#9ca3af; font-size:14px;">
                    No hay estudiantes en este grado.
                </p>`;
                    }

                    contador.innerHTML = `
                        <b>${presentes} presente${presentes !== 1 ? "s" : ""} | 
                        ${ausentes} ausente${ausentes !== 1 ? "s" : ""}</b>
                    `;
                })
                .catch(err => {
                    console.error("Error cargando asistencia:", err);
                });
        }

        function marcarAsistencia(id_usuario) {

            const id_grado = document.getElementById("grupo").value;

            fetch("/SII-IETSN/api/asistencia/manual.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    id_usuario,
                    id_grado
                })
            })
                .then(() => cargarAsistencia());
        }

        function marcarAusente(id_usuario) {

            const id_grado = document.getElementById("grupo").value;

            fetch("/SII-IETSN/api/asistencia/ausente.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    id_usuario,
                    id_grado
                })
            })
                .then(r => r.json())
                .then(r => {

                    if (r.success) {
                        M.toast({
                            html: r.message || "Ausente registrado",
                            classes: "orange"
                        });

                        cargarAsistencia();

                    } else {
                        M.toast({
                            html: r.message || "Error",
                            classes: "red"
                        });
                    }
                });
        }

        function marcarAusenteAseo(id) {

            fetch("/SII-IETSN/api/aseo/ausente.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ id })
            })
                .then(res => res.json())
                .then(data => {

                    console.log("RESPUESTA:", data); // 👈 debug

                    if (data && data.success) {

                        M.toast({
                            html: data.message || "Marcado como ausente",
                            classes: "orange"
                        });

                        cargarAseo();

                    } else {
                        M.toast({
                            html: "Error en respuesta",
                            classes: "red"
                        });
                    }
                })
                .catch(err => {

                    console.error("ERROR FETCH:", err);

                    M.toast({
                        html: "Error de conexión",
                        classes: "red"
                    });
                });
        }

        function render(data) {

            const cont = document.getElementById("contenedor");
            cont.innerHTML = "";

            const CONFIG = {
                barrer: 2,
                ordenar_mesas: 2,
                ordenar_sillas: 1,
                vaciar_canecas: 1,
                trapear: 2
            };

            Object.keys(CONFIG).forEach(act => {

                const col = document.createElement("div");
                col.className = "columna";

                col.innerHTML = `
      <h6>${act.replace("_", " ").toUpperCase()}</h6>
      <div class="lista" id="${act}"></div>
    `;

                cont.appendChild(col);

                const lista = col.querySelector(".lista");

                data
                    .filter(t => t.actividad === act)
                    .forEach(t => {
                        console.log(t.id);
                        const item = document.createElement("div");
                        item.className = "tarea-item";

                        // 🔥 CLAVE: esto arregla el bug
                        item.dataset.id = t.id;

                        item.innerHTML = `
                        <span class="
                            ${t.estado === 'completado' ? 'texto-completado' : ''}
                            ${t.estado === 'ausente' ? 'texto-ausente' : ''}
                            ">
                            ${t.nombre} ${t.apellido}
                        </span>

                        <div class="acciones">
                            <div class="btn-check" onclick="completar(${t.id})">✔</div>

                            ${t.estado !== "completado" ? `
                            <div class="btn-ausente" onclick="marcarAusenteAseo(${t.id})">✖</div>
                            ` : ""}
                        </div>
                        `;

                        lista.appendChild(item);
                    });

                // 🔥 Drag & drop activo por columna
                new Sortable(lista, {
                    group: "shared",
                    animation: 150,
                    onEnd: guardarCambios
                });

            });

        }

        function abrirTablero() {

            const select = document.getElementById("grupo");
            const id_grado = select.value;

            if (!id_grado) {
                M.toast({ html: "Seleccione grupo", classes: "red" });
                return;
            }

            // obtener nombre visible del grado
            const nombre = select.options[select.selectedIndex].text;

            const url = `/SII-IETSN/aseo_tablero.php?grado=${id_grado}&nombre=${encodeURIComponent(nombre)}`;

            window.open(url, "_blank", "fullscreen=yes");
        }

        function generarAseo() {

            const grupo = document.getElementById("grupo").value;
            if (!grupo) return M.toast({ html: "Seleccione grupo", classes: "red" });

            fetch("/SII-IETSN/api/aseo/generar.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ grupo })
            })
                .then(r => r.json())
                .then(r => {
                    if (r.success) {
                        M.toast({ html: "Lista generada", classes: "green" });
                        cargarAseo();
                        abrirTablero();
                    }
                });
        }

        function cargarAseo() {

            const grupo = document.getElementById("grupo").value;
            if (!grupo) return;

            fetch(`/SII-IETSN/api/aseo/listar.php?grupo=${grupo}`)
                .then(r => r.json())
                .then(r => {
                    if (!r.success) {
                        M.toast({ html: "Error cargando", classes: "red" });
                        return;
                    }
                    render(r.data);
                });
        }

        function completar(id) {

            fetch("/SII-IETSN/api/aseo/completar.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id })
            })
                .then(r => r.json())
                .then(r => {
                    if (r.success) {
                        M.toast({
                            html: r.message || "Tarea completada",
                            classes: "green"
                        });
                        cargarAseo();
                    } else {
                        M.toast({
                            html: r.message || "Error",
                            classes: "red"
                        });
                    }
                });

        }

        function guardarCambios() {
            const datos = [];

            document.querySelectorAll(".lista").forEach(lista => {

                const actividad = lista.id;

                lista.querySelectorAll(".tarea-item").forEach((item, index) => {

                    datos.push({
                        id: item.dataset.id,
                        actividad,
                        orden: index
                    });

                });

            });

            fetch("/SII-IETSN/api/aseo/reordenar.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });

        }

        async function cargarMetricas(id_grado) {
            const res = await fetch(`/SII-IETSN/api/aseo/metricas_aseo.php?id_grado=${id_grado}`);
            const r = await res.json();

            if (!r.success) return;

            pintarMetricas(r.data);
        }

        function pintarMetricas(data) {

            pintarResumen(data.cumplimiento);
            pintarRanking(data.cumplimiento);
            pintarCiclo(data.ciclo);
        }

        function pintarResumen(data) {

            if (!data.length) return;

            const mejor = data[0];
            const peor = data[data.length - 1];

            document.getElementById("top-estudiante").innerHTML =
                `${mejor.estudiante} (${mejor.porcentaje}%)`;

            document.getElementById("riesgo-estudiante").innerHTML =
                `${peor.estudiante} (${peor.porcentaje}%)`;
        }

        function pintarRanking(data) {

            const div = document.getElementById("ranking");
            div.innerHTML = "";

            data.forEach((u, i) => {

                let color = "grey lighten-3";

                if (u.porcentaje >= 80) color = "green lighten-4";
                else if (u.porcentaje >= 50) color = "yellow lighten-4";
                else color = "red lighten-4";

                div.innerHTML += `
            <div class="card ${color}">
                <div class="card-content">
                    <strong>${i + 1}. ${u.estudiante}</strong>
                    <br>
                    ${u.porcentaje}% cumplimiento
                </div>
            </div>
        `;
            });
        }

        function pintarAlertas(data) {
            const div = document.getElementById("alertas");
            div.innerHTML = "";

            data.forEach(u => {
                div.innerHTML += `
            <div class="card-panel red lighten-4">
                ⚠ ${u.estudiante} — ${u.ausencias} ausencias
            </div>
        `;
            });
        }

        function pintarCiclo(c) {

            document.getElementById("info-ciclo").innerHTML =
                ` <b>Ciclo:</b> ${c.ciclo} | 
        ${c.total} / ${c.esperado} tareas`;

            document.getElementById("barra-ciclo").style.width =
                c.porcentaje + "%";

            const barra = document.getElementById("barra-ciclo");

            barra.style.width = c.porcentaje + "%";

            if (c.porcentaje >= 80) {
                barra.className = "determinate green";
            } else if (c.porcentaje >= 50) {
                barra.className = "determinate yellow";
            } else {
                barra.className = "determinate red";
            }
        }



    </script>

</body>

</html>