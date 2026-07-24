<?php

declare(strict_types=1);

function createCaseNumber(
    PDO $pdo,
    string $itNumber,
    ?string $assetNumber,
    int $receiverEmployeeId,
    int $createdByUserId,
    string $deliverySheetPath
): int {
    $statement = $pdo->prepare(
        'INSERT INTO case_numbers (
            it_number,
            asset_number,
            receiver_employee_id,
            created_by_user_id,
            delivery_sheet_path
        ) VALUES (
            :it_number,
            :asset_number,
            :receiver_employee_id,
            :created_by_user_id,
            :delivery_sheet_path
        )'
    );

    $statement->execute([
        'it_number' => $itNumber,
        'asset_number' => $assetNumber,
        'receiver_employee_id' => $receiverEmployeeId,
        'created_by_user_id' => $createdByUserId,
        'delivery_sheet_path' => $deliverySheetPath,
    ]);

    return (int) $pdo->lastInsertId();
}

function createCaseCpu(
    PDO $pdo,
    string $itNumber,
    string $brand,
    int $generation,
    string $model,
    string $speedGhz,
    ?string $warrantyFilePath
): void {
    $statement = $pdo->prepare(
        'INSERT INTO case_cpus (
            it_number,
            brand,
            generation,
            model,
            speed_ghz,
            warranty_file_path
        ) VALUES (
            :it_number,
            :brand,
            :generation,
            :model,
            :speed_ghz,
            :warranty_file_path
        )'
    );

    $statement->execute([
        'it_number' => $itNumber,
        'brand' => $brand,
        'generation' => $generation,
        'model' => $model,
        'speed_ghz' => $speedGhz,
        'warranty_file_path' => $warrantyFilePath,
    ]);
}
