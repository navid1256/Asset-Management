<?php require_once dirname(__DIR__, 2) . '/bootstrap/constants.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Info</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/select-user.css">
</head>

<body>
    <header>
        <h1>نرم افزار مدیریت درخواست و ثبت تجهیزات فناوری</h1>
        <div class="user-profile">
            <p>معاونت برنامه ریزی و فناوری اطلاعات</p>
        </div>
        <div class="logged-in-user">
            <span>کاربر :</span>
            <span id="logged-in-username">کاربر نامشخص</span>
        </div>
        <img src="<?= ASSETS_URL ?>/img/logo.png" alt="لوگو شرکت">
    </header>
    <form id="user-info-form" action="<?= BASE_URL ?>/pages/select-assets/assets.php">
        <fieldset class="form-section">
            <legend>
                اطلاعات تحویل گیرنده
            </legend>
            <section class="userinfo">
                <div class="form-name">
                    <div>
                        <label for="name">نام :</label>
                        <input type="text" id="name" name="name" placeholder="نام" autocomplete="given-name"
                            pattern="[^0-9۰-۹٠-٩]*" required>
                    </div>
                    <div>
                        <label for="family-name">نام خانوادگی :</label>
                        <input type="text" id="family-name" name="familyName" placeholder="نام خانوادگی"
                            autocomplete="family-name" pattern="[^0-9۰-۹٠-٩]*" required>
                    </div>
                    <div>
                        <label for="national-code">کد ملی :</label>
                        <input type="text" id="national-code" name="nationalCode" placeholder="کد ملی"
                            inputmode="numeric" maxlength="10" pattern="[0-9۰-۹٠-٩]{10}" required>
                    </div>
                </div>


                <div class="container">
                    <div>
                        <label for="moavenat">معاونت :</label>
                        <select name="moavenat" id="moavenat">
                            <option value="" disabled selected>معاونت</option>
                            <option value="1">معاونت 1</option>
                            <option value="2">معاونت 2</option>
                            <option value="3">معاونت 3</option>
                            <option value="4">معاونت 4</option>

                        </select>
                    </div>
                    <div>
                        <label for="edare">اداره / واحد :</label>
                        <select name="edare" id="edare">
                            <option value="" disabled selected>اداره</option>
                            <option value="1">اداره 1</option>
                            <option value="2">اداره 2</option>
                            <option value="3">اداره 3</option>
                            <option value="4">اداره 4</option>
                        </select>
                    </div>
                </div>
            </section>
        </fieldset>
        <button type="submit" class="submit">ثبت اطلاعات</button>
    </form>

    <footer class="page-footer">
        <form class="logout-form" action="<?= BASE_URL ?>/process/logout-handler.php" method="post">
            <button type="submit" class="logout-button">خروج</button>
        </form>
    </footer>

    <script src="<?= ASSETS_URL ?>/js/user-profile.js"></script>
    <script src="<?= ASSETS_URL ?>/js/index.js"></script>
</body>

</html>
