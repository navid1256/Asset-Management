<?php

// ============== FILE UPLOADS ==============
$uploadedPaths = [];

$deliverySheetPath = storeRequiredUploadedFile(
    $_FILES['delivery_sheet'] ?? [],
    BASE_PATH . '/storage/uploads/delivery-sheets',
    'storage/uploads/delivery-sheets'
);
$uploadedPaths[] = $deliverySheetPath;

// CPU Warranty
$cpuWarrantyPath = null;
if (!empty($_FILES['cpu_warranty_file']['name'])) {
    try {
        $cpuWarrantyPath = storeRequiredUploadedFile(
            $_FILES['cpu_warranty_file'] ?? [],
            BASE_PATH . '/storage/uploads/warranties',
            'storage/uploads/warranties'
        );
        $uploadedPaths[] = $cpuWarrantyPath;
    } catch (Throwable) {
        // Optional file
    }
}

// Motherboard Warranty
$motherboardWarrantyPath = null;
if (!empty($_FILES['motherboard_warranty_file']['name'])) {
    try {
        $motherboardWarrantyPath = storeRequiredUploadedFile(
            $_FILES['motherboard_warranty_file'] ?? [],
            BASE_PATH . '/storage/uploads/warranties',
            'storage/uploads/warranties'
        );
        $uploadedPaths[] = $motherboardWarrantyPath;
    } catch (Throwable) {
        // Optional file
    }
}

// GPU Warranty
$gpuWarrantyPath = null;
if (!empty($_FILES['gpu_warranty_file']['name'])) {
    try {
        $gpuWarrantyPath = storeRequiredUploadedFile(
            $_FILES['gpu_warranty_file'] ?? [],
            BASE_PATH . '/storage/uploads/warranties',
            'storage/uploads/warranties'
        );
        $uploadedPaths[] = $gpuWarrantyPath;
    } catch (Throwable) {
        // Optional file
    }
}

// RAM Warranty
$ramWarrantyPath = null;
if (!empty($_FILES['ram_warranty_file']['name'])) {
    try {
        $ramWarrantyPath = storeRequiredUploadedFile(
            $_FILES['ram_warranty_file'] ?? [],
            BASE_PATH . '/storage/uploads/warranties',
            'storage/uploads/warranties'
        );
        $uploadedPaths[] = $ramWarrantyPath;
    } catch (Throwable) {
        // Optional file
    }
}

// Storage Device Warranty (array)
$storageWarrantyPaths = [];
if (!empty($_FILES['storage_warranty_files']['name'])) {
    for ($i = 0; $i < count($_FILES['storage_warranty_files']['name']); $i++) {
        if (!empty($_FILES['storage_warranty_files']['name'][$i])) {
            try {
                $file = [
                    'name' => $_FILES['storage_warranty_files']['name'][$i],
                    'tmp_name' => $_FILES['storage_warranty_files']['tmp_name'][$i],
                    'error' => $_FILES['storage_warranty_files']['error'][$i],
                    'size' => $_FILES['storage_warranty_files']['size'][$i],
                ];
                $path = storeRequiredUploadedFile(
                    $file,
                    BASE_PATH . '/storage/uploads/warranties',
                    'storage/uploads/warranties'
                );
                $storageWarrantyPaths[$i] = $path;
                $uploadedPaths[] = $path;
            } catch (Throwable) {
                // Optional file
            }
        }
    }
}

// Writer Warranty
$writerWarrantyPath = null;
if (!empty($_FILES['writer_warranty_file']['name'])) {
    try {
        $writerWarrantyPath = storeRequiredUploadedFile(
            $_FILES['writer_warranty_file'] ?? [],
            BASE_PATH . '/storage/uploads/warranties',
            'storage/uploads/warranties'
        );
        $uploadedPaths[] = $writerWarrantyPath;
    } catch (Throwable) {
        // Optional file
    }
}

// Power Supply Warranty
$powerSupplyWarrantyPath = null;
if (!empty($_FILES['power_warranty_file']['name'])) {
    try {
        $powerSupplyWarrantyPath = storeRequiredUploadedFile(
            $_FILES['power_warranty_file'] ?? [],
            BASE_PATH . '/storage/uploads/warranties',
            'storage/uploads/warranties'
        );
        $uploadedPaths[] = $powerSupplyWarrantyPath;
    } catch (Throwable) {
        // Optional file
    }
}

// Chassis Warranty
$chassisWarrantyPath = null;
if (!empty($_FILES['case_warranty_file']['name'])) {
    try {
        $chassisWarrantyPath = storeRequiredUploadedFile(
            $_FILES['case_warranty_file'] ?? [],
            BASE_PATH . '/storage/uploads/warranties',
            'storage/uploads/warranties'
        );
        $uploadedPaths[] = $chassisWarrantyPath;
    } catch (Throwable) {
        // Optional file
    }
}
