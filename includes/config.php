<?php
// --- Database Configuration ---
define('DB_HOST', 'sql106.infinityfree.com');
define('DB_NAME', 'if0_42140002_aym');
define('DB_USER', 'if0_42140002');
define('DB_PASS', 'Palesamira97');

// --- Admin Secret Codes (must match exactly) ---
define('ADMIN_NAME', 'Jehofa');
define('ADMIN_SURNAME', 'Mmabaledi');
define('ADMIN_PHRASE', 'reyago boka morena');
define('ADMIN_RATING', 2);

// --- Upload Settings ---
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 500 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', ['jpg','jpeg','png','gif','webp','mp4','webm','mov']);

// --- Start session for admin auth ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getDB(): mysqli {
    static $db = null;
    if ($db === null) {
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($db->connect_error) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }
        $db->set_charset('utf8mb4');
    }
    return $db;
}

// from manually specifying data type
function jsonResponse(mixed $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function requireAdmin(): void {
    if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
        jsonResponse(['error' => 'Unauthorized'], 401);
    }
}
