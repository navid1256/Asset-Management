<?php

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require_once __DIR__ . '/../bootstrap/database.php';
require_once __DIR__ . '/../libs/lib-auth.php';

$userId = (int) ($_SESSION['authenticated_user_id'] ?? 0);

if ($userId < 1) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthenticated']);
    exit;
}

$user = findUserById($pdo, $userId);

if (!$user) {
    unset($_SESSION['authenticated_user_id']);
    http_response_code(401);
    echo json_encode(['error' => 'Unauthenticated']);
    exit;
}

echo json_encode([
    'user' => [
        'id' => (int) $user['id'],
        'nationalId' => $user['national_id'],
        'firstName' => $user['first_name'],
        'lastName' => $user['last_name'],
        'username' => $user['username'],
        'fullName' => trim($user['first_name'] . ' ' . $user['last_name']),
    ],
], JSON_UNESCAPED_UNICODE);
