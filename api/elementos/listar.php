<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

// 🔐 Validar sesión
if (!isset($_SESSION['usuario'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]);
    exit;
}

try {
    // 📥 Parámetros
    $buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
    $categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
    $estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';

    // 📥 Parámetros
    $buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
    $id_categoria = isset($_GET['id_categoria']) ? (int) $_GET['id_categoria'] : 0;
    $estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';

    // 🧠 SQL base
    $sql = "SELECT 
        e.id_elemento,
        e.codigo,
        e.nombre,
        e.estado,
        e.id_categoria,
        e.observaciones_generales,
        c.codigo AS categoria_codigo,
        c.nombre AS categoria_nombre
        FROM elementos e
        JOIN categorias c ON e.id_categoria = c.id_categoria
        WHERE 1=1";

    $params = [];

    // 🔍 Buscador
    if ($buscar !== '') {
        $sql .= " AND (e.codigo LIKE :buscar OR e.nombre LIKE :buscar)";
        $params[':buscar'] = "%$buscar%";
    }

    // 🧩 Filtro por categoría
    if ($id_categoria > 0) {
        $sql .= " AND e.id_categoria = :id_categoria";
        $params[':id_categoria'] = $id_categoria;
    }

    // 🔁 Filtro por estado
    if ($estado !== '') {
        $sql .= " AND e.estado = :estado";
        $params[':estado'] = $estado;
    }

    // 📊 Orden
    $sql .= " ORDER BY e.created_at ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $elementos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $elementos
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al listar elementos',
        'error' => $e->getMessage()
    ]);
}
