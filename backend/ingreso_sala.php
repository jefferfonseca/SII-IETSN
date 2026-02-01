<?php
// backend/ingreso_sala.php
$mysqli = new mysqli("localhost", "root", "", "ietsannicolas");
if ($mysqli->connect_errno)
    die("Error DB: " . $mysqli->connect_error);

// Recibir QR o documento manual
$documento = $_POST['documento_qr'] ?? $_POST['documento_manual'] ?? null;

if (!$documento) {
    echo "Error: no se recibió ningún documento";
    exit;
}

// Buscar usuario
$stmt = $mysqli->prepare("SELECT id_usuario, nombre, apellido, rol FROM usuarios WHERE id_usuario=? OR documento=? LIMIT 1");
$stmt->bind_param("ss", $documento, $documento);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>M.toast({ html: `Documento no encontrado: ${documento}` });</script>";
    die;
} else {

    $usuario = $result->fetch_assoc();

    // Redirigir a la página de registro de préstamo
    session_start();
    $_SESSION['usuario_id'] = $usuario['id_usuario'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
    $_SESSION['usuario_rol'] = $usuario['rol'];

    header("Location: ./../frontend/prestamo.html");
    exit;
}
?>