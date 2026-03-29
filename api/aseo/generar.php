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

/* ===============================
   FUNCIONES
================================= */

function obtenerCicloActual($pdo, $id_grado)
{
    $stmt = $pdo->prepare("
        SELECT MAX(ciclo) as ciclo 
        FROM tareas_aseo 
        WHERE id_grado = ?
    ");
    $stmt->execute([$id_grado]);
    $row = $stmt->fetch();

    return ($row && $row['ciclo']) ? (int) $row['ciclo'] : 1;
}

function cicloCompleto($pdo, $id_grado, $ciclo, $totalActividades, $fecha)
{

    // estudiantes activos HOY
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT id_usuario) as estudiantes
        FROM asistencia
        WHERE id_grado = ?
        AND fecha = ?
        AND (estado IS NULL OR estado = 'presente')
    ");
    $stmt->execute([$id_grado, $fecha]);
    $estudiantes = (int) $stmt->fetch()['estudiantes'];

    // total asignaciones en el ciclo
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM tareas_aseo
        WHERE id_grado = ? AND ciclo = ?
    ");
    $stmt->execute([$id_grado, $ciclo]);
    $total = (int) $stmt->fetch()['total'];

    return $estudiantes > 0 && $total >= ($estudiantes * $totalActividades);
}

function actividadCompleta($pdo, $id_grado, $actividad, $ciclo, $fecha)
{

    // estudiantes activos HOY
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT id_usuario) as estudiantes
        FROM asistencia
        WHERE id_grado = ?
        AND fecha = ?
        AND (estado IS NULL OR estado = 'presente')
    ");
    $stmt->execute([$id_grado, $fecha]);
    $estudiantes = (int) $stmt->fetch()['estudiantes'];

    // cuántos ya hicieron esta actividad en el ciclo
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT id_usuario) as total
        FROM tareas_aseo
        WHERE id_grado = ?
        AND actividad = ?
        AND ciclo = ?
    ");
    $stmt->execute([$id_grado, $actividad, $ciclo]);
    $total = (int) $stmt->fetch()['total'];

    return $estudiantes > 0 && $total >= $estudiantes;
}

/* ===============================
   CICLO
================================= */

$ciclo = obtenerCicloActual($pdo, $id_grado);
$totalActividades = count($actividades);

if (cicloCompleto($pdo, $id_grado, $ciclo, $totalActividades, $fecha)) {
    $ciclo++;
}

/* ===============================
   LIMPIEZA SEGURA (NO ROMPE CICLO)
================================= */

$stmt = $pdo->prepare("
    DELETE FROM tareas_aseo 
    WHERE fecha=? AND id_grado=? AND ciclo=?
");
$stmt->execute([$fecha, $id_grado, $ciclo]);

/* ===============================
   ASIGNACIÓN
================================= */

foreach ($actividades as $actividad => $cupos) {

    for ($i = 0; $i < $cupos; $i++) {

        $actividadLlena = actividadCompleta($pdo, $id_grado, $actividad, $ciclo, $fecha);

        // 🔥 si ya todos hicieron esta actividad → permitir repetir
        $filtroActividad = $actividadLlena ? "" : "
            AND a.id_usuario NOT IN (
                SELECT id_usuario 
                FROM tareas_aseo 
                WHERE actividad = ?
                AND ciclo = ?
                AND id_grado = ?
            )
        ";

        $sql = "
            SELECT 
                a.id_usuario,
                COUNT(t2.id) AS ausencias,
                COUNT(t.id) AS uso_actividad
            FROM asistencia a

            LEFT JOIN tareas_aseo t 
                ON t.id_usuario = a.id_usuario 
                AND t.actividad = ?
                AND t.ciclo = ?

            LEFT JOIN tareas_aseo t2
                ON t2.id_usuario = a.id_usuario 
                AND t2.estado = 'ausente'

            WHERE a.fecha = ?
            AND a.id_grado = ?
            AND (a.estado IS NULL OR a.estado = 'presente')

            AND a.id_usuario NOT IN (
                SELECT id_usuario 
                FROM tareas_aseo 
                WHERE fecha = ? 
                AND id_grado = ?
            )

            $filtroActividad

            GROUP BY a.id_usuario

            ORDER BY 
                ausencias DESC,
                uso_actividad ASC,
                RAND()

            LIMIT 1
        ";

        $params = [
            $actividad,
            $ciclo,
            $fecha,
            $id_grado,
            $fecha,
            $id_grado
        ];

        if (!$actividadLlena) {
            $params[] = $actividad;
            $params[] = $ciclo;
            $params[] = $id_grado;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        if ($row = $stmt->fetch()) {

            $insert = $pdo->prepare("
                INSERT INTO tareas_aseo 
                (id_usuario, fecha, id_grado, actividad, ciclo)
                VALUES (?, ?, ?, ?, ?)
            ");

            $insert->execute([
                $row['id_usuario'],
                $fecha,
                $id_grado,
                $actividad,
                $ciclo
            ]);
        }
    }
}

echo json_encode([
    "success" => true,
    "ciclo" => $ciclo
]);