<?php
session_start();
require_once "../config/database.php";

header("Content-Type: application/json");

// 🔐 Validar sesión y rol
if (
    !isset($_SESSION["usuario"]) ||
    !in_array($_SESSION["usuario"]["rol"], ["Admin", "Docente"])

) {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit();
}



try {
    // ===============================
    // FILTROS (opcionales, mismos que listar.php)
    // ===============================
    $accion = $_GET["accion"] ?? null;
    $id_usuario = $_GET["id_usuario"] ?? null;
    $id_elemento = $_GET["id_elemento"] ?? null;
    $fecha_inicio = $_GET["fecha_inicio"] ?? null;
    $fecha_fin = $_GET["fecha_fin"] ?? null;

    // ===============================
    // WHERE DINÁMICO
    // ===============================
    $where = " WHERE 1=1 ";
    $params = [];

    if ($accion) {
        $where .= " AND b.accion = :accion";
        $params[":accion"] = $accion;
    }

    if ($id_usuario) {
        $where .= " AND b.id_usuario = :id_usuario";
        $params[":id_usuario"] = $id_usuario;
    }

    if ($id_elemento) {
        $where .= " AND b.id_elemento = :id_elemento";
        $params[":id_elemento"] = $id_elemento;
    }

    if ($fecha_inicio) {
        $where .= " AND DATE(b.fecha) >= :fecha_inicio";
        $params[":fecha_inicio"] = $fecha_inicio;
    }

    if ($fecha_fin) {
        $where .= " AND DATE(b.fecha) <= :fecha_fin";
        $params[":fecha_fin"] = $fecha_fin;
    }

    // ===============================
    // CONSULTA DE ESTADÍSTICAS
    // ===============================
    $sql = "
    SELECT
    SUM(CASE WHEN b.accion = 'prestamo' THEN 1 ELSE 0 END)       AS prestamo,
    SUM(CASE WHEN b.accion = 'devolucion' THEN 1 ELSE 0 END)     AS devolucion,
    SUM(CASE WHEN b.accion = 'mantenimiento' THEN 1 ELSE 0 END) AS mantenimiento,
    SUM(CASE WHEN b.accion = 'fuera_servicio' THEN 1 ELSE 0 END) AS fuera_servicio,
    SUM(CASE WHEN b.accion = 'observacion' THEN 1 ELSE 0 END)   AS observacion


    FROM bitacora b
    INNER JOIN usuarios u ON b.id_usuario = u.id_usuario
    INNER JOIN elementos e ON b.id_elemento = e.id_elemento
    $where
";


    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Normalizar null → 0
    foreach ($stats as $k => $v) {
        $stats[$k] = (int) $v;
    }

    echo json_encode([
        "success" => true,
        "stats" => $stats
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error obteniendo estadísticas de bitácora"
    ]);
}
