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

        <div class="tareas-container" id="contenedor"></div>
        <div class="asistencia-container">

            <div class="asistencia-header">
                <h5>Asistencia del grupo</h5>
                <span id="contadorAsistencia"></span>
            </div>

            <div id="listaAsistencia" class="asistencia-lista"></div>

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

            cargarGrupos();

            document.getElementById("grupo").addEventListener("change", () => {
                cargarAsistencia();
                cargarAseo();
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

                    const cont = document.getElementById("listaAsistencia");
                    const contador = document.getElementById("contadorAsistencia");

                    cont.innerHTML = "";

                    let presentes = 0;

                    r.data.forEach(est => {

                        let estadoHTML = "";
                        let metodo = est.metodo;

                        if (metodo === "prestamo") {
                            estadoHTML = `<span class="badge-presente">✔ Préstamo</span>`;
                            presentes++;
                        }
                        else if (metodo === "manual") {
                            estadoHTML = `<span class="badge-presente badge-manual">✔ Manual</span>`;
                            presentes++;
                        }
                        else {
                            estadoHTML = `
          <button class="btn-asistencia" onclick="marcarAsistencia(${est.id_usuario})">
            Marcar
          </button>
        `;
                        }

                        cont.innerHTML += `
        <div class="asistencia-item">
          <span>${est.nombre} ${est.apellido}</span>
          ${estadoHTML}
        </div>
      `;
                    });

                    contador.innerHTML = `${presentes} presentes`;

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

                        const item = document.createElement("div");
                        item.className = "tarea-item";

                        // 🔥 CLAVE: esto arregla el bug
                        item.dataset.id = t.id;

                        item.innerHTML = `
          <span>${t.nombre} ${t.apellido}</span>
          <div class="btn-check" onclick="completar(${t.id})">✔</div>
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
                .then(() => cargarAseo());
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

    </script>

</body>

</html>