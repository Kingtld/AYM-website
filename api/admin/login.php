<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$name = trim($_POST['name'] ?? '');
$surname = trim($_POST['surname'] ?? '');
$phrase = trim($_POST['phrase'] ?? '');
$rating = intval($_POST['rating'] ?? 0);

if (
    $name === ADMIN_NAME &&
    $surname === ADMIN_SURNAME &&
    $phrase === ADMIN_PHRASE &&
    $rating === ADMIN_RATING
) {
    $_SESSION['admin'] = true;
    $_SESSION['admin_name'] = $name;
    jsonResponse(['success' => true, 'admin' => true]);
}

jsonResponse(['error' => 'Invalid credentials'], 401);
