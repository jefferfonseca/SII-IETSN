<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

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

// ============================
// Leer filtros
// ============================
$estado = $_GET['estado'] ?? 'activo';
$estado = strtolower(trim($estado));

$estadosPermitidos = ['activo', 'devuelto'];

if (!in_array($estado, $estadosPermitidos)) {
    $estado = 'activo';
}

try {

    $stmt = $pdo->prepare("
        SELECT
            p.id                AS id_prestamo,
            p.fecha_prestamo,
            p.fecha_devolucion,
            LOWER(TRIM(p.estado)) AS estado,

            u.id_usuario        AS tomador_id,
            CONCAT(u.nombre, ' ', u.apellido) AS tomador_nombre,
            u.rol               AS tomador_rol,

            e.id_elemento,
            e.nombre            AS elemento_nombre,
            e.codigo            AS elemento_codigo,

            op.id_usuario       AS operador_id,
            CONCAT(op.nombre, ' ', op.apellido) AS operador_nombre,

            /* ===== CAMPOS CALCULADOS ===== */
            CASE
                WHEN LOWER(TRIM(p.estado)) = 'activo'
                 AND p.fecha_devolucion IS NOT NULL
                 AND DATE(p.fecha_devolucion) < CURDATE()
                THEN 1
                ELSE 0
            END AS vencido,

            CASE
                WHEN LOWER(TRIM(p.estado)) = 'activo'
                 AND p.fecha_devolucion IS NOT NULL
                 AND DATE(p.fecha_devolucion) < CURDATE()
                THEN DATEDIFF(CURDATE(), DATE(p.fecha_devolucion))
                ELSE 0
            END AS dias_retraso

        FROM prestamos p
        INNER JOIN usuarios u  ON u.id_usuario = p.id_tomador
        INNER JOIN elementos e ON e.id_elemento = p.id_elemento
        INNER JOIN usuarios op ON op.id_usuario = p.id_operador

        WHERE LOWER(TRIM(p.estado)) = ?

        ORDER BY p.fecha_prestamo DESC
    ");

    $stmt->execute([$estado]);
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
