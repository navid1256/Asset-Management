<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/constants.php';
require_once __DIR__ . '/lib-upload.php';

/**
 * Stores an optional case file when the user has selected one.
 */
function storeOptionalCaseUploadedFile(
    array $file,
    string $destinationDirectory,
    string $storedPathPrefix
): ?string {
    $uploadError = $file['error'] ?? UPLOAD_ERR_NO_FILE;

    if ($uploadError === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    return storeRequiredUploadedFile(
        $file,
        $destinationDirectory,
        $storedPathPrefix
    );
}

/**
 * Stores optional repeated warranty files while preserving their row indexes.
 */
function storeOptionalRepeatedCaseUploadedFiles(
    array $uploadedFileGroup,
    int $expectedCount,
    string $componentName,
    string $destinationDirectory,
    string $storedPathPrefix
): array {
    $fileParts = [];

    foreach (['name', 'tmp_name', 'error', 'size'] as $partName) {
        $partValues = $uploadedFileGroup[$partName] ?? [];

        if (!is_array($partValues)) {
            throw new InvalidArgumentException(
                "ساختار فایل‌های گارانتی {$componentName} معتبر نیست."
            );
        }

        $fileParts[$partName] = $partValues;
    }

    if (count($fileParts['name']) > $expectedCount) {
        throw new InvalidArgumentException(
            "تعداد برگه‌های گارانتی {$componentName} بیشتر از تعداد انتخاب‌شده است."
        );
    }

    $storedPaths = [];

    for ($index = 0; $index < $expectedCount; $index++) {
        $uploadedFile = [
            'name' => $fileParts['name'][$index] ?? '',
            'tmp_name' => $fileParts['tmp_name'][$index] ?? '',
            'error' => $fileParts['error'][$index]
                ?? UPLOAD_ERR_NO_FILE,
            'size' => $fileParts['size'][$index] ?? 0,
        ];
        $storedPath = storeOptionalCaseUploadedFile(
            $uploadedFile,
            $destinationDirectory,
            $storedPathPrefix
        );

        if ($storedPath !== null) {
            $storedPaths[$index] = $storedPath;
        }
    }

    return $storedPaths;
}

/**
 * Stores component warranty files before storing the required delivery sheet.
 */
function uploadCaseFiles(
    array $files,
    int $ramCount,
    int $storageCount
): array
{
    // ============== FILE UPLOADS ==============
    $uploadedPaths = [];
    $warrantyDirectory = BASE_PATH . '/storage/uploads/warranties';
    $warrantyPathPrefix = 'storage/uploads/warranties';

    if ($ramCount < 1 || $ramCount > 4) {
        throw new InvalidArgumentException(
            'تعداد RAM برای بارگذاری گارانتی معتبر نیست.'
        );
    }

    if ($storageCount < 1 || $storageCount > 3) {
        throw new InvalidArgumentException(
            'تعداد Storage برای بارگذاری گارانتی معتبر نیست.'
        );
    }

    try {
        // CPU Warranty
        $cpuWarrantyPath = storeOptionalCaseUploadedFile(
            $files['cpu_warranty_file'] ?? [],
            $warrantyDirectory,
            $warrantyPathPrefix
        );
        if ($cpuWarrantyPath !== null) {
            $uploadedPaths[] = $cpuWarrantyPath;
        }

        // Motherboard Warranty
        $motherboardWarrantyPath = storeOptionalCaseUploadedFile(
            $files['motherboard_warranty_file'] ?? [],
            $warrantyDirectory,
            $warrantyPathPrefix
        );
        if ($motherboardWarrantyPath !== null) {
            $uploadedPaths[] = $motherboardWarrantyPath;
        }

        // GPU Warranty
        $gpuWarrantyPath = storeOptionalCaseUploadedFile(
            $files['gpu_warranty_file'] ?? [],
            $warrantyDirectory,
            $warrantyPathPrefix
        );
        if ($gpuWarrantyPath !== null) {
            $uploadedPaths[] = $gpuWarrantyPath;
        }

        // RAM Warranties (array)
        $ramWarrantyPaths = storeOptionalRepeatedCaseUploadedFiles(
            $files['ram_warranty_files'] ?? [],
            $ramCount,
            'RAM',
            $warrantyDirectory,
            $warrantyPathPrefix
        );
        foreach ($ramWarrantyPaths as $ramWarrantyPath) {
            $uploadedPaths[] = $ramWarrantyPath;
        }

        // Storage Device Warranties (array)
        $storageWarrantyPaths = storeOptionalRepeatedCaseUploadedFiles(
            $files['storage_warranty_files'] ?? [],
            $storageCount,
            'Storage',
            $warrantyDirectory,
            $warrantyPathPrefix
        );
        foreach ($storageWarrantyPaths as $storageWarrantyPath) {
            $uploadedPaths[] = $storageWarrantyPath;
        }

        // Writer Warranty
        $writerWarrantyPath = storeOptionalCaseUploadedFile(
            $files['writer_warranty_file'] ?? [],
            $warrantyDirectory,
            $warrantyPathPrefix
        );
        if ($writerWarrantyPath !== null) {
            $uploadedPaths[] = $writerWarrantyPath;
        }

        // Power Supply Warranty
        $powerSupplyWarrantyPath = storeOptionalCaseUploadedFile(
            $files['power_warranty_file'] ?? [],
            $warrantyDirectory,
            $warrantyPathPrefix
        );
        if ($powerSupplyWarrantyPath !== null) {
            $uploadedPaths[] = $powerSupplyWarrantyPath;
        }

        // Chassis Warranty
        $chassisWarrantyPath = storeOptionalCaseUploadedFile(
            $files['case_warranty_file'] ?? [],
            $warrantyDirectory,
            $warrantyPathPrefix
        );
        if ($chassisWarrantyPath !== null) {
            $uploadedPaths[] = $chassisWarrantyPath;
        }

        $deliverySheetPath = storeRequiredUploadedFile(
            $files['delivery_sheet'] ?? [],
            BASE_PATH . '/storage/uploads/delivery-sheets',
            'storage/uploads/delivery-sheets'
        );
        $uploadedPaths[] = $deliverySheetPath;

        return [
            'delivery_sheet' => $deliverySheetPath,
            'cpu_warranty' => $cpuWarrantyPath,
            'motherboard_warranty' => $motherboardWarrantyPath,
            'gpu_warranty' => $gpuWarrantyPath,
            'ram_warranties' => $ramWarrantyPaths,
            'storage_warranties' => $storageWarrantyPaths,
            'writer_warranty' => $writerWarrantyPath,
            'power_supply_warranty' => $powerSupplyWarrantyPath,
            'chassis_warranty' => $chassisWarrantyPath,
            'uploaded_paths' => $uploadedPaths,
        ];
    } catch (Throwable $exception) {
        cleanupUploadedPaths($uploadedPaths);
        throw $exception;
    }
}
