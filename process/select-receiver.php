<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap/constants.php';

function redirectToReceiverForm(): void
{
    header('Location: ' . BASE_URL . '/pages/select-user/select-user.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectToReceiverForm();
}

if (empty($_SESSION['authenticated_user_id'])) {
    header('Location: ' . BASE_URL . '/pages/normal-login/normal-login.php');
    exit;
}

require_once __DIR__ . '/../bootstrap/database.php';
require_once __DIR__ . '/../libs/lib-reciever.php';
require_once __DIR__ . '/../libs/lib-input.php';

$oldInput = [
    'first_name' => trim($_POST['first_name'] ?? ''),
    'last_name' => trim($_POST['last_name'] ?? ''),
    'national_id' => trim($_POST['national_id'] ?? ''),
    'mobile' => trim($_POST['mobile'] ?? ''),
    'deputy_id' => trim($_POST['deputy_id'] ?? ''),
    'department_id' => trim($_POST['department_id'] ?? ''),
];

$_SESSION['receiver_form_old'] = $oldInput;

try {
    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['receiver_form_csrf'] ?? '';

    if (
        !is_string($submittedToken)
        || !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $submittedToken)
    ) {
        throw new InvalidArgumentException('درخواست نامعتبر است. صفحه را دوباره بارگذاری کنید.');
    }

    $firstName = $oldInput['first_name'];
    $lastName = $oldInput['last_name'];
    $nationalId = normalizeDigitsToEnglish($oldInput['national_id']);
    $mobile = normalizeDigitsToEnglish($oldInput['mobile']);
    $deputyId = filter_var($oldInput['deputy_id'], FILTER_VALIDATE_INT);
    $departmentId = filter_var($oldInput['department_id'], FILTER_VALIDATE_INT);

    if ($firstName === '' || $lastName === '') {
        throw new InvalidArgumentException('نام و نام خانوادگی الزامی هستند.');
    }

    if (mb_strlen($firstName) > 100 || mb_strlen($lastName) > 150) {
        throw new InvalidArgumentException('نام یا نام خانوادگی بیشتر از حد مجاز است.');
    }

    if (preg_match('/\p{N}/u', $firstName . $lastName)) {
        throw new InvalidArgumentException('نام و نام خانوادگی نباید شامل عدد باشند.');
    }

    if (!preg_match('/^[0-9]{10}$/', $nationalId)) {
        throw new InvalidArgumentException('کد ملی باید دقیقاً ۱۰ رقم باشد.');
    }

    if (!preg_match('/^09[0-9]{9}$/', $mobile)) {
        throw new InvalidArgumentException('شماره همراه باید ۱۱ رقم و با ۰۹ شروع شود.');
    }

    if ($deputyId === false || $departmentId === false) {
        throw new InvalidArgumentException('معاونت و اداره را انتخاب کنید.');
    }

    if (!departmentBelongsToDeputy($pdo, $departmentId, $deputyId)) {
        throw new InvalidArgumentException('اداره انتخاب‌شده متعلق به معاونت انتخاب‌شده نیست.');
    }

    $receiverId = saveEmployee(
        $pdo,
        $firstName,
        $lastName,
        $nationalId,
        $mobile,
        $deputyId,
        $departmentId
    );

    if ($receiverId < 1) {
        throw new RuntimeException('شناسه تحویل‌گیرنده ایجاد نشد.');
    }

    $_SESSION['receiver_user_id'] = $receiverId;
    $_SESSION['receiver_form_csrf'] = bin2hex(random_bytes(32));

    unset($_SESSION['receiver_form_error'], $_SESSION['receiver_form_old']);

    header('Location: ' . BASE_URL . '/pages/select-assets/assets.php');
    exit;
} catch (InvalidArgumentException $exception) {
    $_SESSION['receiver_form_error'] = $exception->getMessage();
    redirectToReceiverForm();
} catch (PDOException | RuntimeException $exception) {
    error_log($exception->getMessage());
    $_SESSION['receiver_form_error'] = 'ثبت اطلاعات تحویل‌گیرنده انجام نشد.';
    redirectToReceiverForm();
}
