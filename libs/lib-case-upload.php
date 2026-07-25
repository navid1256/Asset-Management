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
 * Stores up to three warranty files before storing the delivery sheet.
 */
function uploadCaseFiles(array $files, int $storageCount): array
{
    // ============== FILE UPLOADS ==============
    $uploadedPaths = [];
    $warrantyDirectory = BASE_PATH . '/storage/uploads/warranties';
    $warrantyPathPrefix = 'storage/uploads/warranties';

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

        // RAM Warranty
        $ramWarrantyPath = storeOptionalCaseUploadedFile(
            $files['ram_warranty_file'] ?? [],
            $warrantyDirectory,
            $warrantyPathPrefix
        );
        if ($ramWarrantyPath !== null) {
            $uploadedPaths[] = $ramWarrantyPath;
        }

        // Storage Device Warranty (array)
        $storageWarrantyPaths = [];
        $storageWarrantyFiles = $files['storage_warranty_files'] ?? [];
        $storageWarrantyNames = $storageWarrantyFiles['name'] ?? [];

        if (!is_array($storageWarrantyNames)) {
            throw new InvalidArgumentException(
                'ساختار فایل‌های گارانتی Storage معتبر نیست.'
            );
        }

        if (count($storageWarrantyNames) > $storageCount) {
            throw new InvalidArgumentException(
                'تعداد برگه‌های گارانتی Storage بیشتر از تعداد هاردها است.'
            );
        }

        for ($index = 0; $index < $storageCount; $index++) {
            $storageWarrantyFile = [
                'name' => $storageWarrantyNames[$index] ?? '',
                'tmp_name' => $storageWarrantyFiles['tmp_name'][$index] ?? '',
                'error' => $storageWarrantyFiles['error'][$index]
                    ?? UPLOAD_ERR_NO_FILE,
                'size' => $storageWarrantyFiles['size'][$index] ?? 0,
            ];
            $storageWarrantyPath = storeOptionalCaseUploadedFile(
                $storageWarrantyFile,
                $warrantyDirectory,
                $warrantyPathPrefix
            );

            if ($storageWarrantyPath !== null) {
                $storageWarrantyPaths[$index] = $storageWarrantyPath;
                $uploadedPaths[] = $storageWarrantyPath;
            }
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
            'ram_warranty' => $ramWarrantyPath,
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
