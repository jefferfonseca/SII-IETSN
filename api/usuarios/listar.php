<?php
session_start();
header("Content-Type: application/json");

// Seguridad
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Admin") {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

require_once __DIR__ . "/../config/database.php";

// Parámetros
$rol      = $_GET["rol"] ?? null;
$buscar   = $_GET["buscar"] ?? null;
$id_grado = $_GET["id_grado"] ?? null;
$limit    = isset($_GET["limit"]) ? (int)$_GET["limit"] : 25;

// Base SQL
$sql = "
SELECT 
    u.id_usuario,
    u.documento,
    u.nombre,
    u.apellido,
    u.rol,
    u.activo,
    g.nombre AS grado
FROM usuarios u
LEFT JOIN grados g ON u.id_grado = g.id_grado
WHERE 1=1
";

$params = [];

// Filtro por rol
if (!empty($rol)) {
    $sql .= " AND u.rol = :rol";
    $params["rol"] = $rol;
}

// Filtro búsqueda (documento / nombre / apellido)
if (!empty($buscar)) {
    $sql .= " AND (
        u.documento LIKE :buscar
        OR u.nombre LIKE :buscar
        OR u.apellido LIKE :buscar
    )";
    $params["buscar"] = "%" . $buscar . "%";
}

// Filtro por grado (solo estudiantes)
if ($rol === "Estudiante" && !empty($id_grado)) {
    $sql .= " AND u.id_grado = :id_grado";
    $params["id_grado"] = $id_grado;
}

// Orden y límite
$sql .= "
ORDER BY 
    u.activo DESC,
    u.apellido ASC,
    u.nombre ASC
LIMIT :limit
";

try {
    $stmt = $pdo->prepare($sql);

    // Bind dinámico
    foreach ($params as $key => $value) {
        $stmt->bindValue(":" . $key, $value);
    }

    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);

    $stmt->execute();

    echo json_encode([
        "success" => true,
        "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al listar usuarios"
    ]);
}
