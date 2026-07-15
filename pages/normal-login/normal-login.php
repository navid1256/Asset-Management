<?php require_once dirname(__DIR__, 2) . '/bootstrap/constants.php'; ?>
<!doctype html>
<html dir="rtl" lang="fa">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ورود پرسنل معاونت برنامه ریزی و فناوری</title>
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/login.css" />
</head>

<body>
  <div class="wrapper">
    <img class="logo" src="<?= ASSETS_URL ?>/img/logo 1024.png" alt="Logo" />
    <h2>ورود</h2>
    <p id="login-message" role="alert" hidden></p>
    <form action="<?= BASE_URL ?>/process/login-handler.php" method="post" id="loginForm">
      <!-- یوزر نیم -->
      <div class="input-box">
        <input type="text" name="username" placeholder="نام کاربری" value="" required />
      </div>
      <!-- پسورد -->
      <div class="input-box">
        <input type="password" name="password" placeholder="رمز عبور" required />
      </div>
      <!-- دکمه ورود -->
      <button type="submit" name="verify" class="btn">ورود</button>
    </form>
  </div>
  <script>
    const loginError = new URLSearchParams(window.location.search).get("error");
    const loginMessage = document.getElementById("login-message");

    const userCreated = new URLSearchParams(window.location.search).get("created");

    if ((loginError || userCreated) && loginMessage) {
      loginMessage.textContent = userCreated
        ? "کاربر ایجاد شد. اکنون وارد شوید."
        : loginError === "required"
          ? "نام کاربری و رمز عبور را وارد کنید."
          : "نام کاربری یا رمز عبور صحیح نیست.";
      loginMessage.hidden = false;
    }
  </script>
</body>

</html>
