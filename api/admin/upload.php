<?php
require_once __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json');
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

if (!isset($_FILES['file'])) {
    jsonResponse(['error' => 'No file uploaded'], 400);
}

$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(['error' => 'Upload error code: ' . $file['error']], 400);
}

if ($file['size'] > MAX_UPLOAD_SIZE) {
    jsonResponse(['error' => 'File too large (max 500MB)'], 400);
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ALLOWED_EXTENSIONS)) {
    jsonResponse(['error' => 'File type not allowed: ' . $ext], 400);
}

$uuid = bin2hex(random_bytes(16));
$filename = $uuid . '.' . $ext;
$destPath = UPLOAD_DIR . $filename;

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

if (move_uploaded_file($file['tmp_name'], $destPath)) {
    $url = '/uploads/' . $filename;
    jsonResponse([
        'success' => true,
        'url' => $url,
        'filename' => $filename,
        'size' => $file['size'],
        'type' => $ext
    ], 201);
}

jsonResponse(['error' => 'Failed to save file'], 500);
