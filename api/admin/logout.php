<?php
require_once __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json');

$_SESSION = [];
session_destroy();
jsonResponse(['success' => true]);
