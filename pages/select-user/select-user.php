<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/bootstrap/constants.php';

if (empty($_SESSION['authenticated_user_id'])) {
    header('Location: ' . BASE_URL . '/pages/normal-login/normal-login.php');
    exit;
}

require_once BASE_PATH . '/bootstrap/database.php';
require_once BASE_PATH . '/libs/lib-reciever.php';

if (empty($_SESSION['receiver_form_csrf'])) {
    $_SESSION['receiver_form_csrf'] = bin2hex(random_bytes(32));
}

$formError = $_SESSION['receiver_form_error'] ?? null;
$oldInput = $_SESSION['receiver_form_old'] ?? [];

unset($_SESSION['receiver_form_error'], $_SESSION['receiver_form_old']);

$deputies = getDeputies($pdo);
$departments = getDepartments($pdo);

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

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

    <?php if ($formError): ?>
        <div class="form-message form-message-error" role="alert">
            <?= escapeHtml($formError) ?>
        </div>
    <?php endif; ?>

    <form id="user-info-form" action="<?= BASE_URL ?>/process/select-receiver.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= escapeHtml($_SESSION['receiver_form_csrf']) ?>">
        <fieldset class="form-section">
            <legend>
                اطلاعات تحویل گیرنده
            </legend>
            <section class="userinfo">
                <div class="form-name">
                    <div>
                        <label for="name">نام :</label>
                        <input type="text" id="name" name="first_name" placeholder="نام" autocomplete="given-name"
                            value="<?= escapeHtml($oldInput['first_name'] ?? '') ?>"
                            pattern="[^0-9۰-۹٠-٩]*" required>
                    </div>
                    <div>
                        <label for="family-name">نام خانوادگی :</label>
                        <input type="text" id="family-name" name="last_name" placeholder="نام خانوادگی"
                            value="<?= escapeHtml($oldInput['last_name'] ?? '') ?>"
                            autocomplete="family-name" pattern="[^0-9۰-۹٠-٩]*" required>
                    </div>
                    <div>
                        <label for="national-code">کد ملی :</label>
                        <input type="text" id="national-code" name="national_id" placeholder="کد ملی"
                            value="<?= escapeHtml($oldInput['national_id'] ?? '') ?>"
                            inputmode="numeric" maxlength="10" pattern="[۰-۹]{10}" data-persian-number required>
                    </div>
                    <div>
                        <label for="mobile">شماره همراه :</label>
                        <input type="tel" id="mobile" name="mobile" placeholder="۰۹xxxxxxxxx"
                            value="<?= escapeHtml($oldInput['mobile'] ?? '') ?>" inputmode="numeric"
                            maxlength="11" pattern="۰۹[۰-۹]{9}" autocomplete="tel" data-persian-number required>
                    </div>
                </div>


                <div class="container">
                    <div>
                        <label for="moavenat">معاونت :</label>
                        <select name="deputy_id" id="moavenat" required>
                            <option value="" disabled <?= empty($oldInput['deputy_id']) ? 'selected' : '' ?>>معاونت</option>
                            <?php foreach ($deputies as $deputy): ?>
                                <option value="<?= (int) $deputy['id'] ?>"
                                    <?= (string) ($oldInput['deputy_id'] ?? '') === (string) $deputy['id'] ? 'selected' : '' ?>>
                                    <?= escapeHtml($deputy['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="edare">اداره / واحد :</label>
                        <select name="department_id" id="edare" required>
                            <option value="" disabled <?= empty($oldInput['department_id']) ? 'selected' : '' ?>>اداره</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= (int) $department['id'] ?>"
                                    data-deputy-id="<?= (int) $department['deputy_id'] ?>"
                                    <?= (string) ($oldInput['department_id'] ?? '') === (string) $department['id'] ? 'selected' : '' ?>>
                                    <?= escapeHtml($department['name']) ?>
                                </option>
                            <?php endforeach; ?>
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

    <script src="<?= ASSETS_URL ?>/js/user-profile.js?v=<?= filemtime(BASE_PATH . '/assets/js/user-profile.js') ?>"></script>
    <script src="<?= ASSETS_URL ?>/js/persian-digits.js?v=<?= filemtime(BASE_PATH . '/assets/js/persian-digits.js') ?>"></script>
    <script src="<?= ASSETS_URL ?>/js/select-user.js?v=<?= filemtime(BASE_PATH . '/assets/js/select-user.js') ?>"></script>
</body>

</html>
