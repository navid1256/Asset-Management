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
require_once __DIR__ . '/../libs/lib-case-validation.php';
require_once __DIR__ . '/../libs/lib-case-upload.php';

/**
 * Redirects the request back to the case registration form.
 */
function redirectToCaseForm(): never
{
    header('Location: ' . BASE_URL . '/pages/case/case.php');
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
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

$uploadedPaths = [];

try {
    $submittedToken = readStringInput($_POST, 'csrf_token');
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

    $caseData = validateCaseForm($_POST);
    $storageCount = $caseData['storage']['count'];
    $uploadedFiles = uploadCaseFiles($_FILES, $storageCount);
    $uploadedPaths = $uploadedFiles['uploaded_paths'];

    $caseNumbers = $caseData['case_numbers'];
    $cpu = $caseData['cpu'];
    $motherboard = $caseData['motherboard'];
    $gpu = $caseData['gpu'];
    $ram = $caseData['ram'];
    $storage = $caseData['storage'];
    $writer = $caseData['writer'];
    $powerSupply = $caseData['power_supply'];
    $chassis = $caseData['chassis'];

    $itNumber = $caseNumbers['it_number'];
    $storageDevices = $storage['devices'];
    $storageWarrantyPaths = $uploadedFiles['storage_warranties'];

    foreach ($storageDevices as $index => $storageDevice) {
        $storageDevices[$index]['warranty_path'] =
            $storageWarrantyPaths[$index] ?? null;
    }

    $gpuMemoryGB = $gpu['memory_gb'] === null
        ? null
        : (string) $gpu['memory_gb'];

    $pdo->beginTransaction();

    $caseId = createCaseNumber(
        $pdo,
        $itNumber,
        $caseNumbers['asset_number'],
        $receiverEmployeeId,
        $createdByUserId,
        $uploadedFiles['delivery_sheet']
    );

    createCaseCpu(
        $pdo,
        $itNumber,
        $cpu['brand'],
        $cpu['generation'],
        $cpu['model'],
        $cpu['speed_ghz'],
        $uploadedFiles['cpu_warranty']
    );

    createCaseMotherboard(
        $pdo,
        $itNumber,
        $motherboard['brand'],
        $motherboard['model'],
        $uploadedFiles['motherboard_warranty']
    );

    createCaseGpu(
        $pdo,
        $itNumber,
        $gpu['type'],
        $gpu['brand'],
        $gpu['model'],
        $gpuMemoryGB,
        $uploadedFiles['gpu_warranty']
    );

    foreach ($ram['configurations'] as $ramConfiguration) {
        createCaseRam(
            $pdo,
            $itNumber,
            $ramConfiguration['brand'],
            $ramConfiguration['ram_count'],
            $ramConfiguration['model'],
            $ramConfiguration['ram_type'],
            (string) $ramConfiguration['module_capacity_gb'],
            $ramConfiguration['speed_mhz'],
            $uploadedFiles['ram_warranty']
        );
    }

    createCaseStorageGroup(
        $pdo,
        $itNumber,
        $storage['count']
    );

    foreach ($storageDevices as $storageDevice) {
        createStorageDevice(
            $pdo,
            $itNumber,
            $storageDevice['device_number'],
            $storageDevice['storage_type'],
            $storageDevice['brand'],
            $storageDevice['model'],
            $storageDevice['capacity_gb'],
            $storageDevice['warranty_path']
        );
    }

    createCaseWriter(
        $pdo,
        $itNumber,
        $writer['enabled'],
        $writer['type'],
        $writer['brand'],
        $writer['model'],
        $uploadedFiles['writer_warranty']
    );

    createCasePowerSupply(
        $pdo,
        $itNumber,
        $powerSupply['brand'],
        $powerSupply['model'],
        $powerSupply['wattage_w'],
        $uploadedFiles['power_supply_warranty']
    );

    createCaseChassis(
        $pdo,
        $itNumber,
        $chassis['brand'],
        $chassis['model'],
        $uploadedFiles['chassis_warranty']
    );

    $pdo->commit();

    $_SESSION['registered_case_id'] = $caseId;

    header(
        'Location: '
            . BASE_URL
            . '/pages/case/case-status.php'
    );
    exit;
} catch (InvalidArgumentException $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    cleanupUploadedPaths($uploadedPaths);
    $_SESSION['case_form_error'] = $exception->getMessage();

    redirectToCaseForm();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    cleanupUploadedPaths($uploadedPaths);
    error_log('Save case error: ' . $exception->getMessage());

    $_SESSION['case_form_error'] =
        'هنگام ثبت اطلاعات کیس خطایی رخ داد.';

    redirectToCaseForm();
}
