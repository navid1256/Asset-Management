<?php

session_start();

require_once __DIR__ . '/../bootstrap/constants.php';
require_once __DIR__ . '/../bootstrap/database.php';
require_once __DIR__ . '/../libs/lib-auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/pages/normal-login/normal-login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: ' . BASE_URL . '/pages/normal-login/normal-login.php?error=required');
    exit;
}

$user = authenticateUser($pdo, $username, $password);

if (!$user) {
    header('Location: ' . BASE_URL . '/pages/normal-login/normal-login.php?error=invalid');
    exit;
}

session_regenerate_id(true);

$_SESSION['authenticated_user_id'] = (int) $user['id'];

header('Location: ' . BASE_URL . '/pages/select-user/select-user.php');
exit;
