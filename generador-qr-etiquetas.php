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
    <link rel="stylesheet" href="/SII-IETSN/css/qr.css">
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
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="material-icons">menu</i>
            </button>

            <div class="page-title">
                <div class="page-title-icon">
                    <i class="material-icons">qr_code_2</i>
                </div>
                <div>
                    <h4>Generador de Etiquetas QR</h4>
                    <p>Generación masiva por categoría</p>
                </div>
            </div>
        </div>

        <!-- ================= GENERADOR ================= -->
        <div class="qr-container qr-stack">

            <!-- Selección -->
            <div class="qr-card qr-full" style="width:100%">
                <h5>
                    <i class="material-icons">category</i>
                    Seleccione una categoría
                </h5>

                <div class="row">
                    <div class="input-field col s12 m6">
                        <select id="categoriaSelect">
                            <option value="" disabled selected>Seleccione categoría</option>
                        </select>
                        <label>Categoría</label>
                    </div>

                    <div class="input-field col s12 m6" style="margin-top:25px">
                        <button class="btn btn-accion waves-effect" onclick="generarEtiquetas()">
                            <i class="material-icons left">qr_code</i>
                            Generar Etiquetas
                        </button>
                    </div>
                    <div id="progressBox" style="display:none;margin-top:20px">
                        <div class="progress">
                            <div class="determinate" id="progressBar" style="width:0%"></div>
                        </div>
                        <span id="progressText"></span>
                    </div>

                </div>
            </div>

            <!-- Resultados -->
            <div class="qr-card qr-full" id="resultado" style="width:100%; display:none">
                <p id="contadorEtiquetas" style="font-weight:600; margin-bottom:10px"></p>
                <h5>
                    <i class="material-icons">list</i>
                    Etiquetas generadas
                </h5>
                <div style="display:flex; justify-content:flex-end; margin-bottom:15px">
                    <div class="row">
                        <div class="col s6">
                            <button class="btn btn-accion waves-effect" id="btnZip" onclick="descargarZip()" disabled>
                                <i class="material-icons left">archive</i>
                                Descargar todo (.zip)
                            </button>
                        </div>
                        <div class="col s6">
                            <button class="btn  btn-accion waves-effect red" onclick="eliminarCarpeta()">
                                <i class="material-icons left">delete</i>
                                Eliminar carpeta
                            </button>
                        </div>
                    </div>
                </div>

                <table class="highlight responsive-table">
                    <thead>
                        <tr>
                            <th>Archivo</th>
                            <th>Vista previa</th>
                            <th>Fecha</th>
                            <th>Abrir</th>
                        </tr>
                    </thead>
                    <tbody id="tablaEtiquetas"></tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- ================= SCRIPTS ================= -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <script>
        let ruta = null;
        let rutaActual = null;

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('btnZip').disabled = true;
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

            rutaActual = `etiquetas_generadas/${carpeta}`;

            // 🔹 SOLO leer carpeta
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
                M.toast({ html: 'Seleccione una categoría', classes: 'red rounded' });
                return;
            }

            // Mostrar progress
            document.getElementById('progressBox').style.display = 'block';
            document.getElementById('progressBar').style.width = '0%';
            document.getElementById('progressText').textContent = 'Iniciando...';

            // 👉 ARRANCAR POLLING ANTES
            iniciarPollingProgreso(() => {
                cargarTablaEtiquetas(rutaActual);
                descargarZip(); // ZIP automático
            });

            // 👉 LUEGO disparar backend pesado
            fetch(`/SII-IETSN/api/elementos/generar_etiquetas_categoria.php?id_categoria=${id}`)
                .then(r => r.json())
                .then(r => {
                    if (!r.success) {
                        M.toast({ html: r.message, classes: 'red rounded' });
                        document.getElementById('progressBox').style.display = 'none';
                        return;
                    }

                    rutaActual = r.data.ruta;
                });
        }


        // ================= TABLA =================
        function cargarTablaEtiquetas(ruta) {
            const tbody = document.getElementById('tablaEtiquetas');
            const contenedor = document.getElementById('resultado');
            const contador = document.getElementById('contadorEtiquetas');
            const btnZip = document.getElementById('btnZip');

            // Reset visual
            tbody.innerHTML = '';
            contenedor.style.display = 'none';
            contador.textContent = '';
            btnZip.disabled = true;

            fetch(`/SII-IETSN/api/elementos/listar_etiquetas.php?ruta=${encodeURIComponent(ruta)}`)
                .then(r => r.json())
                .then(r => {

                    // Validación básica
                    if (!r.success) {
                        M.toast({ html: r.message || 'No se pudo leer la carpeta', classes: 'red rounded' });
                        return;
                    }

                    // Sin archivos
                    if (r.total === 0) {
                        M.toast({ html: 'No hay elementos en esta categoría', classes: 'orange rounded' });
                        return;
                    }

                    // Mostrar resultados
                    contenedor.style.display = 'block';
                    btnZip.disabled = false;
                    contador.textContent = `Total de etiquetas: ${r.total}`;

                    r.data.forEach(item => {
                        const url = `/SII-IETSN/${ruta}/${item.archivo}`;

                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                    <td>${item.archivo}</td>
                    <td>
                        <img src="${url}" style="width:120px;border:1px solid #ccc">
                    </td>
                    <td>${item.fecha}</td>
                    <td>
                        <a href="${url}" target="_blank" class="btn-small">
                            <i class="material-icons">open_in_new</i>
                        </a>
                    </td>
                `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(() => {
                    M.toast({ html: 'Error al cargar etiquetas', classes: 'red rounded' });
                });
        }


        function eliminarCarpeta() {
            if (!rutaActual) return;

            if (!confirm('¿Eliminar todas las etiquetas de esta categoría?')) return;

            fetch('/SII-IETSN/api/elementos/eliminar_etiquetas.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ruta: rutaActual })
            })
                .then(r => r.json())
                .then(r => {
                    if (!r.success) {
                        M.toast({ html: r.message, classes: 'red rounded' });
                        return;
                    }

                    document.getElementById('resultado').style.display = 'none';
                    document.getElementById('btnZip').disabled = true;
                    M.toast({ html: 'Carpeta eliminada', classes: 'green rounded' });
                    cargarTablaEtiquetas(rutaActual);
                });
        }

        function iniciarPollingProgreso(onFinish) {
            const interval = setInterval(() => {
                fetch('/SII-IETSN/api/elementos/progreso_etiquetas.php')
                    .then(r => r.json())
                    .then(p => {

                        // 🔑 NO bloquear el estado final
                        if (!p.activo && !p.completado) return;

                        if (p.total > 0) {
                            const porcentaje = Math.round((p.actual / p.total) * 100);
                            document.getElementById('progressBar').style.width = porcentaje + '%';
                            document.getElementById('progressText')
                                .textContent = `Generando ${p.actual} de ${p.total}`;
                        }

                        if (p.completado) {
                            clearInterval(interval);

                            document.getElementById('progressText').textContent = 'Completado ✔';
                            document.getElementById('progressBox').style.display = 'none';

                            if (typeof onFinish === 'function') {
                                onFinish(); // 👈 AQUÍ se carga la tabla
                            }
                        }
                    });
            }, 500);
        }
        function descargarZip() {
            if (!rutaActual) {
                M.toast({ html: 'No hay etiquetas para descargar', classes: 'orange rounded' });
                return;
            }

            const url = `/SII-IETSN/api/elementos/descargar_zip_etiquetas.php?ruta=${encodeURIComponent(rutaActual)}`;
            window.location.href = url;
        }

    </script>

<script>
const menuElementos = document.getElementById('menu-elementos');
const submenuElementos = document.getElementById('submenu-elementos');

menuElementos.addEventListener('click', () => {
  menuElementos.classList.toggle('open');
  submenuElementos.classList.toggle('open');
});
</script>
</body>
</html>