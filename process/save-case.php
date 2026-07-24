<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap/constants.php';
require_once __DIR__ . '/../bootstrap/database.php';
require_once __DIR__ . '/../libs/lib-input.php';
require_once __DIR__ . '/../libs/lib-case.php';

function redirectToCaseForm(): never
{
    header('Location: ' . BASE_URL . '/pages/case/case.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectToCaseForm();
}

$createdByUserId = (int) ($_SESSION['authenticated_user_id'] ?? 0);
$receiverEmployeeId = (int) ($_SESSION['receiver_user_id'] ?? 0);

if ($createdByUserId < 1) {
    header(
        'Location: '
            . BASE_URL
            . '/pages/normal-login/normal-login.php'
    );
    exit;
}

if ($receiverEmployeeId < 1) {
    header(
        'Location: '
            . BASE_URL
            . '/pages/select-user/select-user.php'
    );
    exit;
}

try {
    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (
        !is_string($submittedToken)
        || !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $submittedToken)
    ) {
        throw new InvalidArgumentException(
            'درخواست نامعتبر است؛ صفحه را دوباره بارگذاری کنید.'
        );
    }

    $itNumberInput = $_POST['itNumber'] ?? '';
    $assetNumberInput = $_POST['assetNumber'] ?? '';

    if (
        !is_string($itNumberInput)
        || !is_string($assetNumberInput)
    ) {
        throw new InvalidArgumentException(
            'شماره IT یا شماره اموال معتبر نیست.'
        );
    }

    $itNumber = normalizeDigitsToEnglish(
        trim($itNumberInput)
    );

    $assetNumber = normalizeDigitsToEnglish(
        trim($assetNumberInput)
    );

    if (!preg_match('/^[0-9]{1,4}$/', $itNumber)) {
        throw new InvalidArgumentException(
            'شماره IT باید بین یک تا چهار رقم باشد.'
        );
    }

    if (
        $assetNumber !== ''
        && !preg_match('/^[0-9]{1,5}$/', $assetNumber)
    ) {
        throw new InvalidArgumentException(
            'شماره اموال باید حداکثر پنج رقم باشد.'
        );
    }

    $assetNumber = $assetNumber === ''
        ? null
        : $assetNumber;

    /*
     * در مرحله بعد Transaction را از اینجا شروع می‌کنیم.
     */
    try {
        $pdo->beginTransaction();

        $caseId = createCaseNumber(
            $pdo,
            $itNumber,
            $assetNumber,
            $receiverEmployeeId,
            $createdByUserId,
            null
        );

        /*
     * توابع ثبت CPU، RAM، Storage و سایر قطعات
     * در مراحل بعدی اینجا فراخوانی می‌شوند.
     */

        $pdo->commit();

        $_SESSION['registered_case_id'] = $caseId;

        header(
            'Location: '
                . BASE_URL
                . '/pages/case/case-status.php'
        );
        exit;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log($exception->getMessage());

        $_SESSION['case_form_error'] =
            'هنگام ثبت اطلاعات کیس خطایی رخ داد.';

        redirectToCaseForm();
    }
} catch (InvalidArgumentException $exception) {
    $_SESSION['case_form_error'] = $exception->getMessage();

    redirectToCaseForm();
}
