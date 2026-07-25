<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap/constants.php';
require_once __DIR__ . '/../bootstrap/database.php';
require_once __DIR__ . '/../libs/lib-input.php';
require_once __DIR__ . '/../libs/lib-case.php';
require_once __DIR__ . '/../libs/lib-upload.php';

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

    $assetNumber = $assetNumber === '' ? null : $assetNumber;

    try {
        // ============== FILE UPLOADS ==============
        include __DIR__ . '/case-waranty-upload-handler.php';

        // ============== CPU DATA ==============
        $cpuBrand = trim($_POST['cpu_brand'] ?? '');
        $cpuGeneration = (int) ($_POST['cpu_generation'] ?? 0);
        $cpuModel = trim($_POST['cpu_model'] ?? '');
        $cpuSpeedGhz = trim($_POST['cpu_speed_ghz'] ?? '');

        if (!$cpuBrand || !$cpuModel || !$cpuSpeedGhz || $cpuGeneration < 1) {
            throw new InvalidArgumentException(
                'اطلاعات CPU کامل نیست.'
            );
        }

        // ============== MOTHERBOARD DATA ==============
        $motherboardBrand = trim($_POST['motherboard_brand'] ?? '');
        $motherboardModel = trim($_POST['motherboard_model'] ?? '');

        if (!$motherboardBrand || !$motherboardModel) {
            throw new InvalidArgumentException(
                'اطلاعات Motherboard کامل نیست.'
            );
        }

        // ============== GPU DATA ==============
        $gpuType = trim($_POST['gpu_type'] ?? '');
        $gpuBrand = trim($_POST['gpu_brand'] ?? '');
        $gpuModel = trim($_POST['gpu_model'] ?? '');
        $gpuMemoryGB = trim($_POST['gpu_memory_gb'] ?? '');

        if (!$gpuType || ($gpuType === 'internal' && (!$gpuBrand || !$gpuModel || !$gpuMemoryGB))) {
            throw new InvalidArgumentException(
                'اطلاعات GPU کامل نیست.'
            );
        }

        // ============== RAM DATA ==============
        $ramCount = (int) ($_POST['ram_count'] ?? 0);
        $ramBrand = trim($_POST['ram_brand'] ?? '');
        $ramModel = trim($_POST['ram_model'] ?? '');
        $ramType = trim($_POST['ram_type'] ?? '');
        $ramModuleCapacityGB = trim($_POST['ram_capacity_gb'] ?? '');
        $ramSpeedMHz = (int) ($_POST['ram_speed_mhz'] ?? 0);

        if (!$ramBrand || !$ramModel || !$ramType || !$ramModuleCapacityGB || $ramCount < 1 || $ramSpeedMHz < 1) {
            throw new InvalidArgumentException(
                'اطلاعات RAM کامل نیست.'
            );
        }

        // ============== STORAGE GROUP DATA ==============
        $storageCount = (int) ($_POST['storage_count'] ?? 0);

        if ($storageCount < 1 || $storageCount > 3) {
            throw new InvalidArgumentException(
                'تعداد Storage باید بین 1 و 3 باشد.'
            );
        }

        // ============== STORAGE DEVICE DATA ==============
        $storageDevices = [];
        for ($i = 1; $i <= $storageCount; $i++) {
            $deviceNumber = $i;
            $storageType = trim($_POST["storage_type"][$i - 1] ?? '');
            $storageBrand = trim($_POST["storage_brand"][$i - 1] ?? '');
            $storageModel = trim($_POST["storage_model"][$i - 1] ?? '');
            $storageCapacityGB = (int) ($_POST["storage_capacity_gb"][$i - 1] ?? 0);

            if (!$storageType || !$storageBrand || !$storageModel || $storageCapacityGB < 1) {
                throw new InvalidArgumentException(
                    "اطلاعات Storage $i کامل نیست."
                );
            }

            $storageWarrantyPath = $storageWarrantyPaths[$i - 1] ?? null;

            $storageDevices[] = [
                'device_number' => $deviceNumber,
                'storage_type' => $storageType,
                'brand' => $storageBrand,
                'model' => $storageModel,
                'capacity_gb' => $storageCapacityGB,
                'warranty_path' => $storageWarrantyPath,
            ];
        }

        // ============== WRITER DATA ==============
        $writerEnabled = (int) ($_POST['writer_enabled'] ?? 0);
        $writerType = null;
        $writerBrand = null;
        $writerModel = null;

        if ($writerEnabled) {
            $writerType = trim($_POST['writer_type'] ?? '');
            $writerBrand = trim($_POST['writer_brand'] ?? '');
            $writerModel = trim($_POST['writer_model'] ?? '');

            if (!$writerType || !$writerBrand || !$writerModel) {
                throw new InvalidArgumentException(
                    'اطلاعات Writer کامل نیست.'
                );
            }
        }

        // ============== POWER SUPPLY DATA ==============
        $powerSupplyBrand = trim($_POST['power_brand'] ?? '');
        $powerSupplyModel = trim($_POST['power_model'] ?? '');
        $powerSupplyWattage = (int) ($_POST['power_wattage_w'] ?? 0);

        if (!$powerSupplyBrand || !$powerSupplyModel || $powerSupplyWattage < 1) {
            throw new InvalidArgumentException(
                'اطلاعات Power Supply کامل نیست.'
            );
        }

        // ============== CHASSIS DATA ==============
        $chassisBrand = trim($_POST['case_brand'] ?? '');
        $chassisModel = trim($_POST['case_model'] ?? '');

        if (!$chassisBrand || !$chassisModel) {
            throw new InvalidArgumentException(
                'اطلاعات Chassis کامل نیست.'
            );
        }

        // ============== CASE STATUS DATA ==============
        $caseType = trim($_POST['case_type'] ?? '');
        $caseStatus = trim($_POST['case_status'] ?? '');

        if (!$caseType || !$caseStatus) {
            throw new InvalidArgumentException(
                'اطلاعات وضعیت کیس کامل نیست.'
            );
        }

        $pdo->beginTransaction();

        $caseId = createCaseNumber(
            $pdo,
            $itNumber,
            $assetNumber,
            $receiverEmployeeId,
            $createdByUserId,
            $deliverySheetPath
        );

        createCaseCpu(
            $pdo,
            $itNumber,
            $cpuBrand,
            $cpuGeneration,
            $cpuModel,
            $cpuSpeedGhz,
            $cpuWarrantyPath
        );

        createCaseMotherboard(
            $pdo,
            $itNumber,
            $motherboardBrand,
            $motherboardModel,
            $motherboardWarrantyPath
        );

        createCaseGpu(
            $pdo,
            $itNumber,
            $gpuBrand,
            $gpuType,
            $gpuModel,
            $gpuMemoryGB,
            $gpuWarrantyPath
        );

        createCaseRam(
            $pdo,
            $itNumber,
            $ramBrand,
            $ramCount,
            $ramModel,
            $ramType,
            $ramModuleCapacityGB,
            $ramSpeedMHz,
            $ramWarrantyPath
        );

        createCaseStorageGroup(
            $pdo,
            $itNumber,
            $storageCount
        );

        foreach ($storageDevices as $device) {
            createStorageDevice(
                $pdo,
                $itNumber,
                $device['device_number'],
                $device['storage_type'],
                $device['brand'],
                $device['model'],
                $device['capacity_gb'],
                $device['warranty_path']
            );
        }

        createCaseWriter(
            $pdo,
            $itNumber,
            $writerEnabled,
            $writerType,
            $writerBrand,
            $writerModel,
            $writerWarrantyPath
        );

        createCasePowerSupply(
            $pdo,
            $itNumber,
            $powerSupplyBrand,
            $powerSupplyModel,
            $powerSupplyWattage,
            $powerSupplyWarrantyPath
        );

        createCaseChassis(
            $pdo,
            $itNumber,
            $chassisBrand,
            $chassisModel,
            $chassisWarrantyPath
        );

        createCaseStatus(
            $pdo,
            $itNumber,
            $caseType,
            $caseStatus
        );

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

            // Delete all uploaded files
            foreach ($uploadedPaths as $path) {
                $absolutePath = BASE_PATH
                    . DIRECTORY_SEPARATOR
                    . str_replace('/', DIRECTORY_SEPARATOR, $path);

                if (is_file($absolutePath) && !unlink($absolutePath)) {
                    error_log('Failed to delete uploaded file: ' . $absolutePath);
                }
            }
        }

        error_log($exception->getMessage());

        $_SESSION['case_form_error'] =
            'هنگام ثبت اطلاعات کیس خطایی رخ داد.';

        redirectToCaseForm();
    }
} catch (InvalidArgumentException $exception) {
    $_SESSION['case_form_error'] = $exception->getMessage();

    redirectToCaseForm();
} catch (Throwable $exception) {
    error_log($exception->getMessage());

    $_SESSION['case_form_error'] =
        'هنگام بارگذاری برگه تحویل خطایی رخ داد.';

    redirectToCaseForm();
}
