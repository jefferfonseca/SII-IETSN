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

            <a href="/SII-IETSN/usuarios.php" class="menu-item">
                <i class="material-icons">people</i>
                <span>Usuarios</span>
            </a>

            <!-- ELEMENTOS -->
            <div class="menu-item has-submenu" id="menu-elementos">
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
    const menuElementos = document.getElementById('menu-elementos');
    const submenuElementos = document.getElementById('submenu-elementos');

    if (menuElementos && submenuElementos) {
        menuElementos.addEventListener('click', () => {
            menuElementos.classList.toggle('open');
            submenuElementos.classList.toggle('open');
        });
    }
});
</script>
