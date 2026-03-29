<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuario = $_SESSION['usuario'] ?? null;
?>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="material-icons">qr_code_scanner</i>
        </div>
        <h5>Sistema de Préstamos</h5>
        <p>Panel Administrativo</p>
    </div>

    <?php if ($usuario): ?>
        <div class="sidebar-user">
            <div class="user-avatar">
                <?= strtoupper(substr($usuario['nombre'], 0, 1)); ?>
            </div>

            <div class="user-name">
                <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
            </div>

            <div class="user-role">
                <?= htmlspecialchars($usuario['rol']); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="sidebar-menu">

        <div class="menu-section">
            <div class="menu-section-title">Principal</div>
            <a href="/SII-IETSN/dashboard.php" class="menu-item">
                <i class="material-icons">dashboard</i>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="menu-section">
            <div class="menu-section-title">Gestión</div>

            <a href="/SII-IETSN/prestamos.php" class="menu-item">
                <i class="material-icons">assignment</i>
                <span>Préstamos</span>
            </a>
            <!-- ASEO -->
            <div class="has-children" id="menu-aseo">

                <div class="menu-parent">
                    <i class="material-icons">cleaning_services</i>
                    <span>Aseo</span>
                    <i class="material-icons arrow">expand_more</i>
                </div>

                <div class="submenu" id="submenu-aseo">

                    <a href="/SII-IETSN/aseo.php" class="submenu-item">
                        <i class="material-icons">view_list</i>
                        <span>Gestión de Aseo</span>
                    </a>

                    <a href="/SII-IETSN/aseo.php#tab-metricas" class="submenu-item">
                        <i class="material-icons">analytics</i>
                        <span>Métricas</span>
                    </a>

                </div>

            </div>
            <!-- USUARIOS CON SUBMENÚ -->
            <div class="has-children" id="menu-usuarios">

                <div class="menu-parent">
                    <i class="material-icons">people</i>
                    <span>Usuarios</span>
                    <i class="material-icons arrow">expand_more</i>
                </div>

                <div class="submenu" id="submenu-usuarios">
                    <a href="/SII-IETSN/usuarios.php" class="submenu-item">
                        <i class="material-icons">list</i>
                        <span>Listar Usuarios</span>
                    </a>
                    <a href="/SII-IETSN/generar-qr-grado.php" class="submenu-item">
                        <i class="material-icons">qr_code_2</i>
                        <span>Generar QR Masivos</span>
                    </a>
                </div>

            </div>

            <!-- ELEMENTOS -->
            <div class="has-children" id="menu-elementos">

                <div class="menu-parent">
                    <i class="material-icons">inventory_2</i>
                    <span>Elementos</span>
                    <i class="material-icons arrow">expand_more</i>
                </div>

                <div class="submenu" id="submenu-elementos">
                    <a href="/SII-IETSN/elementos.php" class="submenu-item">
                        <i class="material-icons">list</i>
                        <span>Listado</span>
                    </a>
                    <a href="/SII-IETSN/generador-qr-etiquetas.php" class="submenu-item">
                        <i class="material-icons">qr_code_2</i>
                        <span>Generador QR</span>
                    </a>
                </div>

            </div>

        </div>

        <div class="menu-section">
            <div class="menu-section-title">Sistema</div>

            <a href="/SII-IETSN/bitacora.php" class="menu-item">
                <i class="material-icons">description</i>
                <span>Bitácora</span>
            </a>

            <a href="/SII-IETSN/configuracion.php" class="menu-item">
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Menú Elementos
        const menuElementos = document.getElementById('menu-elementos');
        if (menuElementos) {
            const parentElementos = menuElementos.querySelector('.menu-parent');
            parentElementos.addEventListener('click', () => {
                menuElementos.classList.toggle('open');
                menuElementos.classList.toggle('menu-item');
            });
        }

        // Menú Usuarios (NUEVO)
        const menuUsuarios = document.getElementById('menu-usuarios');
        if (menuUsuarios) {
            const parentUsuarios = menuUsuarios.querySelector('.menu-parent');
            parentUsuarios.addEventListener('click', () => {
                menuUsuarios.classList.toggle('open');
                menuUsuarios.classList.toggle('menu-item');
            });
        }
    });

    const menuAseo = document.getElementById('menu-aseo');
    if (menuAseo) {
        const parentAseo = menuAseo.querySelector('.menu-parent');
        parentAseo.addEventListener('click', () => {
            menuAseo.classList.toggle('open');
            menuAseo.classList.toggle('menu-item');
        });
    }
    /* ===============================
   LOGOUT
=============================== */
    function cerrarSesion() {
        fetch('/SII-IETSN/api/auth/logout.php', {
            method: 'POST',
            credentials: 'same-origin'
        })
            .then(() => location.href = "/SII-IETSN/index.html");
    }
</script>