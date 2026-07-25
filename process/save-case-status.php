<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap/constants.php';
require_once __DIR__ . '/../bootstrap/database.php';

const NEW_CASE_MIN_CPU_GENERATION = 12;

function redirectToCaseStatus(): never
{
    header(
        'Location: '
            . BASE_URL
            . '/pages/case/case-status.php'
    );
    exit;
}

function redirectToCaseFormWithError(string $message): never
{
    $_SESSION['case_form_error'] = $message;

    header(
        'Location: '
            . BASE_URL
            . '/pages/case/case.php'
    );
    exit;
}

function redirectWithCaseStatusError(string $message): never
{
    $_SESSION['case_status_error'] = $message;
    redirectToCaseStatus();
}

function readStatusStringInput(mixed $value): string
{
    if (!is_string($value)) {
        throw new InvalidArgumentException(
            'ساختار اطلاعات فرم معتبر نیست.'
        );
    }

    return trim($value);
}

function findRegisteredCaseForStatus(
    PDO $pdo,
    int $caseId,
    int $createdByUserId,
    int $receiverEmployeeId
): array {
    $statement = $pdo->prepare(
        'SELECT
            case_numbers.it_number,
            case_cpus.generation
        FROM case_numbers
        INNER JOIN case_cpus
            ON case_cpus.it_number = case_numbers.it_number
        WHERE case_numbers.id = :case_id
          AND case_numbers.created_by_user_id = :created_by_user_id
          AND case_numbers.receiver_employee_id = :receiver_employee_id
        LIMIT 1'
    );

    $statement->execute([
        'case_id' => $caseId,
        'created_by_user_id' => $createdByUserId,
        'receiver_employee_id' => $receiverEmployeeId,
    ]);

    $caseRecord = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$caseRecord) {
        throw new RuntimeException(
            'اطلاعات کیس یا CPU آن پیدا نشد.'
        );
    }

    return $caseRecord;
}

$authenticatedUserId = (int) (
    $_SESSION['authenticated_user_id'] ?? 0
);
$receiverEmployeeId = (int) (
    $_SESSION['receiver_user_id'] ?? 0
);
$registeredCaseId = (int) (
    $_SESSION['registered_case_id'] ?? 0
);

if ($authenticatedUserId < 1) {
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

if ($registeredCaseId < 1) {
    redirectToCaseFormWithError(
        'ابتدا اطلاعات یک کیس را ثبت کنید.'
    );
}

try {
    $caseRecord = findRegisteredCaseForStatus(
        $pdo,
        $registeredCaseId,
        $authenticatedUserId,
        $receiverEmployeeId
    );

    $itNumber = (string) $caseRecord['it_number'];
    $cpuGeneration = (int) $caseRecord['generation'];

    if ($cpuGeneration < 1) {
        throw new RuntimeException(
            'نسل CPU ثبت‌شده معتبر نیست.'
        );
    }

    $caseType = $cpuGeneration >= NEW_CASE_MIN_CPU_GENERATION
        ? 'new'
        : 'old';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $executedFile = realpath(
            (string) ($_SERVER['SCRIPT_FILENAME'] ?? '')
        );

        if ($executedFile === realpath(__FILE__)) {
            redirectToCaseStatus();
        }

        return;
    }

    $submittedToken = readStatusStringInput(
        $_POST['csrf_token']
            ?? $_POST['csrf-token']
            ?? ''
    );
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (
        !is_string($sessionToken)
        || $submittedToken === ''
        || $sessionToken === ''
        || !hash_equals($sessionToken, $submittedToken)
    ) {
        throw new InvalidArgumentException(
            'درخواست نامعتبر است؛ صفحه را دوباره بارگذاری کنید.'
        );
    }

    $caseStatus = readStatusStringInput(
        $_POST['case_status']
            ?? $_POST['caseStatus']
            ?? $_POST['oldCaseStatus']
            ?? ''
    );

    $allowedStatuses = $caseType === 'new'
        ? ['in_use', 'unused']
        : ['in_use', 'retired'];

    if (!in_array($caseStatus, $allowedStatuses, true)) {
        throw new InvalidArgumentException(
            'وضعیت انتخاب‌شده برای این کیس مجاز نیست.'
        );
    }

    $statusStatement = $pdo->prepare(
        'INSERT INTO case_statuses (
            it_number,
            case_type,
            case_status
        ) VALUES (
            :it_number,
            :case_type,
            :case_status
        )
        ON DUPLICATE KEY UPDATE
            case_type = :updated_case_type,
            case_status = :updated_case_status,
            updated_at = CURRENT_TIMESTAMP'
    );

    $statusStatement->execute([
        'it_number' => $itNumber,
        'case_type' => $caseType,
        'case_status' => $caseStatus,
        'updated_case_type' => $caseType,
        'updated_case_status' => $caseStatus,
    ]);

    $_SESSION['case_status_success'] =
        'وضعیت کیس با موفقیت ثبت شد.';
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    redirectToCaseStatus();
} catch (InvalidArgumentException $exception) {
    redirectWithCaseStatusError($exception->getMessage());
} catch (Throwable $exception) {
    error_log(
        'Save case status error: '
            . $exception->getMessage()
    );

    redirectWithCaseStatusError(
        'هنگام ثبت وضعیت کیس خطایی رخ داد.'
    );
}
