<?php
require_once __DIR__ . "/../config/database.php";

$id_grado = $_GET['grupo'];
$fecha = date("Y-m-d");

$sql = "
SELECT t.id, t.actividad, t.estado, u.nombre, u.apellido
FROM tareas_aseo t
JOIN usuarios u ON u.id_usuario = t.id_usuario
WHERE t.fecha=? AND t.id_grado=?
ORDER BY t.orden AND u.id_usuario ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$fecha, $id_grado]);

$data = $stmt->fetchAll();

echo json_encode([
    "success" => true,
    "data" => $data
]);