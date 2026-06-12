<?php
require_once __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
jsonResponse(['admin' => $isAdmin]);
