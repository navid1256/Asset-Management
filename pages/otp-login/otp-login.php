<?php require_once dirname(__DIR__, 2) . '/bootstrap/constants.php'; ?>
<!doctype html>
<html dir="rtl" lang="fa">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ورود  پرسنل معاونت برنامه ریزی و فناوری</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/login.css" />
  </head>
  <body>
    <div class="wrapper">
      <img class="logo" src="<?= ASSETS_URL ?>/img/logo 1024.png" alt="Logo" />
      <h2>ورود</h2>
      <form method="post" id="loginForm">
        <!-- شماره موبایل -->
        <div class="input-box">
          <input
            type="text"
            name="phone"
            placeholder="شماره موبایل"
            value=""
            inputmode="numeric"
            maxlength="11"
            pattern="۰۹[۰-۹]{9}"
            data-persian-number
            required
          />
        </div>
        <!-- کد OTP و دکمه درخواست کد -->
        <div class="input-box">
          <input
            type="text"
            name="otp"
            inputmode="numeric"
            maxlength="6"
            pattern="[۰-۹]{6}"
            placeholder="کد یک‌بار مصرف"
            data-persian-number
          />
          <button type="button" class="btn-inside" id="codeBtn">دریافت کد</button>
        </div>
        <!-- دکمه ورود -->
        <button type="submit" name="verify" class="btn">ورود</button>
      </form>
    </div>
    <button type="button" class="back-button" onclick="window.history.back()" aria-label="بازگشت به صفحه قبل">
      <span aria-hidden="true">&larr;</span>
      <span>بازگشت</span>
    </button>
    <script src="<?= ASSETS_URL ?>/js/persian-digits.js?v=<?= filemtime(BASE_PATH . '/assets/js/persian-digits.js') ?>"></script>
  </body>
</html>
