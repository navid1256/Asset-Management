<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap/constants.php';
require_once __DIR__ . '/../bootstrap/database.php';
require_once __DIR__ . '/../libs/lib-case-status.php';

/**
 * Redirects the request to the case status page.
 */
function redirectToCaseStatus(): never
{
    header('Location: ' . BASE_URL . '/pages/case/case-status.php');
    exit;
}

/**
 * Redirects to the case form with a validation error message.
 */
function redirectToCaseFormWithError(string $message): never
{
    $_SESSION['case_form_error'] = $message;

    header('Location: ' . BASE_URL . '/pages/case/case.php');
    exit;
}

/**
 * Redirects to the case status page with a flash error message.
 */
function redirectWithCaseStatusError(string $message): never
{
    $_SESSION['case_status_error'] = $message;
    redirectToCaseStatus();
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirectToCaseStatus();
}

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
    redirectToCaseFormWithError(
        'ابتدا اطلاعات یک کیس را ثبت کنید.'
    );
}

try {
    $caseContext = findRegisteredCaseStatusContext(
        $pdo,
        $registeredCaseId,
        $authenticatedUserId,
        $receiverEmployeeId
    );

    $itNumber = (string) $caseContext['it_number'];
    $caseType = determineCaseType((int) $caseContext['generation']);
    $submittedToken = $_POST['csrf_token'] ?? null;
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (
        !is_string($submittedToken)
        || !is_string($sessionToken)
        || trim($submittedToken) === ''
        || $sessionToken === ''
        || !hash_equals($sessionToken, trim($submittedToken))
    ) {
        throw new InvalidArgumentException(
            'درخواست نامعتبر است؛ صفحه را دوباره بارگذاری کنید.'
        );
    }

    $caseStatus = validateSubmittedCaseStatus(
        $_POST['case_status'] ?? null,
        $caseType
    );

    upsertCaseStatus($pdo, $itNumber, $caseType, $caseStatus);

    $_SESSION['case_status_success'] = 'وضعیت کیس با موفقیت ثبت شد.';
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    redirectToCaseStatus();
} catch (InvalidArgumentException $exception) {
    redirectWithCaseStatusError($exception->getMessage());
} catch (Throwable $exception) {
    error_log('Save case status error: ' . $exception->getMessage());

    redirectWithCaseStatusError(
        'هنگام ثبت وضعیت کیس خطایی رخ داد.'
    );
}
