<?php

require_once __DIR__ . "/../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);

$id_grado = $data['grupo'];
$fecha = date("Y-m-d");

/* ==========================================
   ACTIVIDADES Y CUPOS
========================================== */

$actividades = [
    'barrer' => 2,
    'ordenar_mesas' => 2,
    'ordenar_sillas' => 1,
    'vaciar_canecas' => 1,
    'trapear' => 2
];

/* ==========================================
   FUNCIONES
========================================== */

function obtenerCicloActual($pdo, $id_grado)
{
    $stmt = $pdo->prepare("
        SELECT MAX(ciclo) as ciclo
        FROM tareas_aseo
        WHERE id_grado = ?
    ");

    $stmt->execute([$id_grado]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return ($row && $row['ciclo'])
        ? (int) $row['ciclo']
        : 1;
}

function cicloCompleto($pdo, $id_grado, $ciclo, $totalActividades, $fecha)
{
    // Estudiantes presentes HOY
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT id_usuario) as estudiantes
        FROM asistencia
        WHERE id_grado = ?
        AND fecha = ?
        AND (estado IS NULL OR estado = 'presente')
    ");

    $stmt->execute([$id_grado, $fecha]);

    $estudiantes = (int) $stmt->fetch()['estudiantes'];

    // Total asignaciones del ciclo
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM tareas_aseo
        WHERE id_grado = ?
        AND ciclo = ?
    ");

    $stmt->execute([$id_grado, $ciclo]);

    $total = (int) $stmt->fetch()['total'];

    return $estudiantes > 0
        && $total >= ($estudiantes * $totalActividades);
}

function actividadCompleta($pdo, $id_grado, $actividad, $ciclo, $fecha)
{
    // Estudiantes presentes HOY
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT id_usuario) as estudiantes
        FROM asistencia
        WHERE id_grado = ?
        AND fecha = ?
        AND (estado IS NULL OR estado = 'presente')
    ");

    $stmt->execute([$id_grado, $fecha]);

    $estudiantes = (int) $stmt->fetch()['estudiantes'];

    // Cuántos ya hicieron esa actividad
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT id_usuario) as total
        FROM tareas_aseo
        WHERE id_grado = ?
        AND actividad = ?
        AND ciclo = ?
    ");

    $stmt->execute([
        $id_grado,
        $actividad,
        $ciclo
    ]);

    $total = (int) $stmt->fetch()['total'];

    return $estudiantes > 0
        && $total >= $estudiantes;
}

/* ==========================================
   CICLO
========================================== */

$ciclo = obtenerCicloActual($pdo, $id_grado);

$totalActividades = count($actividades);

if (
    cicloCompleto(
        $pdo,
        $id_grado,
        $ciclo,
        $totalActividades,
        $fecha
    )
) {
    $ciclo++;
}

/* ==========================================
   LIMPIAR SOLO EL DÍA ACTUAL
========================================== */

$stmt = $pdo->prepare("
    DELETE FROM tareas_aseo
    WHERE fecha = ?
    AND id_grado = ?
    AND ciclo = ?
");

$stmt->execute([
    $fecha,
    $id_grado,
    $ciclo
]);

/* ==========================================
   GENERAR TAREAS
========================================== */

foreach ($actividades as $actividad => $cupos) {

    for ($i = 0; $i < $cupos; $i++) {

        $actividadLlena = actividadCompleta(
            $pdo,
            $id_grado,
            $actividad,
            $ciclo,
            $fecha
        );

        /* ==========================================
           FILTRO PARA EVITAR REPETIR ACTIVIDAD
        ========================================== */

        $filtroActividad = "";

        if (!$actividadLlena) {

            $filtroActividad = "
                AND a.id_usuario NOT IN (
                    SELECT id_usuario
                    FROM tareas_aseo
                    WHERE actividad = ?
                    AND ciclo = ?
                    AND id_grado = ?
                )
            ";
        }

        /* ==========================================
           QUERY PRINCIPAL
        ========================================== */

        $sql = "

            SELECT

                a.id_usuario,

                COUNT(DISTINCT t3.id) AS total_participaciones,

                COALESCE(
                    MAX(t3.fecha),
                    '2000-01-01'
                ) AS ultima_participacion,

                DATEDIFF(
                    CURDATE(),
                    COALESCE(MAX(t3.fecha), '2000-01-01')
                ) AS dias_sin_participar

            FROM asistencia a

            LEFT JOIN tareas_aseo t3
                ON t3.id_usuario = a.id_usuario

            WHERE a.fecha = ?
            AND a.id_grado = ?
            AND (a.estado IS NULL OR a.estado = 'presente')

            -- evitar repetir el mismo día
            AND a.id_usuario NOT IN (
                SELECT id_usuario
                FROM tareas_aseo
                WHERE fecha = ?
                AND id_grado = ?
            )

            $filtroActividad

            GROUP BY a.id_usuario

               ORDER BY
    total_participaciones ASC,
    dias_sin_participar DESC

            LIMIT 5
        ";

        $params = [
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

        $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /* ==========================================
           ALEATORIEDAD REAL
        ========================================== */

        if (!empty($candidatos)) {

            shuffle($candidatos);

            $row = $candidatos[0];

            $insert = $pdo->prepare("
                INSERT INTO tareas_aseo
                (
                    id_usuario,
                    fecha,
                    id_grado,
                    actividad,
                    ciclo
                )
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

/* ==========================================
   RESPUESTA
========================================== */

echo json_encode([
    "success" => true,
    "ciclo" => $ciclo
]);