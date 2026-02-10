<?php
session_start();
require_once "../config/database.php";

header("Content-Type: application/json");

// 🔐 Validar sesión
if (!isset($_SESSION["usuario"])) {
    echo json_encode([
        "success" => false,
        "message" => "No autenticado"
    ]);
    exit;
}

$id_categoria = $_GET["id_categoria"] ?? null;

if (!$id_categoria) {
    echo json_encode([
        "success" => false,
        "message" => "Categoría no válida"
    ]);
    exit;
}

try {
    // ============================
    // Obtener info de la categoría
    // ============================
    $stmt = $pdo->prepare("
        SELECT codigo
        FROM categorias
        WHERE id_categoria = ?
    ");
    $stmt->execute([$id_categoria]);
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$categoria) {
        throw new Exception("Categoría no encontrada");
    }

    $codigoCategoria = strtoupper(trim($categoria["codigo"]));

    // ============================
    // Obtener último correlativo
    // ============================
    $stmt = $pdo->prepare("
        SELECT MAX(
            CAST(
                SUBSTRING_INDEX(codigo, '-', -1) AS UNSIGNED
            )
        ) AS max_num
        FROM elementos
        WHERE id_categoria = ?
          AND codigo LIKE CONCAT('LAB-', ?, '-%')
    ");
    $stmt->execute([$id_categoria, $codigoCategoria]);
    $max = (int) $stmt->fetchColumn();

    $siguiente = $max + 1;

    // Formato con ceros a la izquierda
    $numeroFormateado = str_pad($siguiente, 3, "0", STR_PAD_LEFT);

    $codigoSugerido = "LAB-$codigoCategoria-$numeroFormateado";

    echo json_encode([
        "success" => true,
        "codigo" => $codigoSugerido
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
