<?php
require_once __DIR__ . "/../config/database.php";

$id_grado = $_GET['id_grado'];
$fecha = date("Y-m-d");

$sql = "
SELECT 
    u.id_usuario,
    u.nombre,
    u.apellido,
    a.metodo,
    a.estado
FROM usuarios u
LEFT JOIN asistencia a 
    ON a.id_usuario = u.id_usuario 
    AND a.fecha = ?
    AND a.id_grado = ?
WHERE u.id_grado = ?
AND u.activo = 1
ORDER BY u.nombre
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$fecha, $id_grado, $id_grado]);

$data = $stmt->fetchAll();

echo json_encode([
    "success" => true,
    "data" => $data
]);