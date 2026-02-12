<?php
session_start();

if (
    !isset($_SESSION["usuario"]) ||
    !in_array($_SESSION["usuario"]["rol"], ["Admin", "Docente"])
) {
    header("Location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Generar QR por Grado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Materialize -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/SII-IETSN/css/sidebar.css">
    <link rel="stylesheet" href="/SII-IETSN/css/usuario-qr.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content" id="mainContent">

        <!-- TOP BAR -->
        <div class="top-bar">
            <div style="display: flex; align-items: center;">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="material-icons">menu</i>
                </button>

                <div class="page-title" style="margin-left: 10px;">
                    <div class="page-title-icon">
                        <i class="material-icons">qr_code_2</i>
                    </div>
                    <div>
                        <h4>Generación de Códigos QR por Grado</h4>
                        <p>Genera QR para todos los estudiantes de un grado</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENIDO -->
        <div class="qr-container qr-stack">

            <!-- Card de Selección -->
            <div class="qr-card qr-full">
                <h5>
                    <i class="material-icons">school</i>
                    Seleccione un grado
                </h5>

                <div class="row" style="margin-bottom: 0;">
                    <div class="input-field col s12 m6">
                        <select id="gradoSelect" onchange="generarQR()">
                            <option value="" disabled selected>Seleccione un grado</option>
                        </select>
                        <label>Grado</label>
                    </div>

                    <div class="input-field col s12 m6">
                        <button class="btn btn-primary waves-effect waves-light" onclick="generarQR()">
                            <i class="material-icons left">qr_code</i>
                            Generar Códigos QR
                        </button>
                    </div>
                </div>

                <!-- Badges de estado -->
                <div class="badge-container" id="badgeQR" style="display:none;">
                    <span class="label">
                        <i class="material-icons tiny">info</i>
                        Estado de los códigos QR:
                    </span>
                    <span class="custom-badge badge-green">
                        <i class="material-icons tiny">check_circle</i>
                        <span id="badgeExistentes">0</span> existentes
                    </span>
                    <span class="custom-badge badge-blue">
                        <i class="material-icons tiny">fiber_new</i>
                        <span id="badgeNuevos">0</span> nuevos
                    </span>
                </div>

                <!-- Botón descarga ZIP -->
                <div class="button-group">
                    <button class="btn btn-success btn-accion waves-effect waves-light" id="btnZip"
                        onclick="descargarZIP()" style="display:none; width: auto !important; min-width: 250px;">
                        <i class="material-icons left">cloud_download</i>
                        Descargar ZIP del Grado
                    </button>
                </div>

                <!-- Loader -->
                <div id="progressContainer" style="display:none;">
                    <div class="progress">
                        <div id="progressBar" class="determinate" style="width: 0%"></div>
                    </div>
                    <div style="text-align:center; margin-top: 12px;">
                        <span id="progressText" style="font-weight: 600; color: #667eea; font-size: 14px;">0%</span>
                    </div>
                </div>

            </div>

            <!-- Card de resultados -->
            <div class="qr-card qr-full" id="resultado" style="display:none;">
                <h5>
                    <i class="material-icons">people</i>
                    Estudiantes del Grado
                </h5>

                <table class="highlight responsive-table">
                    <thead>
                        <tr>
                            <th>Nombre del Estudiante</th>
                            <th>Documento</th>
                            <th class="center-align">Código QR</th>
                            <th>Fecha de Generación</th>
                        </tr>
                    </thead>
                    <tbody id="tabla"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL VISTA QR -->
    <div id="modalVistaQR" class="modal">
        <div class="modal-content center-align">
            <h5 id="tituloQR">Código QR del Estudiante</h5>
            <h6 id="subtituloQR"></h6>
            <img id="imgVistaQR" src=""
                style="max-width:100%; max-height:60vh; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); border: 5px solid white;">
        </div>
        <div class="modal-footer">
            <a id="btnDescargarQR" href="#" download class="btn btn-download waves-effect waves-light">
                <i class="material-icons left">download</i>
                Descargar QR
            </a>
            <a href="#!" class="modal-close btn-flat waves-effect">
                <i class="material-icons left">close</i>
                Cerrar
            </a>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>

        let gradoActual = null;
        let rutaActual = null;
        let nombreGradoActual = null;

        // Sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('mainContent');
            sidebar.classList.toggle('hidden');
            main.classList.toggle('expanded');
        }

        document.addEventListener("DOMContentLoaded", () => {

            M.FormSelect.init(document.querySelectorAll("select"));

            const modalQR = document.getElementById("modalVistaQR");
            if (modalQR) {
                M.Modal.init(modalQR);
            }

            cargarGrados();
        });

        /* ================= CARGAR GRADOS ================= */
        function cargarGrados() {
            fetch("api/grados/listar.php")
                .then(r => r.json())
                .then(data => {

                    const select = document.getElementById("gradoSelect");
                    select.innerHTML = '<option value="" disabled selected>Seleccione un grado</option>';

                    data.data.forEach(g => {
                        const opt = document.createElement("option");
                        opt.value = g.id_grado;
                        opt.textContent = g.nombre;
                        select.appendChild(opt);
                    });

                    M.FormSelect.init(select);
                });
        }

        /* ================= GENERAR QR ================= */
        async function generarQR() {

            const select = document.getElementById("gradoSelect");
            if (!select) return;

            const grado = select.value;

            if (!grado) {
                M.toast({
                    html: '<i class="material-icons left">warning</i> Seleccione un grado',
                    classes: 'rounded orange'
                });
                return;
            }

            // Reset visual
            document.getElementById("badgeQR").style.display = "none";
            document.getElementById("badgeExistentes").textContent = "0";
            document.getElementById("badgeNuevos").textContent = "0";

            bloquearInterfaz(true);

            M.toast({
                html: '<i class="material-icons left">hourglass_top</i> Generando códigos QR...',
                classes: 'rounded blue'
            });

            try {

                const response = await fetch(
                    `api/usuarios/generar_qr_estudiantes_grado.php?id_grado=${grado}`
                );

                const data = await response.json();

                if (!data.success) {
                    bloquearInterfaz(false);
                    M.toast({
                        html: `<i class="material-icons left">info</i> ${data.message}`,
                        classes: 'rounded orange'
                    });
                    return;
                }

                gradoActual = grado;
                nombreGradoActual = select.options[select.selectedIndex].text;

                if (data.data.length > 0) {
                    rutaActual = data.data[0].ruta;
                }

                renderTabla(data.data);

                // 🔥 Mostrar contadores inteligentes
                document.getElementById("badgeQR").style.display = "flex";
                document.getElementById("badgeExistentes").textContent = data.existentes;
                document.getElementById("badgeNuevos").textContent = data.nuevos;

                M.toast({
                    html: `<i class="material-icons left">check_circle</i> ${data.nuevos} nuevos | ${data.existentes} existentes`,
                    classes: 'rounded green'
                });

                bloquearInterfaz(false);

            } catch (error) {
                bloquearInterfaz(false);
                console.error("Error:", error);
                M.toast({
                    html: '<i class="material-icons left">error</i> Error al generar códigos QR',
                    classes: 'rounded red'
                });
            }
        }

        /* ================= POLLING REAL ================= */
        function iniciarPollingGrado(onFinish) {

            const interval = setInterval(() => {

                fetch("api/usuarios/progreso_qr_grado.php")
                    .then(r => r.json())
                    .then(p => {

                        if (!p.total) return;

                        const porcentaje = Math.round((p.actual / p.total) * 100);

                        actualizarProgreso(porcentaje);

                        document.getElementById("progressText").textContent =
                            `Generando ${p.actual} de ${p.total} (${porcentaje}%)`;

                        if (p.completado) {

                            clearInterval(interval);

                            document.getElementById("progressText").textContent =
                                "✅ Completado";

                            setTimeout(() => {
                                if (typeof onFinish === "function") {
                                    onFinish();
                                }
                            }, 400);
                        }
                    });

            }, 400);
        }

        /* ================= TABLA ================= */
        function renderTabla(estudiantes) {

            const tbody = document.getElementById("tabla");
            if (!tbody) return;

            tbody.innerHTML = "";

            if (!estudiantes || estudiantes.length === 0) {
                M.toast({
                    html: '<i class="material-icons left">warning</i> No hay registros',
                    classes: 'rounded orange'
                });
                return;
            }

            estudiantes.forEach(est => {

                const url = `${est.ruta}/${est.archivo}`;

                const tr = document.createElement("tr");

                tr.innerHTML = `
            <td style="font-weight: 600; color: #2c3e50;">${est.nombre}</td>
            <td>${est.documento}</td>
            <td class="center-align">
                <div class="qr-actions">
                    <img
                        src="${url}"
                        width="80"
                        class="qr-preview"
                        onclick="verQR('${url}', '${est.nombre}', '${nombreGradoActual}')"
                        alt="QR de ${est.nombre}"
                        title="Click para ampliar"
                    >
                    <a href="${url}" download>
                        <i class="material-icons tiny">download</i> Descargar
                    </a>
                </div>
            </td>
            <td>${new Date().toLocaleDateString()}</td>
        `;

                tbody.appendChild(tr);
            });

            document.getElementById("resultado").style.display = "block";
            document.getElementById("btnZip").style.display = "inline-flex";
            document.querySelector("#resultado h5").innerHTML = `
        <i class="material-icons">people</i>
        Estudiantes del Grado ${nombreGradoActual}°
    `;
        }

        /* ================= ZIP ================= */
        function descargarZIP() {

            if (!gradoActual) {
                M.toast({
                    html: '<i class="material-icons left">warning</i> Seleccione un grado primero',
                    classes: 'rounded orange'
                });
                return;
            }

            window.location.href =
                `api/usuarios/descargar_zip_qr_estudiantes_grado.php?id_grado=${gradoActual}`;

            M.toast({
                html: '<i class="material-icons left">cloud_download</i> Descargando archivo ZIP...',
                classes: 'rounded blue'
            });
        }

        /* ================= BLOQUEO UI ================= */
        function bloquearInterfaz(bloquear = true) {

            const btn = document.querySelector("button[onclick='generarQR()']");
            const select = document.getElementById("gradoSelect");

            btn.disabled = bloquear;
            select.disabled = bloquear;

            M.FormSelect.init(select);

            if (bloquear) {
                document.getElementById("progressContainer").style.display = "block";
                actualizarProgreso(0);
            } else {
                document.getElementById("progressContainer").style.display = "none";
            }
        }

        /* ================= PROGRESO ================= */
        function actualizarProgreso(porcentaje) {

            const barra = document.getElementById("progressBar");
            const texto = document.getElementById("progressText");

            barra.style.width = porcentaje + "%";
            texto.textContent = porcentaje + "%";
        }

        /* ================= MODAL QR ================= */
        function verQR(url, nombre, grado) {
            const img = document.getElementById("imgVistaQR");
            const btnDescargar = document.getElementById("btnDescargarQR");
            const titulo = document.getElementById("tituloQR");
            const subtitulo = document.getElementById("subtituloQR");

            img.src = url;
            titulo.textContent = "Código QR del Estudiante";
            subtitulo.textContent = `${grado} — ${nombre}`;

            const nombreArchivo = nombre
                .trim()
                .replace(/\s+/g, '_')
                .replace(/[^a-zA-Z0-9_-]/g, '');

            btnDescargar.href = url;
            btnDescargar.download = nombreArchivo + ".png";

            const modal = M.Modal.getInstance(
                document.getElementById("modalVistaQR")
            );
            modal.open();
        }

    </script>

</body>

</html>