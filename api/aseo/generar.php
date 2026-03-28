<?php
require_once __DIR__ . "/../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);
$id_grado = $data['grupo'];
$fecha = date("Y-m-d");

$actividades = [
  'barrer' => 2,
  'ordenar_mesas' => 2,
  'ordenar_sillas' => 1,
  'vaciar_canecas' => 1,
  'trapear' => 2
];

// borrar anterior
$stmt = $pdo->prepare("DELETE FROM tareas_aseo WHERE fecha=? AND id_grado=?");
$stmt->execute([$fecha, $id_grado]);

foreach ($actividades as $actividad => $cupos) {

    for ($i = 0; $i < $cupos; $i++) {

        $sql = "
        SELECT a.id_usuario
        FROM asistencia a
        LEFT JOIN tareas_aseo t 
          ON t.id_usuario = a.id_usuario 
          AND t.actividad = ?
        WHERE a.fecha = ?
        AND a.id_grado = ?
        AND a.id_usuario NOT IN (
            SELECT id_usuario FROM tareas_aseo 
            WHERE fecha=? AND id_grado=?
        )
        GROUP BY a.id_usuario
        ORDER BY COUNT(t.id) ASC
        LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$actividad, $fecha, $id_grado, $fecha, $id_grado]);

        if ($row = $stmt->fetch()) {

            $insert = $pdo->prepare("
                INSERT INTO tareas_aseo (id_usuario, fecha, id_grado, actividad)
                VALUES (?, ?, ?, ?)
            ");

            $insert->execute([
                $row['id_usuario'],
                $fecha,
                $id_grado,
                $actividad
            ]);
        }
    }
}

echo json_encode(["success" => true]);