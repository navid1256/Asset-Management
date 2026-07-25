<?php

declare(strict_types=1);

const NEW_CASE_MIN_CPU_GENERATION = 12;

/**
 * Finds the registered case that belongs to the current operator and receiver.
 */
function findRegisteredCaseStatusContext(
    PDO $pdo,
    int $caseId,
    int $createdByUserId,
    int $receiverEmployeeId
): array {
    $statement = $pdo->prepare(
        'SELECT
            case_numbers.it_number,
            case_cpus.generation,
            case_statuses.case_status
        FROM case_numbers
        INNER JOIN case_cpus
            ON case_cpus.it_number = case_numbers.it_number
        LEFT JOIN case_statuses
            ON case_statuses.it_number = case_numbers.it_number
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

    $caseContext = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$caseContext) {
        throw new RuntimeException(
            'اطلاعات کیس یا CPU آن پیدا نشد.'
        );
    }

    return $caseContext;
}

/**
 * Determines whether a case is new or old from its CPU generation.
 */
function determineCaseType(int $cpuGeneration): string
{
    if ($cpuGeneration < 1) {
        throw new RuntimeException(
            'نسل CPU ثبت شده معتبر نیست.'
        );
    }

    return $cpuGeneration >= NEW_CASE_MIN_CPU_GENERATION ? 'new' : 'old';
}

/**
 * Returns the status values allowed for the supplied case type.
 */
function getAllowedCaseStatuses(string $caseType): array
{
    return match ($caseType) {
        'new' => ['in_use', 'unused'],
        'old' => ['in_use', 'retired'],
        default => throw new InvalidArgumentException(
            'نوع کیس معتبر نیست.'
        ),
    };
}

/**
 * Validates and returns the status submitted by the case status form.
 */
function validateSubmittedCaseStatus(mixed $submittedStatus, string $caseType): string 
{
    if (!is_string($submittedStatus)) {
        throw new InvalidArgumentException(
            'ساختار وضعیت ارسال شده معتبر نیست.'
        );
    }

    $caseStatus = trim($submittedStatus);
    $allowedStatuses = getAllowedCaseStatuses($caseType);

    if (!in_array($caseStatus, $allowedStatuses, true)) {
        throw new InvalidArgumentException(
            'وضعیت انتخاب شده برای این کیس مجاز نیست.'
        );
    }

    return $caseStatus;
}

/**
 * Creates or updates the status record associated with an IT number.
 */
function upsertCaseStatus(PDO $pdo, string $itNumber, string $caseType, string $caseStatus): void
{
    $statement = $pdo->prepare(
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

    $statement->execute([
        'it_number' => $itNumber,
        'case_type' => $caseType,
        'case_status' => $caseStatus,
        'updated_case_type' => $caseType,
        'updated_case_status' => $caseStatus,
    ]);
}
