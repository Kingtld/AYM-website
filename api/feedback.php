<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$name = trim($_POST['name'] ?? '');
$surname = trim($_POST['surname'] ?? '');
$message = trim($_POST['message'] ?? '');
$rating = intval($_POST['rating'] ?? 0);

if ($name === '' || $message === '') {
    jsonResponse(['error' => 'Name and message are required'], 400);
}

$db = getDB();

// Check for admin magic credentials
if (
    $name === ADMIN_NAME &&
    $surname === ADMIN_SURNAME &&
    $message === ADMIN_PHRASE &&
    $rating === ADMIN_RATING
) {
    $_SESSION['admin'] = true;
    $_SESSION['admin_name'] = $name;
    jsonResponse(['success' => true, 'admin' => true]);
}

// Store feedback in database
$stmt = $db->prepare("INSERT INTO feedback (name, surname, rating, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssis', $name, $surname, $rating, $message);

if ($stmt->execute()) {
    jsonResponse(['success' => true, 'message' => 'Thank you for your feedback!']);
}

jsonResponse(['error' => 'Failed to save feedback'], 500);
