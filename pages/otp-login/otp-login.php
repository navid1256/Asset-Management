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
            required
          />
        </div>
        <!-- کد OTP و دکمه درخواست کد -->
        <div class="input-box">
          <input
            type="text"
            name="otp"
            pattern="^[0-9]{6}$"
            placeholder="کد یک‌بار مصرف"
            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
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
  </body>
</html>
