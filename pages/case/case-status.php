<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/bootstrap/constants.php';
require_once dirname(__DIR__, 2) . '/bootstrap/database.php';
require_once dirname(__DIR__, 2) . '/libs/lib-case-status.php';

$authenticatedUserId = (int) ($_SESSION['authenticated_user_id'] ?? 0);
$receiverEmployeeId = (int) ($_SESSION['receiver_user_id'] ?? 0);
$registeredCaseId = (int) ($_SESSION['registered_case_id'] ?? 0);

if ($authenticatedUserId < 1) {
    header('Location: ' . BASE_URL . '/pages/normal-login/normal-login.php');
    exit;
}

if ($receiverEmployeeId < 1) {
    header('Location: ' . BASE_URL . '/pages/select-user/select-user.php');
    exit;
}

if ($registeredCaseId < 1) {
    $_SESSION['case_form_error'] = 'ابتدا اطلاعات یک کیس را ثبت کنید.';
    header('Location: ' . BASE_URL . '/pages/case/case.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $caseContext = findRegisteredCaseStatusContext(
        $pdo,
        $registeredCaseId,
        $authenticatedUserId,
        $receiverEmployeeId
    );

    $caseType = determineCaseType((int) $caseContext['generation']);
    $currentCaseStatus = is_string($caseContext['case_status'])
        ? $caseContext['case_status']
        : null;
} catch (Throwable $exception) {
    error_log('Load case status error: ' . $exception->getMessage());
    $_SESSION['case_form_error'] = 'اطلاعات کیس برای تعیین وضعیت پیدا نشد.';
    header('Location: ' . BASE_URL . '/pages/case/case.php');
    exit;
}

$statusError = $_SESSION['case_status_error'] ?? null;
$statusSuccess = $_SESSION['case_status_success'] ?? null;

unset(
    $_SESSION['case_status_error'],
    $_SESSION['case_status_success']
);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

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
        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

        <?php if (is_string($statusError) && $statusError !== ''): ?>
            <p class="form-message form-message--error" role="alert">
                <?= htmlspecialchars($statusError, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <?php if (is_string($statusSuccess) && $statusSuccess !== ''): ?>
            <p class="form-message form-message--success" role="status">
                <?= htmlspecialchars($statusSuccess, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <fieldset class="form-section">
            <legend>
                وضعیت کیس
            </legend>
            <?php if ($caseType === 'new'): ?>
                <section class="new-case">
                    <h2>کیس جدید</h2>
                    <div class="case-status">
                        <label for="case-status"> وضعیت کیس :</label>
                        <select id="case-status" name="case_status" required>
                            <option value="" disabled <?= $currentCaseStatus === null ? 'selected' : '' ?>>
                                Select Status
                            </option>
                            <option value="in_use" <?= $currentCaseStatus === 'in_use' ? 'selected' : '' ?>>
                                در حال استفاده
                            </option>
                            <option value="unused" <?= $currentCaseStatus === 'unused' ? 'selected' : '' ?>>
                                استفاده نشده
                            </option>
                        </select>
                    </div>
                </section>
                <hr>
            <?php else: ?>
                <section class="old-case" id="old-case">
                    <h2>کیس قدیمی</h2>
                    <div class="case-status">
                        <label for="old-case-status"> وضعیت کیس :</label>
                        <select id="old-case-status" name="case_status" required>
                            <option value="" disabled <?= $currentCaseStatus === null ? 'selected' : '' ?>>
                                Select Status
                            </option>
                            <option value="in_use" <?= $currentCaseStatus === 'in_use' ? 'selected' : '' ?>>
                                در حال استفاده
                            </option>
                            <option value="retired" <?= $currentCaseStatus === 'retired' ? 'selected' : '' ?>>
                                از رده خارج شده
                            </option>
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
</body>

</html>
