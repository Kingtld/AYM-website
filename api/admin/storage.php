<?php
require_once __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json');
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$db = getDB();

$postCount = $db->query("SELECT COUNT(*) as c FROM posts")->fetch_assoc()['c'];
$feedbackCount = $db->query("SELECT COUNT(*) as c FROM feedback")->fetch_assoc()['c'];
$bookingCount = $db->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c'];

$totalSize = 0;
$imageCount = 0;
$videoCount = 0;
$totalFiles = 0;

$uploadDir = UPLOAD_DIR;
if (is_dir($uploadDir)) {
    $iterator = new FilesystemIterator($uploadDir);
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, ALLOWED_EXTENSIONS)) {
                $totalFiles++;
                $totalSize += $file->getSize();
                if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                    $imageCount++;
                } else {
                    $videoCount++;
                }
            }
        }
    }
}

jsonResponse([
    'images' => $imageCount,
    'videos' => $videoCount,
    'posts' => intval($postCount),
    'bookings' => intval($bookingCount),
    'feedback' => intval($feedbackCount),
    'total_files' => $totalFiles,
    'total_size' => $totalSize,
    'total_size_mb' => round($totalSize / (1024 * 1024), 2),
    'max_storage' => 10 * 1024 * 1024 * 1024,
    'usage_percent' => $totalSize > 0 ? round(($totalSize / (10 * 1024 * 1024 * 1024)) * 100, 1) : 0
]);
