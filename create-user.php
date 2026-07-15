<?php

session_start();

require_once __DIR__ . '/bootstrap/database.php';
require_once __DIR__ . '/libs/lib-auth.php';

$userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$isFirstUser = $userCount === 0;
$authenticatedUserId = (int) ($_SESSION['authenticated_user_id'] ?? 0);
$authenticatedUser = $authenticatedUserId > 0
    ? findUserById($pdo, $authenticatedUserId)
    : null;

if (!$isFirstUser && !$authenticatedUser) {
    header('Location: pages/normal-login/normal-login.html');
    exit;
}

if (!isset($_SESSION['create_user_csrf'])) {
    $_SESSION['create_user_csrf'] = bin2hex(random_bytes(32));
}

$message = '';
$messageType = '';
$values = [
    'national_id' => '',
    'first_name' => '',
    'last_name' => '',
    'username' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $field => $value) {
        $values[$field] = trim($_POST[$field] ?? '');
    }

    $password = $_POST['password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    try {
        if (!hash_equals($_SESSION['create_user_csrf'], $csrfToken)) {
            throw new RuntimeException('درخواست نامعتبر است. صفحه را دوباره بارگذاری کنید.');
        }

        if ($password !== $passwordConfirmation) {
            throw new InvalidArgumentException('رمز عبور و تکرار آن یکسان نیستند.');
        }

        createUser(
            $pdo,
            $values['national_id'],
            $values['first_name'],
            $values['last_name'],
            $values['username'],
            $password
        );

        $_SESSION['create_user_csrf'] = bin2hex(random_bytes(32));

        if ($isFirstUser) {
            header('Location: pages/normal-login/normal-login.html?created=1');
            exit;
        }

        $message = 'کاربر با موفقیت ایجاد شد.';
        $messageType = 'success';
        $values = array_fill_keys(array_keys($values), '');
    } catch (InvalidArgumentException | RuntimeException $exception) {
        $message = $exception->getMessage();
        $messageType = 'error';
    } catch (PDOException $exception) {
        error_log($exception->getMessage());
        $message = $exception->getCode() === '23000'
            ? 'کد ملی یا نام کاربری قبلاً ثبت شده است.'
            : 'ثبت کاربر انجام نشد.';
        $messageType = 'error';
    }
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html dir="rtl" lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ایجاد کاربر</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
    <div class="wrapper">
        <img class="logo" src="assets/img/logo 1024.png" alt="لوگو">
        <h2>ایجاد کاربر</h2>

        <?php if ($message !== ''): ?>
            <p role="alert" class="<?= escape($messageType) ?>"><?= escape($message) ?></p>
        <?php endif; ?>

        <form method="post" action="create-user.php">
            <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['create_user_csrf']) ?>">

            <div class="input-box">
                <input type="text" name="national_id" value="<?= escape($values['national_id']) ?>"
                    placeholder="کد ملی" inputmode="numeric" maxlength="10" pattern="[0-9]{10}" required>
            </div>

            <div class="input-box">
                <input type="text" name="first_name" value="<?= escape($values['first_name']) ?>"
                    placeholder="نام" autocomplete="given-name" pattern="[^0-9۰-۹٠-٩]*" required>
            </div>

            <div class="input-box">
                <input type="text" name="last_name" value="<?= escape($values['last_name']) ?>"
                    placeholder="نام خانوادگی" autocomplete="family-name" pattern="[^0-9۰-۹٠-٩]*" required>
            </div>

            <div class="input-box">
                <input type="text" name="username" value="<?= escape($values['username']) ?>"
                    placeholder="نام کاربری" autocomplete="username" required>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="رمز عبور"
                    autocomplete="new-password" minlength="8" required>
            </div>

            <div class="input-box">
                <input type="password" name="password_confirmation" placeholder="تکرار رمز عبور"
                    autocomplete="new-password" minlength="8" required>
            </div>

            <button type="submit" class="btn">ثبت کاربر</button>
        </form>
    </div>
</body>

</html>
