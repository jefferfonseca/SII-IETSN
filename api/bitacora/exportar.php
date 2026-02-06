<?php
session_start();
require_once "../config/database.php";

// 🔐 Validar sesión y rol autorizado
if (
    !isset($_SESSION["usuario"]) ||
    !in_array($_SESSION["usuario"]["rol"], ["administrativo", "ingeniero", "Admin"])
) {
    http_response_code(403);
    exit("No autorizado");
}

try {
    // ===============================
    // LEER FILTROS (JSON)
    // ===============================
    $input = json_decode(file_get_contents("php://input"), true);

    $accion       = $input["filtros"]["accion"]        ?? null;
    $id_usuario   = $input["filtros"]["usuario"]       ?? null;
    $id_elemento  = $input["filtros"]["elemento"]      ?? null;
    $fecha_inicio = $input["filtros"]["fechaInicio"]   ?? null;
    $fecha_fin    = $input["filtros"]["fechaFin"]      ?? null;

    // ===============================
    // SQL BASE (MISMA LÓGICA QUE listar.php)
    // ===============================
    $sql = "
        SELECT
            b.id AS id_evento,
            b.fecha,
            b.accion,
            b.detalle,

            CONCAT(u.nombre, ' ', u.apellido) AS usuario,
            u.rol AS rol_usuario,

            e.nombre AS elemento,
            e.codigo AS codigo_elemento

        FROM bitacora b
        INNER JOIN usuarios u ON b.id_usuario = u.id_usuario
        INNER JOIN elementos e ON b.id_elemento = e.id_elemento
        WHERE 1=1
    ";

    $params = [];

    if (!empty($accion)) {
        $sql .= " AND b.accion = :accion";
        $params[":accion"] = $accion;
    }

    if (!empty($id_usuario)) {
        $sql .= " AND b.id_usuario = :id_usuario";
        $params[":id_usuario"] = $id_usuario;
    }

    if (!empty($id_elemento)) {
        $sql .= " AND b.id_elemento = :id_elemento";
        $params[":id_elemento"] = $id_elemento;
    }

    if (!empty($fecha_inicio)) {
        $sql .= " AND DATE(b.fecha) >= :fecha_inicio";
        $params[":fecha_inicio"] = $fecha_inicio;
    }

    if (!empty($fecha_fin)) {
        $sql .= " AND DATE(b.fecha) <= :fecha_fin";
        $params[":fecha_fin"] = $fecha_fin;
    }

    $sql .= " ORDER BY b.fecha DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ===============================
    // HEADERS DE DESCARGA CSV
    // ===============================
    $filename = "bitacora_" . date("Ymd_His") . ".csv";

    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"$filename\"");

    // BOM para Excel (UTF-8 correcto)
    echo "\xEF\xBB\xBF";

    $output = fopen("php://output", "w");

    // ===============================
    // CABECERAS
    // ===============================
    fputcsv($output, [
        "ID Evento",
        "Fecha",
        "Acción",
        "Usuario",
        "Rol Usuario",
        "Elemento",
        "Código Elemento",
        "Detalle"
    ], ";");

    // ===============================
    // FILAS
    // ===============================
    foreach ($rows as $r) {
        fputcsv($output, [
            $r["id_evento"],
            $r["fecha"],
            ucfirst($r["accion"]),
            $r["usuario"],
            $r["rol_usuario"],
            $r["elemento"],
            $r["codigo_elemento"],
            $r["detalle"]
        ], ";");
    }

    fclose($output);
    exit();

} catch (Exception $e) {
    http_response_code(500);
    echo "Error al exportar la bitácora";
}
