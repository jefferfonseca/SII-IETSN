<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tablero de Aseo</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: white;
        }

        /* HEADER */
        .header {
            text-align: center;
            padding: 20px;
            font-size: 28px;
            font-weight: bold;
        }

        /* GRID */
        .tablero {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        /* COLUMNAS */
        .columna {
            background: #1e293b;
            border-radius: 12px;
            padding: 15px;
        }

        .columna h3 {
            text-align: center;
            margin-bottom: 10px;
        }

        /* ITEMS */
        .tarea {
            background: #334155;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* COMPLETADO */
        .completado {
            background: #22c55e !important;
            color: white;
            font-weight: bold;
        }

        /* NOMBRE */
        .nombre {
            font-size: 18px;
        }

        /* CHECK */
        .check {
            font-size: 20px;
        }
    </style>

</head>

<body>

    <div class="header" id="titulo">
        ASEO DEL DÍA
    </div>
    <div id="progreso" style="text-align:center; font-size:20px;"></div>
    <div class="tablero" id="tablero"></div>

    <script>

        const params = new URLSearchParams(window.location.search);
        const ID_GRADO = params.get("grado");
        const NOMBRE_GRADO = params.get("nombre") || "";

        // 🔥 Asegurar que el DOM esté listo
        document.addEventListener("DOMContentLoaded", () => {

            // Título
            document.getElementById("titulo").innerText = `ASEO – GRADO ${NOMBRE_GRADO}`;

            cargarTablero();

            // refresco automático
            setInterval(cargarTablero, 5000);

        });

        const CONFIG = [
            "barrer",
            "ordenar_mesas",
            "ordenar_sillas",
            "vaciar_canecas",
            "trapear"
        ];

        const NOMBRES = {
            barrer: "Barrer",
            ordenar_mesas: "Mesas",
            ordenar_sillas: "Sillas",
            vaciar_canecas: "Canecas",
            trapear: "Trapear"
        };

        function cargarTablero() {

            if (!ID_GRADO) return;

            fetch(`/SII-IETSN/api/aseo/listar.php?grupo=${ID_GRADO}`)
                .then(r => r.json())
                .then(r => {

                    const cont = document.getElementById("tablero");
                    const progreso = document.getElementById("progreso");

                    cont.innerHTML = "";

                    let total = 0;
                    let completadas = 0;

                    // 🔥 CONTADOR CORRECTO
                    r.data.forEach(t => {
                        total++;
                        if (t.estado === "completado") completadas++;
                    });

                    // 🔥 TEXTO PROGRESO
                    if (total > 0 && total === completadas) {
                        progreso.innerText = "🎉 ¡Aseo terminado!";
                    } else {
                        progreso.innerText = `${completadas} / ${total} tareas completadas`;
                    }

                    // 🔥 RENDER COLUMNAS
                    CONFIG.forEach(act => {

                        const col = document.createElement("div");
                        col.className = "columna";

                        col.innerHTML = `<h3>${NOMBRES[act]}</h3>`;

                        const lista = document.createElement("div");

                        r.data
                            .filter(t => t.actividad === act)
                            .forEach(t => {

                                const item = document.createElement("div");

                                item.className = "tarea " +
                                    (t.estado === "completado" ? "completado" : "");

                                item.innerHTML = `
                        <span class="nombre">${t.nombre} ${t.apellido}</span>
                        <span class="check">${t.estado === "completado" ? "✔" : ""}</span>
                    `;

                                lista.appendChild(item);
                            });

                        col.appendChild(lista);
                        cont.appendChild(col);

                    });

                })
                .catch(err => {
                    console.error("Error cargando tablero:", err);
                });

        }

    </script>
</body>

</html>