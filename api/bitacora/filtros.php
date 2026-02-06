<?php
session_start();
require_once "../config/database.php";

header("Content-Type: application/json");

// 🔐 Validar sesión y rol autorizado
if (
    !isset($_SESSION["usuario"]) ||
    !in_array($_SESSION["usuario"]["rol"], ["administrativo", "ingeniero", "Admin"])
) {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit();
}

try {
    /* ===============================
       USUARIOS (solo los que aparecen en bitácora)
       =============================== */
    $sqlUsuarios = "
        SELECT DISTINCT
            u.id_usuario AS id,
            CONCAT(u.nombre, ' ', u.apellido) AS nombre
        FROM bitacora b
        INNER JOIN usuarios u ON b.id_usuario = u.id_usuario
        ORDER BY nombre ASC
    ";

    $usuarios = $pdo
        ->query($sqlUsuarios)
        ->fetchAll(PDO::FETCH_ASSOC);

    /* ===============================
       ELEMENTOS (solo los que aparecen en bitácora)
       =============================== */
    $sqlElementos = "
        SELECT DISTINCT
            e.id_elemento AS id,
            e.nombre,
            e.codigo
        FROM bitacora b
        INNER JOIN elementos e ON b.id_elemento = e.id_elemento
        ORDER BY e.nombre ASC
    ";

    $elementos = $pdo
        ->query($sqlElementos)
        ->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "usuarios" => $usuarios,
        "elementos" => $elementos
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error cargando filtros de bitácora"
    ]);
}
