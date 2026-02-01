<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['usuario'])) {
    echo json_encode(["success" => false, "message" => "No autenticado"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id_elemento = $data['id_elemento'] ?? null;

if (!$id_elemento) {
    echo json_encode(["success" => false, "message" => "Elemento no válido"]);
    exit;
}

// Usuario que hace la acción
$id_usuario = $_SESSION['usuario']['id_usuario'];

// 1️⃣ Obtener estado actual
$stmt = $pdo->prepare("SELECT estado FROM elementos WHERE id_elemento = ?");
$stmt->execute([$id_elemento]);
$elemento = $stmt->fetch();

if (!$elemento) {
    echo json_encode(["success" => false, "message" => "Elemento no encontrado"]);
    exit;
}

$estadoActual = $elemento['estado'];

// 2️⃣ Validar reglas
if ($estadoActual === "Prestado") {
    echo json_encode(["success" => false, "message" => "No se puede cambiar el estado de un elemento prestado"]);
    exit;
}

// 3️⃣ Definir nuevo estado
// 3️⃣ Definir nuevo estado según reglas claras
switch ($estadoActual) {
    case "Disponible":
        $nuevoEstado = "Fuera de servicio";
        break;

    case "Fuera de servicio":
    case "Mantenimiento":
        $nuevoEstado = "Disponible";
        break;

    default:
        echo json_encode([
            "success" => false,
            "message" => "Estado no permitido para cambio"
        ]);
        exit;
}


// 4️⃣ Actualizar elemento
$pdo->prepare("UPDATE elementos SET estado = ? WHERE id_elemento = ?")
    ->execute([$nuevoEstado, $id_elemento]);

// 5️⃣ Registrar en bitácora 🔥
$detalle = "$estadoActual → $nuevoEstado";

$pdo->prepare("
    INSERT INTO bitacora (id_elemento, id_usuario, accion, fecha, detalle)
    VALUES (?, ?, 'CAMBIO_ESTADO', NOW(), ?)
")->execute([
    $id_elemento,
    $id_usuario,
    $detalle
]);

echo json_encode(["success" => true]);
