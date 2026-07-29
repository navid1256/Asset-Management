<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/bootstrap/constants.php';

if (empty($_SESSION['authenticated_user_id'])) {
    header('Location: ' . BASE_URL . '/pages/normal-login/normal-login.php');
    exit;
}

if (empty($_SESSION['receiver_user_id'])) {
    header('Location: ' . BASE_URL . '/pages/select-user/select-user.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/assets.css">
</head>

<body>
    <header>
        <h1>نرم افزار مدیریت درخواست و ثبت تجهیزات فناوری</h1>
        <div class="user-profile">
            <img src="<?= ASSETS_URL ?>/img/profile-avatar.avif" alt="user profile" class="avatar">
            <div class="user-info">
                <p class="username">
                    <span>تحویل گیرنده :</span>
                    <span id="profile-full-name">نام کاربر</span>
                </p>
                <p class="userrole" id="profile-department">معاونت / اداره</p>
            </div>
        </div>
        <div class="logged-in-user">
            <span>کاربر :</span>
            <span id="logged-in-username">کاربر نامشخص</span>
        </div>
        <img src="<?= ASSETS_URL ?>/img/logo.png" alt="لوگو شرکت">
    </header>

    <main class="assets-main">
        <section class="asset-actions" aria-label="انتخاب نوع تجهیزات">
            <button type="button" class="asset-button"
                data-target-page="<?= BASE_URL ?>/pages/case/case.php">کیس</button>
            <button type="button" class="asset-button"
                data-target-page="<?= BASE_URL ?>/pages/monitor/monitor.php"> مانیتور</button>
            <button type="button" class="asset-button">تونر</button>
            <button type="button" class="asset-button">لپ تاپ</button>
            <button type="button" class="asset-button">موس و کیبورد</button>
            <button type="button" class="asset-button">پرینتر و اسکنر</button>
        </section>
    </main>
    <script src="<?= ASSETS_URL ?>/js/user-profile.js?v=<?= filemtime(BASE_PATH . '/assets/js/user-profile.js') ?>"></script>
    <script src="<?= ASSETS_URL ?>/js/assets.js"></script>
</body>

</html>
