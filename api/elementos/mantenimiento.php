<?php
session_start();
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
  echo json_encode(["success" => false, "message" => "No autorizado"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id_elemento'] ?? null;

if (!$id) {
  echo json_encode(["success" => false, "message" => "Elemento inválido"]);
  exit;
}

$id_usuario = $_SESSION['usuario']['id_usuario'];

// Obtener estado actual
$stmt = $pdo->prepare("SELECT estado FROM elementos WHERE id_elemento = ?");
$stmt->execute([$id]);
$el = $stmt->fetch();

if (!$el) {
  echo json_encode(["success" => false, "message" => "Elemento no encontrado"]);
  exit;
}

if ($el['estado'] !== "Disponible") {
  echo json_encode(["success" => false, "message" => "Solo se puede enviar a mantenimiento desde Disponible"]);
  exit;
}

// Actualizar estado
$pdo->prepare("
  UPDATE elementos SET estado = 'Mantenimiento'
  WHERE id_elemento = ?
")->execute([$id]);

// Bitácora
$pdo->prepare("
  INSERT INTO bitacora (id_elemento, id_usuario, accion, fecha, detalle)
  VALUES (?, ?, 'MANTENIMIENTO', NOW(), 'Elemento enviado a mantenimiento')
")->execute([$id, $id_usuario]);

echo json_encode(["success" => true]);
