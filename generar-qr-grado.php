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
        <!-- Header -->
        <div class="page-header">
            <h4>
                <i class="material-icons">qr_code_2</i>
                Generación de Códigos QR por Grado
            </h4>
        </div>

        <!-- Card Principal -->
        <div class="card">
            <div class="card-content">
                <div class="row">
                    <div class="input-field col s12 m6">
                        <select id="grado" onchange="cargarPorGrado()">
                            <option value="" disabled selected>Seleccione un grado</option>
                        </select>
                        <label>
                            <i class="material-icons tiny">school</i>
                            Grado
                        </label>
                    </div>

                    <div class="col s12 m6" style="display: flex; align-items: flex-end;">
                        <button class="btn btn-primary waves-effect waves-light" onclick="generarQR()">
                            <i class="material-icons left">add_circle</i>
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
                    <button class="btn btn-success waves-effect waves-light" id="btnZip" onclick="descargarZIP()"
                        style="display:none">
                        <i class="material-icons left">cloud_download</i>
                        Descargar ZIP del Grado
                    </button>
                </div>

                <!-- Loader -->
                <div class="progress" id="loader" style="display:none;">
                    <div class="indeterminate"></div>
                </div>
            </div>
        </div>

        <!-- Card de resultados -->
        <div class="card" id="resultado" style="display:none;">
            <div class="card-content">
                <span class="card-title">
                    <i class="material-icons">people</i>
                    Estudiantes del Grado
                </span>

                <table class="striped highlight responsive-table">
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
                style="max-width:100%; max-height:60vh; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.15);">
        </div>
        <div class="modal-footer">
            <a id="btnDescargarQR" href="#" download class="btn btn-download waves-effect waves-light">
                <i class="material-icons left">download</i>
                Descargar QR
            </a>
            <a href="#!" class="modal-close btn-flat waves-effect">Cerrar</a>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Inicializar selects
            M.FormSelect.init(document.querySelectorAll("select"));

            // Inicializar modal QR
            const modalQR = document.getElementById("modalVistaQR");
            if (modalQR) {
                M.Modal.init(modalQR);
            } else {
                console.error("No se encontró el modal para vista previa de QR");
            }

            cargarGrados();
        });

        /* ================= CARGAR GRADOS ================= */
        function cargarGrados() {
            fetch("api/grados/listar.php")
                .then(r => r.json())
                .then(data => {
                    const select = document.getElementById("grado");

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

        /* ================= ESTADO ÚNICO ================= */
        let gradoActual = null;
        let rutaActual = null;
        let nombreGradoActual = null;

        /* ================= GENERAR QR ================= */
        function generarQR() {
            cargarPorGrado();
        }

        /* ================= EVENTO AL SELECCIONAR ================= */
        function cargarPorGrado() {
            const select = document.getElementById("grado");
            const grado = select.value;

            if (!grado) {
                M.toast({ html: '<i class="material-icons left">warning</i> Por favor seleccione un grado', classes: 'rounded' });
                return;
            }

            gradoActual = grado;
            nombreGradoActual = select.options[select.selectedIndex].text;

            document.getElementById("loader").style.display = "block";
            document.getElementById("resultado").style.display = "none";
            document.getElementById("btnZip").style.display = "none";
            document.getElementById("badgeQR").style.display = "none";

            fetch(`api/usuarios/generar_qr_estudiantes_grado.php?id_grado=${grado}`)
                .then(r => r.json())
                .then(r => {
                    document.getElementById("loader").style.display = "none";

                    if (!r.success) {
                        M.toast({ html: `<i class="material-icons left">error</i> ${r.message}`, classes: 'rounded red' });
                        return;
                    }

                    if (r.total === 0) {
                        M.toast({ html: '<i class="material-icons left">info</i> No hay estudiantes en este grado', classes: 'rounded orange' });
                        return;
                    }

                    // Mostrar badges
                    const badgeBox = document.getElementById("badgeQR");
                    const badgeExist = document.getElementById("badgeExistentes");
                    const badgeNew = document.getElementById("badgeNuevos");

                    badgeExist.textContent = r.existentes;
                    badgeNew.textContent = r.nuevos;
                    badgeBox.style.display = "flex";

                    // Derivar ruta desde el primer QR
                    const partes = r.data[0].qr.split('/');
                    rutaActual = partes[1];

                    M.toast({ html: `<i class="material-icons left">check_circle</i> ${r.total} códigos QR generados exitosamente`, classes: 'rounded green' });

                    cargarTablaEstudiantes();
                });
        }

        /* ================= LISTAR ================= */
        function cargarTablaEstudiantes() {
            if (!rutaActual) return;

            const tbody = document.getElementById("tabla");
            tbody.innerHTML = "";

            fetch(`api/usuarios/listar_qr_estudiantes_grado.php?ruta=${encodeURIComponent(rutaActual)}`)
                .then(res => res.json())
                .then(res => {
                    if (!res.success || res.total === 0) {
                        M.toast({ html: '<i class="material-icons left">warning</i> No hay códigos QR para mostrar', classes: 'rounded orange' });
                        return;
                    }

                    res.data.forEach(item => {
                        const url = `qr_estudiantes/${rutaActual}/${item.archivo}`;

                        const tr = document.createElement("tr");
                        tr.innerHTML = `
                            <td style="font-weight: 500;">${item.nombre}</td>
                            <td>${item.documento}</td>
                            <td class="center-align">
                                <div class="qr-actions">
                                    <img
                                        src="${url}"
                                        width="80"
                                        class="qr-preview"
                                        onclick="verQR('${url}', '${item.nombre}', '${nombreGradoActual}')"
                                        alt="QR de ${item.nombre}"
                                    >
                                    <a href="${url}" download>
                                        <i class="material-icons tiny">download</i> Descargar
                                    </a>
                                </div>
                            </td>
                            <td>${item.fecha}</td>
                        `;
                        tbody.appendChild(tr);
                    });

                    document.getElementById("resultado").style.display = "block";
                    document.getElementById("btnZip").style.display = "inline-flex";
                });
        }

        /* ================= ZIP ================= */
        function descargarZIP() {
            if (!gradoActual) {
                M.toast({ html: '<i class="material-icons left">warning</i> Seleccione un grado primero', classes: 'rounded orange' });
                return;
            }

            M.toast({ html: '<i class="material-icons left">cloud_download</i> Descargando archivo ZIP...', classes: 'rounded blue' });

            window.location.href = `api/usuarios/descargar_zip_qr_estudiantes_grado.php?id_grado=${gradoActual}`;
        }

        /* ================= MODAL QR ================= */
        function verQR(url, nombre, grado) {
            const img = document.getElementById("imgVistaQR");
            const btnDescargar = document.getElementById("btnDescargarQR");
            const titulo = document.getElementById("tituloQR");
            const subtitulo = document.getElementById("subtituloQR");

            img.src = url;
            titulo.textContent = "Código QR del Estudiante";
            subtitulo.textContent = `${grado} – ${nombre}`;

            // Limpiar nombre para archivo
            const nombreArchivo = nombre
                .trim()
                .replace(/\s+/g, ' ')
                .replace(/[^a-zA-Z0-9_-]/g, ' ');

            btnDescargar.href = url;
            btnDescargar.download = nombreArchivo + ".png";

            const modal = M.Modal.getInstance(document.getElementById("modalVistaQR"));
            modal.open();
        }
    </script>
</body>

</html>