<?php
require_once __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json');
requireAdmin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $db->query("SELECT * FROM posts ORDER BY sort_order DESC, created_at DESC");
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    jsonResponse($posts);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    if ($title === '') {
        jsonResponse(['error' => 'Title is required'], 400);
    }

    $stmt = $db->prepare("INSERT INTO posts (title, caption, media_type, media_url, thumbnail_url, event_date, event_time, location, published, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $caption = $_POST['caption'] ?? '';
    $mediaType = $_POST['media_type'] ?? 'image';
    $mediaUrl = $_POST['media_url'] ?? '';
    $thumbnailUrl = $_POST['thumbnail_url'] ?? '';
    $eventDate = $_POST['event_date'] ?? '';
    $eventTime = $_POST['event_time'] ?? '';
    $location = $_POST['location'] ?? '';
    $published = intval($_POST['published'] ?? 1);
    $sortOrder = intval($_POST['sort_order'] ?? 0);

    $stmt->bind_param('ssssssssii', $title, $caption, $mediaType, $mediaUrl, $thumbnailUrl, $eventDate, $eventTime, $location, $published, $sortOrder);

    if ($stmt->execute()) {
        jsonResponse(['id' => $db->insert_id, 'success' => true], 201);
    }
    jsonResponse(['error' => 'Failed to create post: ' . $db->error], 500);
}

jsonResponse(['error' => 'Method not allowed'], 405);
