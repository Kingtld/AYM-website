<?php
require_once __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json');
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$files = [];
$uploadDir = UPLOAD_DIR;

if (is_dir($uploadDir)) {
    $iterator = new FilesystemIterator($uploadDir);
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, ALLOWED_EXTENSIONS)) {
                $files[] = [
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'url' => '/uploads/' . $file->getFilename(),
                    'type' => $ext,
                    'modified' => date('Y-m-d H:i:s', $file->getMTime())
                ];
            }
        }
    }
}

usort($files, fn($a, $b) => strcmp($b['modified'], $a['modified']));

jsonResponse($files);
