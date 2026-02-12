<?php
session_start();

// 🔐 Validar sesión
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
    <title>Generador de Etiquetas QR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Materialize -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Estilos del sistema -->
    <link rel="stylesheet" href="/SII-IETSN/css/sidebar.css">
    <link rel="stylesheet" href="/SII-IETSN/css/qr-elementos.css">
</head>

<body>

    <!-- ================= SIDEBAR ================= -->
    <?php
    include 'sidebar.php';
    ?>

    <!-- ================= MAIN CONTENT ================= -->
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
                        <h4>Generador de Etiquetas QR</h4>
                        <p>Generación masiva por categoría</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= GENERADOR ================= -->
        <div class="qr-container qr-stack">

            <!-- Selección -->
            <div class="qr-card qr-full" style="width:100%; position: relative; overflow: hidden;">
                <h5>
                    <i class="material-icons">category</i>
                    Seleccione una categoría
                </h5>

                <div class="row" style="margin-bottom: 0;">
                    <div class="input-field col s12 m6">
                        <select id="categoriaSelect">
                            <option value="" disabled selected>Seleccione categoría</option>
                        </select>
                        <label>Categoría</label>
                    </div>

                    <div class="input-field col s12 m6">
                        <button class="btn btn-accion waves-effect waves-light" onclick="generarEtiquetas()">
                            <i class="material-icons left">qr_code</i>
                            Generar
                        </button>
                    </div>
                </div>

                <!-- Progress Box -->
                <div id="progressBox" style="display:none;">
                    <div class="progress">
                        <div class="determinate" id="progressBar" style="width:0%"></div>
                    </div>
                    <span id="progressText"></span>
                </div>

            </div>

            <!-- Resultados -->
            <div class="qr-card qr-full" id="resultado"
                style="width:100%; display:none; position: relative; overflow: hidden;">
                <p id="contadorEtiquetas"></p>

                <h5>
                    <i class="material-icons">view_list</i>
                    Etiquetas generadas
                </h5>

                <div class="download-section">
                    <button class="btn btn-accion waves-effect waves-light" id="btnZip" onclick="descargarZip()"
                        disabled>
                        <i class="material-icons left">archive</i>
                        Descargar todo (.zip)
                    </button>

                    <button class="btn btn-accion red waves-effect waves-light" onclick="eliminarCarpeta()">
                        <i class="material-icons left">delete_forever</i>
                        Eliminar carpeta
                    </button>
                </div>

                <table class="highlight responsive-table">
                    <thead>
                        <tr>
                            <th>Archivo</th>
                            <th>Vista previa</th>
                            <th>Fecha de creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaEtiquetas"></tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- MODAL VISTA PREVIA -->
    <div id="modalVistaEtiqueta" class="modal">
        <div class="modal-content center-align">
            <img id="imgVistaEtiqueta" src="" style="max-width:100%; max-height:70vh">
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close btn-flat waves-effect waves-light">
                <i class="material-icons left">close</i>
                Cerrar
            </a>
        </div>
    </div>

    <!-- ================= SCRIPTS ================= -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <script>
        let ruta = null;
        let rutaActual = null;

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('btnZip').disabled = true;
            const modal = document.getElementById('modalVistaEtiqueta');
            if (modal) M.Modal.init(modal);
        });

        document.getElementById('categoriaSelect').addEventListener('change', () => {
            const select = document.getElementById('categoriaSelect');
            if (!select.value) return;

            const nombreCategoria = select.options[select.selectedIndex].text;

            const tbody = document.getElementById('tablaEtiquetas');
            const contenedor = document.getElementById('resultado');
            const btnZip = document.getElementById('btnZip');

            tbody.innerHTML = '';
            contenedor.style.display = 'none';
            btnZip.disabled = true;

            const carpeta = 'QR-' + nombreCategoria
                .replace(/\s+/g, '_')
                .replace(/[^a-zA-Z0-9_-]/g, '');

            rutaActual = carpeta;

            cargarTablaEtiquetas(rutaActual);
        });

        // Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('mainContent');
            sidebar.classList.toggle('hidden');
            main.classList.toggle('expanded');
        }

        // Logout
        function cerrarSesion() {
            fetch('/SII-IETSN/api/auth/logout.php', { method: 'POST' })
                .then(() => window.location.href = 'index.html');
        }

        // ================= CARGAR CATEGORÍAS =================
        document.addEventListener('DOMContentLoaded', () => {
            fetch('/SII-IETSN/api/elementos/categorias.php')
                .then(r => r.json())
                .then(r => {
                    const select = document.getElementById('categoriaSelect');

                    r.data.forEach(cat => {
                        const opt = document.createElement('option');
                        opt.value = cat.id_categoria;
                        opt.textContent = cat.nombre;
                        select.appendChild(opt);
                    });

                    M.FormSelect.init(select);
                });
        });

        // ================= GENERAR =================
        function generarEtiquetas() {
            const id = document.getElementById('categoriaSelect').value;
            if (!id) {
                M.toast({ html: '⚠️ Seleccione una categoría', classes: 'orange rounded' });
                return;
            }

            document.getElementById('progressBox').style.display = 'block';
            document.getElementById('progressBar').style.width = '0%';
            document.getElementById('progressText').textContent = 'Iniciando generación...';

            iniciarPollingProgreso(() => {
                cargarTablaEtiquetas(rutaActual);
                descargarZip();
            });

            fetch(`/SII-IETSN/api/elementos/generar_etiquetas_categoria.php?id_categoria=${id}`)
                .then(r => r.json())
                .then(r => {
                    if (!r.success) {
                        M.toast({ html: '❌ ' + r.message, classes: 'red rounded' });
                        document.getElementById('progressBox').style.display = 'none';
                        return;
                    }
                });
        }

        // ================= TABLA =================
        function cargarTablaEtiquetas(ruta) {
            const tbody = document.getElementById('tablaEtiquetas');
            const contenedor = document.getElementById('resultado');
            const contador = document.getElementById('contadorEtiquetas');
            const btnZip = document.getElementById('btnZip');

            tbody.innerHTML = '';
            contenedor.style.display = 'none';
            contador.textContent = '';
            btnZip.disabled = true;

            if (!ruta) {
                M.toast({ html: '⚠️ Ruta inválida', classes: 'orange rounded' });
                return;
            }

            fetch(`/SII-IETSN/api/elementos/listar_etiquetas.php?ruta=${encodeURIComponent(ruta)}`)
                .then(res => res.json())
                .then(res => {
                    if (!res.success) {
                        M.toast({
                            html: '❌ ' + (res.message || 'No se pudo leer la carpeta'),
                            classes: 'red rounded'
                        });
                        return;
                    }

                    if (!Array.isArray(res.data) || res.data.length === 0) {
                        M.toast({
                            html: 'ℹ️ No hay etiquetas en esta categoría',
                            classes: 'blue rounded'
                        });
                        return;
                    }

                    contenedor.style.display = 'block';
                    btnZip.disabled = false;
                    contador.textContent = `${res.total} etiquetas generadas`;

                    res.data.forEach(item => {
                        const url = `/SII-IETSN/etiquetas_generadas/${ruta}/${item.archivo}`;
                        const tr = document.createElement('tr');

                        tr.innerHTML = `
                            <td><strong>${item.archivo}</strong></td>
                            <td class="center-align">
                                <img
                                    src="${url}"
                                    alt="${item.archivo}"
                                    style="width:120px; cursor:pointer;"
                                    title="Click para ampliar"
                                    onclick="verEtiqueta('${url}')"
                                >
                            </td>
                            <td>${item.fecha}</td>
                            <td class="center-align">
                                <button
                                    class="btn-small btn-accion waves-effect waves-light"
                                    title="Descargar etiqueta"
                                    onclick="descargarEtiqueta('${url}', '${item.archivo}')"
                                >
                                    <i class="material-icons">file_download</i>
                                </button>
                            </td>
                        `;

                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    console.error(err);
                    M.toast({
                        html: '❌ Error al cargar etiquetas',
                        classes: 'red rounded'
                    });
                });
        }

        function eliminarCarpeta() {
            if (!rutaActual) return;

            if (!confirm('⚠️ ¿Está seguro de eliminar todas las etiquetas de esta categoría?\n\nEsta acción no se puede deshacer.')) return;

            fetch('/SII-IETSN/api/elementos/eliminar_etiquetas.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ruta: rutaActual })
            })
                .then(r => r.json())
                .then(r => {
                    if (!r.success) {
                        M.toast({ html: '❌ ' + r.message, classes: 'red rounded' });
                        return;
                    }

                    document.getElementById('resultado').style.display = 'none';
                    document.getElementById('btnZip').disabled = true;
                    M.toast({ html: '✅ Carpeta eliminada correctamente', classes: 'green rounded' });
                    cargarTablaEtiquetas(rutaActual);
                });
        }

        function iniciarPollingProgreso(onFinish) {
            const interval = setInterval(() => {
                fetch('/SII-IETSN/api/elementos/progreso_etiquetas.php')
                    .then(r => r.json())
                    .then(p => {
                        if (!p.activo && !p.completado) return;

                        if (p.total > 0) {
                            const porcentaje = Math.round((p.actual / p.total) * 100);
                            document.getElementById('progressBar').style.width = porcentaje + '%';
                            document.getElementById('progressText')
                                .textContent = `Generando etiqueta ${p.actual} de ${p.total} (${porcentaje}%)`;
                        }

                        if (p.completado) {
                            clearInterval(interval);
                            document.getElementById('progressText').textContent = '✅ Generación completada';

                            setTimeout(() => {
                                document.getElementById('progressBox').style.display = 'none';
                            }, 2000);

                            if (typeof onFinish === 'function') {
                                onFinish();
                            }
                        }
                    });
            }, 500);
        }

        function descargarZip() {
            if (!rutaActual) {
                M.toast({ html: '⚠️ No hay etiquetas para descargar', classes: 'orange rounded' });
                return;
            }

            const url = `/SII-IETSN/api/elementos/descargar_zip_etiquetas.php?ruta=${encodeURIComponent(rutaActual)}`;
            window.location.href = url;
            M.toast({ html: '⬇️ Descargando archivo ZIP...', classes: 'blue rounded' });
        }

        function verEtiqueta(url) {
            const img = document.getElementById('imgVistaEtiqueta');
            img.src = url;

            const modal = M.Modal.getInstance(
                document.getElementById('modalVistaEtiqueta')
            );
            modal.open();
        }

        function descargarEtiqueta(url, nombreArchivo) {
            const a = document.createElement('a');
            a.href = url;
            a.download = nombreArchivo;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            M.toast({ html: '⬇️ Descargando ' + nombreArchivo, classes: 'blue rounded' });
        }

    </script>
</body>

</html>