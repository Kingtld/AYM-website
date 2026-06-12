<?php
require_once __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json');
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$db = getDB();
$result = $db->query("SELECT * FROM feedback ORDER BY created_at DESC");
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
jsonResponse($items);
