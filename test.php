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
</head>

<body class="container">

    <h4 class="center-align">Generación de Códigos QR por Grado</h4>

    <div class="card">
        <div class="card-content">

            <div class="row">
                <div class="input-field col s12 m6">
                    <select id="grado" onchange="cargarPorGrado()">
                        <option value="" disabled selected>Seleccione grado</option>
                        <!-- Se cargan dinámicamente -->
                    </select>
                    <label>Grado</label>
                </div>

                <div class="col s12 m6" style="margin-top:25px">
                    <button class="btn blue" onclick="generarQR()">
                        Generar QR
                    </button>
                </div>
            </div>
            <div class="center-align" style="margin-top:20px">
                <button class="btn green" id="btnZip" onclick="descargarZIP()" style="display:none">
                    Descargar ZIP del grado
                </button>
            </div>

            <div class="progress" id="loader" style="display:none;">
                <div class="indeterminate"></div>
            </div>

        </div>
    </div>

    <div class="card" id="resultado" style="display:none;">
        <div class="card-content">
            <span class="card-title">Estudiantes</span>

            <table class="striped">
                <thead>
                    <tr>
                        <th>Nombre del estudiante</th>
                        <th>Documento</th>
                        <th>QR</th>
                        <th>Fecha generación</th>
                    </tr>
                </thead>
                <tbody id="tabla"></tbody>
            </table>
        </div>
    </div>
    <!-- MODAL VISTA QR -->
    <div id="modalVistaQR" class="modal">
        <div class="modal-content center-align">
            <h5>Vista previa del código QR</h5>
            <img id="imgVistaQR" src="" style="max-width:100%; max-height:80vh;">
        </div>
        <div class="modal-footer">
            <a id="btnDescargarQR" href="#" download="QR_Estudiante.png" class="btn blue">
                Descargar
            </a>
            <a href="#!" class="modal-close btn-flat">Cerrar</a>
        </div>
    </div>


    <!-- JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            M.FormSelect.init(document.querySelectorAll("select"));
            cargarGrados();
            const modalQR = document.getElementById("modalVistaQR");
            if (modalQR) {
                M.Modal.init(modalQR);
            } else {
                console.error("No se encontró el modal para vista previa de QR");
            }
        });

        function cargarGrados() {
            fetch("api/grados/listar.php")
                .then(r => r.json())
                .then(data => {
                    const select = document.getElementById("grado");
                    select.innerHTML = '<option value="" disabled selected>Seleccione grado</option>';

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

        /* ================= EVENTO AL SELECCIONAR ================= */
        function cargarPorGrado() {
            const grado = document.getElementById("grado").value;
            if (!grado) return;

            gradoActual = grado;

            document.getElementById("loader").style.display = "block";
            document.getElementById("resultado").style.display = "none";
            document.getElementById("btnZip").style.display = "none";

            // 👉 Genera si no existe (idempotente)
            fetch(`api/usuarios/generar_qr_estudiantes_grado.php?id_grado=${grado}`)
                .then(r => r.json())
                .then(r => {
                    document.getElementById("loader").style.display = "none";

                    if (!r.success) {
                        M.toast({ html: r.message });
                        return;
                    }

                    if (r.total === 0) {
                        M.toast({ html: "No hay estudiantes en este grado" });
                        return;
                    }

                    // 🔑 Derivar la ruta desde el primer QR
                    // ej: qr_estudiantes/grado_9/213213.png
                    const partes = r.data[0].qr.split('/');
                    rutaActual = partes[1]; // grado_9

                    cargarTablaEstudiantes();
                });
        }

        /* ================= LISTAR (NUEVO ENDPOINT) ================= */
        function cargarTablaEstudiantes() {
            if (!rutaActual) return;

            const tbody = document.getElementById("tabla");
            tbody.innerHTML = "";

            fetch(`api/usuarios/listar_qr_estudiantes_grado.php?ruta=${encodeURIComponent(rutaActual)}`)
                .then(res => res.json())
                .then(res => {

                    if (!res.success || res.total === 0) {
                        M.toast({ html: "No hay QR para mostrar" });
                        return;
                    }

                    res.data.forEach(item => {
                        const url = `qr_estudiantes/${rutaActual}/${item.archivo}`;

                        const tr = document.createElement("tr");
                        tr.innerHTML = `
        <td>${item.nombre}</td>
        <td>${item.documento}</td>
        <td>
            <img
                src="${url}"
                width="80"
                style="cursor:pointer"
                onclick="verQR('${url}', '${item.nombre}')"
"
            ><br>
            <a href="${url}" download>Descargar</a>
        </td>
        <td>${item.fecha}</td>
    `;
                        tbody.appendChild(tr);
                    });


                    document.getElementById("resultado").style.display = "block";
                    document.getElementById("btnZip").style.display = "inline-block";
                });
        }

        /* ================= ZIP ================= */
        function descargarZIP() {
            if (!gradoActual) {
                M.toast({ html: "Seleccione un grado primero" });
                return;
            }

            window.location.href =
                `api/usuarios/descargar_zip_qr_estudiantes_grado.php?id_grado=${gradoActual}`;
        }
        function verQR(url, nombre) {

            const img = document.getElementById("imgVistaQR");
            const btnDescargar = document.getElementById("btnDescargarQR");

            img.src = url;

            // 🧹 Limpiar nombre para archivo
            const nombreArchivo = nombre
                .trim()
                .replace(/\s+/g, ' ')
                .replace(/[^a-zA-Z0-9_-]/g, ' ');

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