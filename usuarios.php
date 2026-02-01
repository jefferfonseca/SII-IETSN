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
    <title>Gestión de Usuarios - Sistema de Préstamos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/SII-IETSN/css/usuario.css">


</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="material-icons">qr_code_scanner</i>
            </div>
            <h5>Sistema de Préstamos</h5>
            <p>Panel Administrativo</p>
        </div>

        <div class="sidebar-user">
            <div class="user-avatar">
                <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
            </div>
            <div class="user-name"><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
            </div>
            <div class="user-role">Administrador</div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-section-title">Principal</div>
                <a href="dashboard.php" class="menu-item">
                    <i class="material-icons">dashboard</i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Gestión</div>
                <a href="#" class="menu-item">
                    <i class="material-icons">assignment</i>
                    <span>Préstamos</span>
                </a>
                <a href="usuarios.php" class="menu-item active">
                    <i class="material-icons">people</i>
                    <span>Usuarios</span>
                </a>
                <a href="elementos.php" class="menu-item">
                    <i class="material-icons">inventory_2</i>
                    <span>Elementos</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Sistema</div>
                <a href="#" class="menu-item">
                    <i class="material-icons">description</i>
                    <span>Bitácora</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="material-icons">settings</i>
                    <span>Configuración</span>
                </a>
            </div>
        </div>

        <div class="sidebar-footer">
            <button class="btn btn-logout-sidebar waves-effect" onclick="cerrarSesion()">
                <i class="material-icons left">exit_to_app</i>
                Cerrar Sesión
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Bar -->
        <div class="top-bar">
            <div style="display: flex; align-items: center; gap: 20px;">
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
            <div class="usuarios-filtros">

                <!-- Rol -->
                <div class="input-field">
                    <select id="filtroRol">
                        <option value="" selected>Todos</option>
                        <option value="Admin">Admin</option>
                        <option value="Docente">Docente</option>
                        <option value="Estudiante">Estudiante</option>
                    </select>
                    <label>Rol</label>
                </div>

                <!-- Grado (oculto por defecto) -->
                <div class="input-field" id="filtroGradoContainer" style="display:none;">
                    <select id="filtroGrado">
                        <option value="" selected>Todos los grados</option>
                    </select>
                    <label>Grado</label>
                </div>

                <!-- Buscador -->
                <div class="input-field">
                    <input id="busquedaUsuario" type="text">
                    <label for="busquedaUsuario">Buscar</label>
                </div>

            </div>

            <a href="#modalNuevoUsuario" class="btn btn-nuevo-usuario waves-effect waves-light modal-trigger">
                <i class="material-icons left">person_add</i>
                Nuevo Usuario
            </a>
        </div>

        <!-- Usuarios Cards -->
        <div class="usuarios-container" id="usuariosContainer"></div>

    </div>

    <!-- Modal Nuevo Usuario -->
    <div id="modalNuevoUsuario" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="material-icons">person_add</i>
                </div>
                <div class="modal-title">
                    <h5>Nuevo Usuario</h5>
                    <p>Completa la información del usuario</p>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <label for="nuevo_documento">Documento</label>
                    <input id="nuevo_documento" type="text" class="validate">
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12 m6">
                    <label for="nuevo_nombre">Nombre</label>
                    <input id="nuevo_nombre" type="text" class="validate">
                </div>

                <div class="input-field col s12 m6">
                    <label for="nuevo_apellido">Apellido</label>
                    <input id="nuevo_apellido" type="text" class="validate">
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <label>Rol</label>
                    <select id="nuevo_rol">
                        <option value="" disabled selected>Seleccione un rol</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
            </div>
            <div class="row" id="grupo-grado-nuevo" style="display:none">
                <div class="input-field col s12">
                    <label>Grado</label>
                    <select id="nuevo_id_grado">
                        <option value="" disabled selected>Seleccione grado</option>
                    </select>
                </div>
            </div>


            <div class="switch-container">
                <div class="switch">
                    <label>
                        <i class="material-icons" style="vertical-align: middle; margin-right: 10px;">check_circle</i>
                        Usuario Inactivo
                        <input type="checkbox" id="nuevo_estado" checked>
                        <span class="lever"></span>
                        Usuario Activo
                    </label>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect btn btn-cancelar">Cancelar</a>
            <a href="#!" class="waves-effect waves-light btn btn-guardar" onclick="guardarNuevoUsuario()">
                <i class="material-icons left">save</i>
                Guardar Usuario
            </a>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div id="modalEditarUsuario" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="material-icons">edit</i>
                </div>
                <div class="modal-title">
                    <h5>Editar Usuario</h5>
                    <p>Actualiza la información del usuario</p>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <label for="editar_documento">Documento</label>
                    <input id="editar_documento" type="text" class="validate">
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12 m6">
                    <label for="editar_nombre">Nombre</label>
                    <input id="editar_nombre" type="text" class="validate">
                </div>

                <div class="input-field col s12 m6">
                    <label for="editar_apellido">Apellido</label>
                    <input id="editar_apellido" type="text" class="validate">
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <label>Rol</label>
                    <select id="editar_rol">
                        <option value="" disabled>Seleccione un rol</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
            </div>

            <div class="row" id="grupo-grado-editar" style="display:none">
                <div class="input-field col s12">
                    <label>Grado</label>
                    <select id="editar_id_grado">
                        <option value="" disabled selected>Seleccione grado</option>
                    </select>
                </div>
            </div>

            <div class="switch-container">
                <div class="switch">
                    <label>
                        <i class="material-icons" style="vertical-align: middle; margin-right: 10px;">check_circle</i>
                        Usuario Inactivo
                        <input type="checkbox" id="editar_estado">
                        <span class="lever"></span>
                        Usuario Activo
                    </label>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect btn btn-cancelar">Cancelar</a>
            <a href="#!" class="waves-effect waves-light btn btn-guardar" onclick="guardarEdicionUsuario()">
                <i class="material-icons left">save</i>
                Guardar Cambios
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
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
        document.addEventListener('DOMContentLoaded', function () {
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
           LISTAR USUARIOS (FIX DEFINITIVO)
        =============================== */
        function cargarUsuarios() {
            const rol = document.getElementById("filtroRol").value;
            const buscar = document.getElementById("busquedaUsuario").value.trim();
            const id_grado = document.getElementById("filtroGrado").value;

            const params = new URLSearchParams();
            params.append("limit", 25);

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


                    <a href="${activo ? `/SII-IETSN/qr/${usuario.id_usuario}` : '#'}"
                       class="btn btn-accion teal ${activo ? '' : 'disabled'}"
                       ${activo ? '' : 'onclick="return false;"'}>
                        <i class="material-icons">qr_code_2</i>
                        QR
                    </a>
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

            M.updateTextFields();
            M.FormSelect.init(document.getElementById('editar_rol'));
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
                M.FormSelect.init(document.getElementById("filtroGrado"));
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

                    const selects = [
                        "filtroGrado",
                        "nuevo_id_grado",
                        "editar_id_grado"
                    ];

                    selects.forEach(id => {
                        const select = document.getElementById(id);
                        if (!select) return;

                        select.innerHTML = id === "filtroGrado"
                            ? '<option value="" selected>Todos los grados</option>'
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

            if (!rolSelect || !grupoGrado) return;

            rolSelect.addEventListener("change", () => {
                if (rolSelect.value === "Estudiante") {
                    grupoGrado.style.display = "block";
                } else {
                    grupoGrado.style.display = "none";
                    const gradoSelect = grupoGrado.querySelector("select");
                    if (gradoSelect) gradoSelect.value = "";
                }

                M.FormSelect.init(grupoGrado.querySelectorAll("select"));
            });
        }
        function guardarNuevoUsuario() {
            const documento = document.getElementById('nuevo_documento').value.trim();
            const nombre = document.getElementById('nuevo_nombre').value.trim();
            const apellido = document.getElementById('nuevo_apellido').value.trim();
            const rol = document.getElementById('nuevo_rol').value;
            const id_grado = document.getElementById('nuevo_id_grado')?.value || null;
            const activo = document.getElementById('nuevo_estado').checked ? 1 : 0;

            if (!documento || !nombre || !apellido || !rol) {
                M.toast({ html: 'Completa todos los campos', classes: 'red' });
                return;
            }

            if (rol === "Estudiante" && !id_grado) {
                M.toast({ html: 'Selecciona el grado', classes: 'red' });
                return;
            }

            fetch("/SII-IETSN/api/usuarios/crear.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                credentials: "same-origin",
                body: JSON.stringify({
                    documento,
                    nombre,
                    apellido,
                    rol,
                    id_grado,
                    activo
                })
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
        function toggleEstado(id_usuario) {
            fetch("/SII-IETSN/api/usuarios/toggle_estado.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                credentials: "same-origin",
                body: JSON.stringify({ id_usuario })
            })
                .then(res => res.json())
                .then(res => {
                    if (!res.success) {
                        M.toast({
                            html: `<i class="material-icons left">error</i>${res.message}`,
                            classes: 'red rounded'
                        });
                        return;
                    }

                    M.toast({
                        html: '<i class="material-icons left">sync</i>Estado actualizado',
                        classes: 'blue rounded'
                    });

                    cargarUsuarios();
                })
                .catch(() => {
                    M.toast({
                        html: '<i class="material-icons left">error</i>Error de conexión',
                        classes: 'red rounded'
                    });
                });
        }
        function guardarEdicionUsuario() {
            const id_usuario = document.getElementById('editar_documento').dataset.id;
            const nombre = document.getElementById('editar_nombre').value.trim();
            const apellido = document.getElementById('editar_apellido').value.trim();
            const rol = document.getElementById('editar_rol').value;
            const id_grado = document.getElementById('editar_id_grado')?.value || null;
            const activo = document.getElementById('editar_estado').checked ? 1 : 0;

            if (!id_usuario || !nombre || !apellido || !rol) {
                M.toast({ html: 'Completa todos los campos', classes: 'red' });
                return;
            }

            if (rol === "Estudiante" && !id_grado) {
                M.toast({ html: 'Selecciona el grado', classes: 'red' });
                return;
            }

            fetch("/SII-IETSN/api/usuarios/actualizar.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                credentials: "same-origin",
                body: JSON.stringify({
                    id_usuario,
                    nombre,
                    apellido,
                    rol,
                    id_grado,
                    activo
                })
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

    </script>


</body>

</html>