<?php
session_start();

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
    <title>Gestión de Usuarios</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/SII-IETSN/css/usuario.css">
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
                        <i class="material-icons">people</i>
                    </div>
                    <div>
                        <h4>Gestión de Usuarios</h4>
                        <p>Administra los usuarios del sistema</p>
                    </div>
                </div>
            </div>

            <!-- FILTROS -->
            <div class="usuarios-filtros">
                <div class="input-field">
                    <select id="filtroRol">
                        <option value="" selected>Todos</option>
                        <option value="Admin">Admin</option>
                        <option value="Docente">Docente</option>
                        <option value="Estudiante">Estudiante</option>
                    </select>
                    <label>Rol</label>
                </div>

                <div class="input-field" id="filtroGradoContainer" style="display:none;">
                    <select id="filtroGrado">
                        <option value="" selected>Todos los grados</option>
                    </select>
                    <label>Grado</label>
                </div>

                <div class="input-field">
                    <input id="busquedaUsuario" type="text">
                    <label>Buscar</label>
                </div>
            </div>

            <a href="#modalNuevoUsuario" class="btn btn-nuevo-usuario modal-trigger">
                <i class="material-icons left">person_add</i>
                Nuevo Usuario
            </a>
        </div>

        <!-- CONTENEDOR -->
        <div class="usuarios-container" id="usuariosContainer"></div>
    </div>

    <!-- ================= MODAL NUEVO USUARIO ================= -->
    <div id="modalNuevoUsuario" class="modal">
        <div class="modal-content">

            <div class="modal-header">
                <div class="modal-icon"><i class="material-icons">person_add</i></div>
                <div class="modal-title">
                    <h5>Nuevo Usuario</h5>
                    <p>El documento se usará para generar el código QR</p>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <input id="nuevo_documento" type="text">
                    <label>Documento (no editable luego)</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12 m6">
                    <input id="nuevo_nombre" type="text">
                    <label>Nombre</label>
                </div>

                <div class="input-field col s12 m6">
                    <input id="nuevo_apellido" type="text">
                    <label>Apellido</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <select id="nuevo_rol">
                        <option value="" disabled selected>Seleccione rol</option>
                        <option value="Admin">Admin</option>
                        <option value="Docente">Docente</option>
                        <option value="Estudiante">Estudiante</option>
                    </select>
                    <label>Rol</label>
                </div>
            </div>

            <div class="row" id="grupo-grado-nuevo" style="display:none;">
                <div class="input-field col s12">
                    <select id="nuevo_id_grado"></select>
                    <label>Grado</label>
                </div>
            </div>

            <div class="switch-container">
                <div class="switch">
                    <label>
                        Inactivo
                        <input type="checkbox" id="nuevo_estado" checked>
                        <span class="lever"></span>
                        Activo
                    </label>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <a class="modal-close btn btn-cancelar">Cancelar</a>
            <a class="btn btn-guardar" onclick="guardarNuevoUsuario()">
                <i class="material-icons left">save</i>Guardar
            </a>
        </div>
    </div>

    <!-- ================= MODAL EDITAR USUARIO ================= -->
    <div id="modalEditarUsuario" class="modal">
        <div class="modal-content">

            <div class="modal-header">
                <div class="modal-icon"><i class="material-icons">edit</i></div>
                <div class="modal-title">
                    <h5>Editar Usuario</h5>
                    <p>El código QR identifica al usuario</p>
                </div>
            </div>

            <!-- HASH -->
            <div class="row">
                <div class="input-field col s12">
                    <input id="editar_doc_hash" type="text" readonly>
                    <label class="active">Código QR (hash)</label>
                </div>
            </div>

            <!-- DOCUMENTO BLOQUEADO -->
            <div class="row">
                <div class="input-field col s12">
                    <input id="editar_documento" type="text" readonly>
                    <label class="active">Documento (solo lectura)</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12 m6">
                    <input id="editar_nombre" type="text">
                    <label>Nombre</label>
                </div>

                <div class="input-field col s12 m6">
                    <input id="editar_apellido" type="text">
                    <label>Apellido</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <select id="editar_rol"></select>
                    <label>Rol</label>
                </div>
            </div>

            <div class="row" id="grupo-grado-editar" style="display:none;">
                <div class="input-field col s12">
                    <select id="editar_id_grado"></select>
                    <label>Grado</label>
                </div>
            </div>

            <div class="switch-container">
                <div class="switch">
                    <label>
                        Inactivo
                        <input type="checkbox" id="editar_estado">
                        <span class="lever"></span>
                        Activo
                    </label>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <a class="modal-close btn btn-cancelar">Cancelar</a>
            <a class="btn btn-guardar" onclick="guardarEdicionUsuario()">
                <i class="material-icons left">save</i>Guardar
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <script>
        /* ===============================
           SIDEBAR
        =============================== */
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

        /* ===============================
           CACHE Y CONSTANTES
        =============================== */
        let usuariosCache = {};
        let debounceTimer = null;
        let GRADOS = [];

        const ROLES = [
            { value: "Admin", label: "Administrador" },
            { value: "Docente", label: "Docente" },
            { value: "Estudiante", label: "Estudiante" }
        ];

        /* ===============================
           INIT
        =============================== */
        document.addEventListener('DOMContentLoaded', () => {
            M.Modal.init(document.querySelectorAll('.modal'));
            M.FormSelect.init(document.querySelectorAll('select'));
            M.updateTextFields();

            cargarRolesSelect("nuevo_rol");
            cargarRolesSelect("editar_rol");
            cargarGrados();
            cargarUsuarios();

            manejarRol("nuevo_rol", "grupo-grado-nuevo");
            manejarRol("editar_rol", "grupo-grado-editar");

            document.getElementById("filtroRol").addEventListener("change", onFiltroChange);
            document.getElementById("filtroGrado").addEventListener("change", cargarUsuarios);
            document.getElementById("busquedaUsuario").addEventListener("input", onBuscarInput);
        });

        /* ===============================
           ROLES
        =============================== */
        function cargarRolesSelect(selectId) {
            const select = document.getElementById(selectId);
            if (!select) return;

            select.innerHTML = '<option value="" disabled selected>Seleccione un rol</option>';

            ROLES.forEach(rol => {
                const option = document.createElement("option");
                option.value = rol.value;
                option.textContent = rol.label;
                select.appendChild(option);
            });

            M.FormSelect.init(select);
        }

        /* ===============================
           LISTAR USUARIOS (BADGES OK)
        =============================== */
        function cargarUsuarios() {
            const rol = document.getElementById("filtroRol").value;
            const buscar = document.getElementById("busquedaUsuario").value.trim();
            const id_grado = document.getElementById("filtroGrado").value;

            const params = new URLSearchParams({ limit: 25 });
            if (rol) params.append("rol", rol);
            if (buscar) params.append("buscar", buscar);
            if (rol === "Estudiante" && id_grado) params.append("id_grado", id_grado);

            fetch("/SII-IETSN/api/usuarios/listar.php?" + params.toString(), {
                credentials: "same-origin"
            })
                .then(res => res.json())
                .then(res => {
                    if (!res.success) {
                        M.toast({ html: "Error cargando usuarios", classes: "red" });
                        return;
                    }

                    const contenedor = document.getElementById("usuariosContainer");
                    contenedor.innerHTML = "";
                    usuariosCache = {};

                    res.data.forEach(usuario => {
                        usuariosCache[usuario.id_usuario] = usuario;

                        const inicial = usuario.nombre.charAt(0).toUpperCase();
                        const activo = usuario.activo == 1;

                        const estadoClase = activo ? "badge-activo" : "badge-inactivo";
                        const estadoTexto = activo ? "Activo" : "Inactivo";
                        const estadoIcono = activo ? "check_circle" : "cancel";

                        const accionTexto = activo ? "Desactivar" : "Activar";
                        const accionIcono = activo ? "lock" : "lock_open";
                        const accionClase = activo ? "" : "activar";

                        contenedor.innerHTML += `
            <div class="usuario-card">
                <div class="usuario-header">
                    <div class="usuario-info">
                        <div class="usuario-avatar">${inicial}</div>
                        <h3 class="usuario-nombre">${usuario.nombre} ${usuario.apellido}</h3>

                        <div class="usuario-documento">
                            <i class="material-icons">badge</i>
                            ${usuario.documento}
                        </div>

                        ${usuario.grado ? `
                        <div class="usuario-grado">
                            <i class="material-icons">school</i>
                            ${usuario.grado}
                        </div>` : ``}
                    </div>

                    <div class="usuario-badges">
                        <span class="badge-rol ${usuario.rol.toLowerCase()}">
                            <i class="material-icons">admin_panel_settings</i>
                            ${usuario.rol}
                        </span>

                        <span class="badge-estado ${estadoClase}">
                            <i class="material-icons">${estadoIcono}</i>
                            ${estadoTexto}
                        </span>
                    </div>
                </div>

                <div class="usuario-acciones">
                    <a href="#modalEditarUsuario"
                       class="btn btn-accion btn-editar modal-trigger"
                       onclick="cargarUsuario(${usuario.id_usuario})">
                        <i class="material-icons">edit</i>
                        Editar
                    </a>

                    <a href="#"
                       class="btn btn-accion btn-toggle ${accionClase}"
                       onclick="toggleEstado(${usuario.id_usuario})">
                        <i class="material-icons">${accionIcono}</i>
                        ${accionTexto}
                    </a>

                    ${usuario.doc_hash ? `
                    <a href="/SII-IETSN/qr.php?hash=${usuario.doc_hash}"
                    class="btn btn-accion teal">
                    <i class="material-icons">qr_code_2</i> QR
                    </a>` : ''}

                </div>
            </div>`;
                    });
                });
        }

        /* ===============================
           CARGAR USUARIO EN MODAL
        =============================== */
        function cargarUsuario(id) {
            const usuario = usuariosCache[id];
            if (!usuario) return;

            const docInput = document.getElementById('editar_documento');
            docInput.value = usuario.documento;
            docInput.dataset.id = usuario.id_usuario;

            document.getElementById('editar_nombre').value = usuario.nombre;
            document.getElementById('editar_apellido').value = usuario.apellido;
            document.getElementById('editar_rol').value = usuario.rol;
            document.getElementById('editar_estado').checked = usuario.activo == 1;

            if (usuario.rol === "Estudiante") {
                document.getElementById('grupo-grado-editar').style.display = "block";
                document.getElementById('editar_id_grado').value = usuario.id_grado;
            }

            M.updateTextFields();
            M.FormSelect.init(document.getElementById('editar_rol'));
            M.FormSelect.init(document.getElementById('editar_id_grado'));
        }

        /* ===============================
           FILTROS
        =============================== */
        function onFiltroChange() {
            const rol = document.getElementById("filtroRol").value;
            const contGrado = document.getElementById("filtroGradoContainer");

            if (rol === "Estudiante") {
                contGrado.style.display = "block";
            } else {
                contGrado.style.display = "none";
                document.getElementById("filtroGrado").value = "";
            }

            cargarUsuarios();
        }

        function onBuscarInput() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(cargarUsuarios, 350);
        }

        /* ===============================
           GRADOS
        =============================== */
        function cargarGrados() {
            fetch("/SII-IETSN/api/grados/listar.php", {
                credentials: "same-origin"
            })
                .then(res => res.json())
                .then(res => {
                    if (!res.success) return;

                    GRADOS = res.data;

                    ["filtroGrado", "nuevo_id_grado", "editar_id_grado"].forEach(id => {
                        const select = document.getElementById(id);
                        if (!select) return;

                        select.innerHTML = id === "filtroGrado"
                            ? '<option value="">Todos los grados</option>'
                            : '<option value="" disabled selected>Seleccione grado</option>';

                        GRADOS.forEach(g => {
                            const opt = document.createElement("option");
                            opt.value = g.id_grado;
                            opt.textContent = g.nombre;
                            select.appendChild(opt);
                        });

                        M.FormSelect.init(select);
                    });
                });
        }

        function manejarRol(selectRolId, grupoGradoId) {
            const rolSelect = document.getElementById(selectRolId);
            const grupoGrado = document.getElementById(grupoGradoId);

            rolSelect.addEventListener("change", () => {
                grupoGrado.style.display = rolSelect.value === "Estudiante" ? "block" : "none";
            });
        }

        /* ===============================
           CRUD
        =============================== */
        function guardarNuevoUsuario() {
            const payload = {
                documento: nuevo_documento.value.trim(),
                nombre: nuevo_nombre.value.trim(),
                apellido: nuevo_apellido.value.trim(),
                rol: nuevo_rol.value,
                id_grado: nuevo_id_grado.value || null,
                activo: nuevo_estado.checked ? 1 : 0
            };

            fetch("/SII-IETSN/api/usuarios/crear.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                credentials: "same-origin",
                body: JSON.stringify(payload)
            })
                .then(res => res.json())
                .then(res => {
                    if (!res.success) {
                        M.toast({ html: res.message, classes: 'red' });
                        return;
                    }
                    M.toast({ html: 'Usuario creado correctamente', classes: 'green' });
                    M.Modal.getInstance(document.getElementById('modalNuevoUsuario')).close();
                    cargarUsuarios();
                });
        }

        function guardarEdicionUsuario() {
            const payload = {
                id_usuario: editar_documento.dataset.id,
                nombre: editar_nombre.value.trim(),
                apellido: editar_apellido.value.trim(),
                rol: editar_rol.value,
                id_grado: editar_id_grado.value || null,
                activo: editar_estado.checked ? 1 : 0
            };

            fetch("/SII-IETSN/api/usuarios/actualizar.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                credentials: "same-origin",
                body: JSON.stringify(payload)
            })
                .then(res => res.json())
                .then(res => {
                    if (!res.success) {
                        M.toast({ html: res.message, classes: 'red' });
                        return;
                    }
                    M.toast({ html: 'Usuario actualizado correctamente', classes: 'green' });
                    M.Modal.getInstance(document.getElementById('modalEditarUsuario')).close();
                    cargarUsuarios();
                });
        }

        function toggleEstado(id_usuario) {
            fetch("/SII-IETSN/api/usuarios/toggle_estado.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                credentials: "same-origin",
                body: JSON.stringify({ id_usuario })
            })
                .then(res => res.json())
                .then(() => cargarUsuarios());
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