<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/bootstrap/constants.php';

if (empty($_SESSION['authenticated_user_id'])) {
    header('Location: ' . BASE_URL . '/pages/normal-login/normal-login.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (empty($_SESSION['receiver_user_id'])) {
    header('Location: ' . BASE_URL . '/pages/select-user/select-user.php');
    exit;
}

$formSuccess = $_SESSION['case_form_success'] ?? null;
$formError = $_SESSION['case_form_error'] ?? null;

unset($_SESSION['case_form_success'], $_SESSION['case_form_error']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Info</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/monitor.css">
    <link rel="icon" href="<?= ASSETS_URL ?>/img/logo.png" type="image">
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
    <?php if ($formSuccess): ?>
        <div class="form-message form-message-success" role="status">
            <?= htmlspecialchars($formSuccess, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($formError): ?>
        <div class="form-message form-message-error" role="alert">
            <?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form id="case-info-form" action="<?= BASE_URL ?>/process/save-case.php" method="post"
        enctype="multipart/form-data">
        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
        <fieldset class="form-section">
            <legend>
                ثبت مشخصات تحویلی مانیتور
            </legend>
            <section class="it-numbers">
                <div class="shomare-amval">
                    <label for="asset-number"> شماره اموال :</label>
                    <input type="text" id="asset-number" name="assetNumber" placeholder="شماره اموال"
                        inputmode="numeric" maxlength="5" pattern="[۰-۹]{0,5}" autocomplete="off"
                        data-persian-number>
                </div>
                <div class="shomare-it">
                    <label for="it-number"> شماره مانیتور :</label>
                    <input type="text" id="it-number" name="itNumber" placeholder="شماره IT" inputmode="numeric"
                        maxlength="4" pattern="[۰-۹]{1,4}" autocomplete="off" data-persian-number required>
                </div>
            </section>
        </fieldset>
        <fieldset class="form-section">
            <legend>ثبت مشخصات فنی مانیتور</legend>
            <section class="monitor-info">
                
                    <div class="monitor">
                        <div class="monitor brand">
                            <label for="monitor-brand">برند :</label>
                            <select name="monitor_brand" id="monitor-brand" required>
                                <option value="" disabled selected>Select Brand</option>
                                <option value="LG">LG</option>
                                <option value="G-Plus">G-Plus</option>
                                <option value="ASUS">ASUS</option>
                            </select>
                        </div>
                        <div class="monitor model">
                            <label for="monitor-model">مدل :</label>
                            <select name="monitor_model" id="monitor-model" required>
                                <option value="" disabled selected>Select Model</option>
                                <option value="LG">LG</option>
                                <option value="G-Plus">G-Plus</option>
                                <option value="ASUS">ASUS</option>
                            </select>
                        </div>