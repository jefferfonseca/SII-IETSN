<?php
require_once "../config/database.php";

$id_grado = $_GET['id_grado'] ?? null;

if (!$id_grado) {
    echo json_encode(["success" => false, "message" => "Grado requerido"]);
    exit;
}

// 🔹 CUMPLIMIENTO
$sql = "
SELECT 
    u.id_usuario,
    CONCAT(u.nombre, ' ', u.apellido) AS estudiante,
    COUNT(t.id) AS total,
    SUM(t.estado = 'completado') AS completadas,
    SUM(t.estado = 'ausente') AS ausentes,
    ROUND((SUM(t.estado = 'completado') / COUNT(t.id)) * 100, 2) AS porcentaje
FROM tareas_aseo t
JOIN usuarios u ON u.id_usuario = t.id_usuario
WHERE t.id_grado = ?
GROUP BY u.id_usuario
HAVING 
    COUNT(t.id) >= 3
    OR (SELECT COUNT(*) FROM tareas_aseo WHERE id_grado = ?) < 20
ORDER BY porcentaje DESC, completadas DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_grado, $id_grado]);
$cumplimiento = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 ALERTAS (top ausentes)
$sql2 = "
SELECT 
    u.id_usuario,
    CONCAT(u.nombre, ' ', u.apellido) AS estudiante,
    COUNT(*) AS ausencias
FROM tareas_aseo t
JOIN usuarios u ON u.id_usuario = t.id_usuario
WHERE t.estado = 'ausente'
AND t.id_grado = ?
GROUP BY u.id_usuario
ORDER BY ausencias DESC
LIMIT 5
";
$stmt = $pdo->prepare($sql2);
$stmt->execute([$id_grado]);
$alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 CICLO
// 🔹 CICLO REAL

// ciclo actual
$stmt = $pdo->prepare("
    SELECT MAX(ciclo) as ciclo
    FROM tareas_aseo
    WHERE id_grado = ?
");
$stmt->execute([$id_grado]);
$cicloActual = (int)$stmt->fetch()['ciclo'] ?: 1;

// total tareas en ese ciclo
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total
    FROM tareas_aseo
    WHERE id_grado = ? AND ciclo = ?
");
$stmt->execute([$id_grado, $cicloActual]);
$total = (int)$stmt->fetch()['total'];

// estudiantes activos (los que participan)
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT id_usuario) as estudiantes
    FROM asistencia
    WHERE id_grado = ?
");
$stmt->execute([$id_grado]);
$estudiantes = (int)$stmt->fetch()['estudiantes'];

// actividades (fijas)
$actividades = 5;

// total esperado
$totalEsperado = $estudiantes * $actividades;

// progreso real
$porcentaje = $totalEsperado > 0 
    ? round(($total / $totalEsperado) * 100, 2)
    : 0;

$ciclo = [
    "ciclo" => $cicloActual,
    "total" => $total,
    "esperado" => $totalEsperado,
    "porcentaje" => $porcentaje
];

echo json_encode([
    "success" => true,
    "data" => [
        "cumplimiento" => $cumplimiento,
        "alertas" => $alertas,
        "ciclo" => $ciclo
    ]
]);