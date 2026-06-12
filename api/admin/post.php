<?php
require_once __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json');
requireAdmin();

$db = getDB();
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    jsonResponse(['error' => 'Invalid post ID'], 400);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    if (!$post) {
        jsonResponse(['error' => 'Post not found'], 404);
    }
    jsonResponse($post);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        jsonResponse(['error' => 'Invalid JSON'], 400);
    }

    $stmt = $db->prepare("UPDATE posts SET title=?, caption=?, media_type=?, media_url=?, thumbnail_url=?, event_date=?, event_time=?, location=?, published=?, sort_order=? WHERE id=?");
    $title = $data['title'] ?? '';
    $caption = $data['caption'] ?? '';
    $mediaType = $data['media_type'] ?? 'image';
    $mediaUrl = $data['media_url'] ?? '';
    $thumbnailUrl = $data['thumbnail_url'] ?? '';
    $eventDate = $data['event_date'] ?? '';
    $eventTime = $data['event_time'] ?? '';
    $location = $data['location'] ?? '';
    $published = intval($data['published'] ?? 1);
    $sortOrder = intval($data['sort_order'] ?? 0);

    $stmt->bind_param('ssssssssiii', $title, $caption, $mediaType, $mediaUrl, $thumbnailUrl, $eventDate, $eventTime, $location, $published, $sortOrder, $id);

    if ($stmt->execute()) {
        jsonResponse(['success' => true]);
    }
    jsonResponse(['error' => 'Failed to update post: ' . $db->error], 500);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $stmt = $db->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        jsonResponse(['success' => true]);
    }
    jsonResponse(['error' => 'Failed to delete post'], 500);
}

jsonResponse(['error' => 'Method not allowed'], 405);
