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

function createCaseMotherboard(
    PDO $pdo,
    string $itNumber,
    string $brand,
    string $model,
    ?string $warrantyFilePath
): void {
    $statement = $pdo->prepare(
        'INSERT INTO case_motherboards (
            it_number,
            brand,
            model,
            warranty_file_path
        ) VALUES (
            :it_number,
            :brand,
            :model,
            :warranty_file_path
        )'
    );

    $statement->execute([
        'it_number' => $itNumber,
        'brand' => $brand,
        'model' => $model,
        'warranty_file_path' => $warrantyFilePath
    ]);
}

function createCaseGpu(
    PDO $pdo,
    string $itNumber,
    string $gpuType,
    ?string $brand,
    ?string $model,
    ?string $memoryGB,
    ?string $warrantyFilePath
): void {
    $statement = $pdo->prepare(
        'INSERT INTO case_gpus (
            it_number,
            brand,
            gpu_type,
            model,
            memory_gb,
            warranty_file_path
        ) VALUES (
            :it_number,
            :brand,
            :gpu_type,
            :model,
            :memory_gb,
            :warranty_file_path
        )'
    );

    $statement->execute([
        'it_number' => $itNumber,
        'brand' => $brand,
        'gpu_type' => $gpuType,
        'model' => $model,
        'memory_gb' => $memoryGB,
        'warranty_file_path' => $warrantyFilePath,
    ]);
}

/**
 * Stores one grouped RAM configuration and returns its database identifier.
 */
function createCaseRam(
    PDO $pdo,
    string $itNumber,
    string $brand,
    int $ramCount,
    string $model,
    string $ramType,
    string $moduleCapacityGB,
    int $speedMHz
): int {
    $statement = $pdo->prepare(
        'INSERT INTO case_rams (
            it_number,
            brand,
            ram_count,
            model,
            ram_type,
            module_capacity_gb,
            speed_mhz
        ) VALUES (
            :it_number,
            :brand,
            :ram_count,
            :model,
            :ram_type,
            :module_capacity_gb,
            :speed_mhz
        )'
    );

    $statement->execute([
        'it_number' => $itNumber,
        'brand' => $brand,
        'ram_count' => $ramCount,
        'model' => $model,
        'ram_type' => $ramType,
        'module_capacity_gb' => $moduleCapacityGB,
        'speed_mhz' => $speedMHz,
    ]);

    return (int) $pdo->lastInsertId();
}

/**
 * Stores the warranty file assigned to one physical RAM module.
 */
function createCaseRamWarranty(
    PDO $pdo,
    int $caseRamId,
    int $moduleNumber,
    string $warrantyFilePath
): void {
    $statement = $pdo->prepare(
        'INSERT INTO case_ram_warranties (
            case_ram_id,
            module_number,
            warranty_file_path
        ) VALUES (
            :case_ram_id,
            :module_number,
            :warranty_file_path
        )'
    );

    $statement->execute([
        'case_ram_id' => $caseRamId,
        'module_number' => $moduleNumber,
        'warranty_file_path' => $warrantyFilePath,
    ]);
}

function createCaseStorageGroup(
    PDO $pdo,
    string $itNumber,
    int $storageCount
): void {
    $statement = $pdo->prepare(
        'INSERT INTO case_storage_groups (
            it_number,
            storage_count
        ) VALUES (
            :it_number,
            :storage_count
        )'
    );

    $statement->execute([
        'it_number' => $itNumber,
        'storage_count' => $storageCount,
    ]);
}

function createStorageDevice(
    PDO $pdo,
    string $itNumber,
    int $deviceNumber,
    string $storageType,
    string $brand,
    string $model,
    int $capacityGB,
    ?string $warrantyFilePath
): void {
    $statement = $pdo->prepare(
        'INSERT INTO case_storage_devices (
            it_number,
            device_number,
            storage_type,
            brand,
            model,
            capacity_gb,
            warranty_file_path
        ) VALUES (
            :it_number,
            :device_number,
            :storage_type,
            :brand,
            :model,
            :capacity_gb,
            :warranty_file_path
        )'
    );

    $statement->execute([
        'it_number' => $itNumber,
        'device_number' => $deviceNumber,
        'storage_type' => $storageType,
        'brand' => $brand,
        'model' => $model,
        'capacity_gb' => $capacityGB,
        'warranty_file_path' => $warrantyFilePath,
    ]);
}


function createCaseWriter(
    PDO $pdo,
    string $itNumber,
    int $writerEnabled,
    ?string $writerType,
    ?string $brand,
    ?string $model,
    ?string $warrantyFilePath
): void {
    $statement = $pdo->prepare(
        'INSERT INTO case_writers (
            it_number,
            writer_enabled,
            writer_type,
            brand,
            model,
            warranty_file_path
        ) VALUES (
            :it_number,
            :writer_enabled,
            :writer_type,
            :brand,
            :model,
            :warranty_file_path
        )'
    );

    $statement->execute([
        'it_number' => $itNumber,
        'writer_enabled' => $writerEnabled,
        'writer_type' => $writerType,
        'brand' => $brand,
        'model' => $model,
        'warranty_file_path' => $warrantyFilePath,
    ]);
}

function createCasePowerSupply(
    PDO $pdo,
    string $itNumber,
    string $brand,
    string $model,
    int $wattageW,
    ?string $warrantyFilePath
): void {
    $statement = $pdo->prepare(
        'INSERT INTO case_power_supplies (
            it_number,
            brand,
            model,
            wattage_w,
            warranty_file_path
        ) VALUES (
            :it_number,
            :brand,
            :model,
            :wattage_w,
            :warranty_file_path
        )'
    );

    $statement->execute([
        'it_number' => $itNumber,
        'brand' => $brand,
        'model' => $model,
        'wattage_w' => $wattageW,
        'warranty_file_path' => $warrantyFilePath,
    ]);
}

function createCaseChassis(
    PDO $pdo,
    string $itNumber,
    string $brand,
    string $model,
    ?string $warrantyFilePath
): void {
    $statement = $pdo->prepare(
        'INSERT INTO case_chassis (
            it_number,
            brand,
            model,
            warranty_file_path
        ) VALUES (
            :it_number,
            :brand,
            :model,
            :warranty_file_path
        )'
    );

    $statement->execute([
        'it_number' => $itNumber,
        'brand' => $brand,
        'model' => $model,
        'warranty_file_path' => $warrantyFilePath,
    ]);
}

function createCaseStatus(
    PDO $pdo,
    string $itNumber,
    string $caseType,
    string $caseStatus
): void {
    $statement = $pdo->prepare(
        'INSERT INTO case_statuses (
            it_number,
            case_type,
            case_status
        ) VALUES (
            :it_number,
            :case_type,
            :case_status
        )'
    );

    $statement->execute([
        'it_number' => $itNumber,
        'case_type' => $caseType,
        'case_status' => $caseStatus,
    ]);
}
