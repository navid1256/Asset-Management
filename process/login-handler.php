<?php

session_start();

require_once __DIR__ . '/../bootstrap/database.php';
require_once __DIR__ . '/../libs/lib-auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/normal-login/normal-login.html');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: ../pages/normal-login/normal-login.html?error=required');
    exit;
}

$user = authenticateUser($pdo, $username, $password);

if (!$user) {
    header('Location: ../pages/normal-login/normal-login.html?error=invalid');
    exit;
}

session_regenerate_id(true);

$_SESSION['authenticated_user_id'] = (int) $user['id'];

header('Location: ../pages/select-user/select-user.html');
exit;
