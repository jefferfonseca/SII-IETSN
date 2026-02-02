<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');

// ============================
// Validar sesión
// ============================
if (
    !isset($_SESSION['usuario']) ||
    !isset($_SESSION['usuario']['id_usuario'])
) {
    echo json_encode([
        'success' => false,
        'message' => 'Sesión no válida'
    ]);
    exit;
}

try {

    $stmt = $pdo->prepare("
    SELECT
        p.id                AS id_prestamo,
        p.fecha_prestamo,
        p.fecha_devolucion,
        p.estado,

        u.id_usuario        AS tomador_id,
        CONCAT(u.nombre, ' ', u.apellido) AS tomador_nombre,
        u.rol               AS tomador_rol,

        e.id_elemento,
        e.nombre            AS elemento_nombre,
        e.codigo            AS elemento_codigo,

        op.id_usuario       AS operador_id,
        CONCAT(op.nombre, ' ', op.apellido) AS operador_nombre

    FROM prestamos p
    INNER JOIN usuarios u  ON u.id_usuario = p.id_tomador
    INNER JOIN elementos e ON e.id_elemento = p.id_elemento
    INNER JOIN usuarios op ON op.id_usuario = p.id_operador

    ORDER BY p.fecha_prestamo DESC
");


    $stmt->execute();
    $prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $prestamos
    ]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Error al listar préstamos'
    ]);
}
