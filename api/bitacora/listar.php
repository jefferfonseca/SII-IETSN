<?php
session_start();
require_once "../config/database.php";

header("Content-Type: application/json");

if (
    !isset($_SESSION["usuario"]) ||
    !in_array($_SESSION["usuario"]["rol"], ["administrativo", "ingeniero", "Admin"])
) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit();
}

try {
    // ===============================
    // FILTROS
    // ===============================
    $accion       = $_GET["accion"]        ?? null;
    $id_usuario   = $_GET["id_usuario"]    ?? null;
    $id_elemento  = $_GET["id_elemento"]   ?? null;
    $fecha_inicio = $_GET["fecha_inicio"]  ?? null;
    $fecha_fin    = $_GET["fecha_fin"]     ?? null;

    // ===============================
    // PAGINACIÓN
    // ===============================
    $page  = max(1, intval($_GET["page"] ?? 1));
    $limit = max(1, intval($_GET["limit"] ?? 20));
    $offset = ($page - 1) * $limit;

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
    // TOTAL REGISTROS
    // ===============================
    $sqlTotal = "
        SELECT COUNT(*) 
        FROM bitacora b
        $where
    ";

    $stmtTotal = $pdo->prepare($sqlTotal);
    $stmtTotal->execute($params);
    $total = (int) $stmtTotal->fetchColumn();

    // ===============================
    // DATA PAGINADA
    // ===============================
    $sql = "
        SELECT
            b.id,
            b.fecha,
            b.accion,
            b.detalle,

            b.id_usuario,
            u.nombre   AS usuario_nombre,
            u.apellido AS usuario_apellido,
            u.rol      AS usuario_rol,

            b.id_elemento,
            e.nombre AS elemento_nombre,
            e.codigo AS elemento_codigo

        FROM bitacora b
        INNER JOIN usuarios u ON b.id_usuario = u.id_usuario
        INNER JOIN elementos e ON b.id_elemento = e.id_elemento
        $where
        ORDER BY b.fecha DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);

    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }

    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $data,
        "pagination" => [
            "page" => $page,
            "limit" => $limit,
            "total" => $total,
            "pages" => ceil($total / $limit)
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error consultando bitácora"
    ]);
}
