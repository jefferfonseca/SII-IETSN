<?php
function registrarAsistencia($pdo, $id_usuario, $id_grado, $metodo)
{
    $fecha = date("Y-m-d");

    $sql = "SELECT id FROM asistencia 
            WHERE id_usuario=? AND fecha=? AND id_grado=?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_usuario, $fecha, $id_grado]);

    if ($row = $stmt->fetch()) {

        $update = $pdo->prepare("
            UPDATE asistencia 
            SET metodo=?, estado='presente'
            WHERE id=?
        ");

        $update->execute([$metodo, $row['id']]);

    } else {

        $insert = $pdo->prepare("
            INSERT INTO asistencia (id_usuario, fecha, id_grado, metodo, estado)
            VALUES (?, ?, ?, ?, 'presente')
        ");

        $insert->execute([$id_usuario, $fecha, $id_grado, $metodo]);
    }
}