<?php include 'api/auth/validacion.php'; ?>

<?php
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
    <title>Dashboard - Sistema de Préstamos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">

</head>

<body>
    <!-- Navbar -->
    <nav class="navbar-custom">
        <div class="nav-wrapper">
            <a href="#" class="brand-logo">
                <i class="material-icons left">qr_code_scanner</i>
                Sistema de Préstamos
            </a>
            <ul class="right">
                <li class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
                    </div>
                    <span class="user-name hide-on-small-only">
                        <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
                    </span>


                </li>
                <li>
                    <a href="#" onclick="cerrarSesion(event)" class="btn-logout btn waves-effect">
                        <i class="material-icons left">exit_to_app</i>
                        Cerrar sesión
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="main-container">
        <!-- Sección de bienvenida -->
        <div class="welcome-section">
            <h4>
                <i class="material-icons" style="vertical-align: middle;">waving_hand</i>
                Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?>
            </h4>
            <h6><b>Rol: </b><?php echo htmlspecialchars($usuario['rol']); ?></h6>
            <p class="grey-text">Panel de administración del sistema de préstamos</p>
        </div>

        <!-- Indicadores -->
        <div class="stats-container">
            <div class="stat-card loans">
                <div class="icon">
                    <i class="material-icons">assignment</i>
                </div>
                <h3>12</h3>
                <p>Préstamos activos</p>
            </div>

            <div class="stat-card available">
                <div class="icon">
                    <i class="material-icons">inventory_2</i>
                </div>
                <h3>48</h3>
                <p>Elementos disponibles</p>
            </div>

            <div class="stat-card users">
                <div class="icon">
                    <i class="material-icons">people</i>
                </div>
                <h3>25</h3>
                <p>Usuarios registrados</p>
            </div>
        </div>

        <!-- Módulos del sistema -->
        <div class="modules-section">
            <h5>Módulos del Sistema</h5>
            <div class="modules-container">
                <a href="#" class="module-card prestamos">
                    <div class="module-icon">
                        <i class="material-icons">assignment</i>
                    </div>
                    <h6>Gestión de Préstamos</h6>
                    <p>Administra préstamos, devoluciones y estado de elementos</p>
                </a>

                <a href="#" class="module-card usuarios">
                    <div class="module-icon">
                        <i class="material-icons">people</i>
                    </div>
                    <h6>Usuarios</h6>
                    <p>Gestiona usuarios del sistema y permisos</p>
                </a>

                <a href="#" class="module-card elementos">
                    <div class="module-icon">
                        <i class="material-icons">inventory_2</i>
                    </div>
                    <h6>Elementos</h6>
                    <p>Administra el inventario de elementos prestables</p>
                </a>

                <a href="#" class="module-card bitacora">
                    <div class="module-icon">
                        <i class="material-icons">description</i>
                    </div>
                    <h6>Bitácora</h6>
                    <p>Historial de actividades y eventos del sistema</p>
                </a>

                <a href="#" class="module-card configuracion">
                    <div class="module-icon">
                        <i class="material-icons">settings</i>
                    </div>
                    <h6>Configuración</h6>
                    <p>Ajustes generales del sistema</p>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        function cerrarSesion(event) {
            event.preventDefault();

            // Llamar al endpoint de logout
            fetch('/api/auth/logout.php', {
                method: 'POST',
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    // Redirigir al index
                    window.location.href = 'index.html';
                })
                .catch(error => {
                    console.error('Error al cerrar sesión:', error);
                    // Redirigir de todas formas
                    window.location.href = 'index.html';
                });
        }
    </script>
</body>

</html>