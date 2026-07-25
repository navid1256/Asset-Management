<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/bootstrap/constants.php';
require_once dirname(__DIR__, 2) . '/process/save-case-status.php';

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
    <title>Case Status</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/case-status.css">
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
                    <span id="profile-full-name">نام تحویل گیرنده</span>
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

    <form action="<?= BASE_URL ?>/process/save-case-status.php" method="post" class="case-form">
        <input type="hidden" name="csrf-token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
        <fieldset class="form-section">
            <legend>
                وضعیت کیس
            </legend>
            <?php if ($caseType === 'new'): ?>
                <section class="new-case">
                    <h2>کیس جدید</h2>
                    <div class="case-status">
                        <label for="case-status"> وضعیت کیس :</label>
                        <select id="case-status" name="caseStatus">
                            <option value="Select Status" disabled selected>Select Status</option>
                            <option value="in_use">در حال استفاده</option>
                            <option value="unused">استفاده نشده</option>
                        </select>
                    </div>
                </section>
                <hr>
            <?php else: ?>
                <section class="old-case" id="old-case">
                    <h2>کیس قدیمی</h2>
                    <div class="case-status">
                        <label for="old-case-status"> وضعیت کیس :</label>
                        <select id="old-case-status" name="oldCaseStatus">
                            <option value="Select Status" disabled selected>Select Status</option>
                            <option value="in_use">در حال استفاده</option>
                            <option value="retired">از رده خارج شده</option>
                        </select>
                    </div>
                </section>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="submit-button">ثبت وضعیت</button>
            </div>
        </fieldset>


    </form>



    <script src="<?= ASSETS_URL ?>/js/user-profile.js?v=<?= filemtime(BASE_PATH . '/assets/js/user-profile.js') ?>"></script>
    <script src="<?= ASSETS_URL ?>/js/case-status.js"></script>
</body>

</html>