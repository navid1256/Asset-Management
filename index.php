<?php require_once __DIR__ . '/bootstrap/constants.php'; ?>
<!doctype html>
<html dir="rtl" lang="fa">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ورود پرسنل معاونت برنامه ریزی و فناوری</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/login-option.css" />
</head>

<body>
    <div class="wrapper">
        <img class="logo" src="<?= ASSETS_URL ?>/img/logo 1024.png" alt="Logo" />
        <h2>نرم افزار مدیریت درخواست و ثبت تجهیزات فناوری</h2>
        <form method="post" id="loginForm">
            <!-- ورود با نام کاربری و رمز عبور -->
            <div class="input-box">
                <button type="button" title="normal-login" name="normal-login" class="btn-normal-login"
                    onclick="window.location.href='<?= BASE_URL ?>/pages/normal-login/normal-login.php'">ورود با نام
                    کاربری و رمز عبور</button>
            </div>
            <!-- ورود با کد OTP -->
            <div class="input-box">
                <button type="button" title="otp-login" name="otp-login" class="btn-otp-login"
                    onclick="window.location.href='<?= BASE_URL ?>/pages/otp-login/otp-login.php'">ورود با کد OTP</button>
            </div>
            <!-- دکمه خروج -->
            <button type="button" id="close-application" class="btn">خروج</button>
        </form>
    </div>
    <script src="<?= ASSETS_URL ?>/js/login-option.js"></script>
</body>

</html>
