<?php

declare(strict_types=1);

require_once __DIR__ . '/lib-input.php';

/**
 * Validates and normalizes the IT number and optional asset number.
 */
function validateCaseNumbers(array $post): array
{
    $itNumber = normalizeDigitsToEnglish(
        readStringInput($post, 'itNumber')
    );
    $assetNumber = normalizeDigitsToEnglish(
        readStringInput($post, 'assetNumber')
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

    return [
        'it_number' => $itNumber,
        'asset_number' => $assetNumber === '' ? null : $assetNumber,
    ];
}

/**
 * Validates CPU form fields and returns normalized CPU data.
 */
function validateCpu(array $post): array
{
    $brand = readStringInput($post, 'cpu_brand');
    $model = readStringInput($post, 'cpu_model');
    $generation = parsePositiveInteger(
        readStringInput($post, 'cpu_generation'),
        'نسل CPU',
        50
    );
    $speedGhz = normalizeDigitsToEnglish(
        readStringInput($post, 'cpu_speed_ghz')
    );

    if (
        $brand === ''
        || $model === ''
        || !preg_match('/^[0-9]{1,2}(?:\.[0-9])?$/', $speedGhz)
        || (float) $speedGhz <= 0
    ) {
        throw new InvalidArgumentException(
            'اطلاعات CPU کامل نیست.'
        );
    }

    return [
        'brand' => $brand,
        'model' => $model,
        'generation' => $generation,
        'speed_ghz' => $speedGhz,
    ];
}

/**
 * Validates motherboard brand and model fields.
 */
function validateMotherboard(array $post): array
{
    $brand = readStringInput($post, 'motherboard_brand');
    $model = readStringInput($post, 'motherboard_model');

    if ($brand === '' || $model === '') {
        throw new InvalidArgumentException(
            'اطلاعات Motherboard کامل نیست.'
        );
    }

    return [
        'brand' => $brand,
        'model' => $model,
    ];
}

/**
 * Validates GPU type and the required fields for an internal GPU.
 */
function validateGpu(array $post): array
{
    $type = readStringInput($post, 'gpu_type');

    if (!in_array($type, ['onboard', 'internal'], true)) {
        throw new InvalidArgumentException('نوع GPU معتبر نیست.');
    }

    if ($type === 'onboard') {
        return [
            'type' => $type,
            'brand' => null,
            'model' => null,
            'memory_gb' => null,
        ];
    }

    $brand = readStringInput($post, 'gpu_brand');
    $model = readStringInput($post, 'gpu_model');
    $memoryInput = preg_replace(
        '/\s*GB$/i',
        '',
        readStringInput($post, 'gpu_memory_gb')
    );

    if (
        $brand === ''
        || $model === ''
        || !is_string($memoryInput)
    ) {
        throw new InvalidArgumentException(
            'اطلاعات GPU داخلی کامل نیست.'
        );
    }

    return [
        'type' => $type,
        'brand' => $brand,
        'model' => $model,
        'memory_gb' => parsePositiveInteger(
            $memoryInput,
            'حافظه GPU',
            255
        ),
    ];
}

/**
 * Validates RAM rows and groups modules with identical specifications.
 */
function buildRamConfigurations(array $post, int $ramCount): array
{
    $brands = readStringListInput($post, 'ram_brand');
    $models = readStringListInput($post, 'ram_model');
    $types = readStringListInput($post, 'ram_type');
    $capacities = readStringListInput($post, 'ram_capacity_gb');
    $speeds = readStringListInput($post, 'ram_speed_mhz');

    foreach ([$brands, $models, $types, $capacities, $speeds] as $values) {
        if (count($values) !== $ramCount) {
            throw new InvalidArgumentException(
                'تعداد ردیف‌های RAM با تعداد انتخاب‌شده یکسان نیست.'
            );
        }
    }

    $configurations = [];

    for ($index = 0; $index < $ramCount; $index++) {
        $brand = $brands[$index];
        $model = $models[$index];
        $ramType = $types[$index];

        if ($brand === '' || $model === '') {
            throw new InvalidArgumentException(
                'برند و مدل تمام RAMها الزامی است.'
            );
        }

        if (!in_array($ramType, ['DDR4', 'DDR5'], true)) {
            throw new InvalidArgumentException('نوع RAM معتبر نیست.');
        }

        $capacityGB = parsePositiveInteger(
            $capacities[$index],
            'ظرفیت RAM',
            255
        );
        $speedMHz = parsePositiveInteger(
            $speeds[$index],
            'سرعت RAM',
            100000
        );
        $configurationKey = json_encode(
            [$brand, $model, $ramType, $capacityGB, $speedMHz],
            JSON_THROW_ON_ERROR
        );

        if (!isset($configurations[$configurationKey])) {
            $configurations[$configurationKey] = [
                'brand' => $brand,
                'model' => $model,
                'ram_type' => $ramType,
                'module_capacity_gb' => $capacityGB,
                'speed_mhz' => $speedMHz,
                'ram_count' => 0,
                'module_numbers' => [],
            ];
        }

        $configurations[$configurationKey]['ram_count']++;
        $configurations[$configurationKey]['module_numbers'][] = $index + 1;
    }

    return array_values($configurations);
}

/**
 * Validates the RAM count and returns all grouped RAM configurations.
 */
function validateRam(array $post): array
{
    $count = parsePositiveInteger(
        readStringInput($post, 'ram_count'),
        'تعداد RAM',
        4
    );

    return [
        'count' => $count,
        'configurations' => buildRamConfigurations($post, $count),
    ];
}

/**
 * Validates each storage row and builds normalized storage device data.
 */
function buildStorageDevices(array $post, int $storageCount): array
{
    $types = readStringListInput($post, 'storage_type');
    $brands = readStringListInput($post, 'storage_brand');
    $models = readStringListInput($post, 'storage_model');
    $capacities = readStringListInput($post, 'storage_capacity_gb');

    foreach ([$types, $brands, $models, $capacities] as $values) {
        if (count($values) !== $storageCount) {
            throw new InvalidArgumentException(
                'تعداد ردیف‌های Storage با تعداد انتخاب‌شده یکسان نیست.'
            );
        }
    }

    $devices = [];
    $allowedTypes = ['NVMe', 'M.2 SATA', 'SATA'];

    for ($index = 0; $index < $storageCount; $index++) {
        if (!in_array($types[$index], $allowedTypes, true)) {
            throw new InvalidArgumentException('نوع Storage معتبر نیست.');
        }

        if ($brands[$index] === '' || $models[$index] === '') {
            throw new InvalidArgumentException(
                'برند و مدل تمام Storageها الزامی است.'
            );
        }

        $devices[] = [
            'device_number' => $index + 1,
            'storage_type' => $types[$index],
            'brand' => $brands[$index],
            'model' => $models[$index],
            'capacity_gb' => parsePositiveInteger(
                $capacities[$index],
                'ظرفیت Storage',
                65535
            ),
            'warranty_path' => null,
        ];
    }

    return $devices;
}

/**
 * Validates the storage count and returns all storage devices.
 */
function validateStorage(array $post): array
{
    $count = parsePositiveInteger(
        readStringInput($post, 'storage_count'),
        'تعداد Storage',
        3
    );

    return [
        'count' => $count,
        'devices' => buildStorageDevices($post, $count),
    ];
}

/**
 * Validates the optional optical writer and its required fields.
 */
function validateWriter(array $post): array
{
    $enabledInput = $post['writer_enabled'] ?? null;

    if ($enabledInput !== null && $enabledInput !== '1') {
        throw new InvalidArgumentException(
            'وضعیت Writer معتبر نیست.'
        );
    }

    $enabled = $enabledInput === '1' ? 1 : 0;

    if ($enabled === 0) {
        return [
            'enabled' => 0,
            'type' => null,
            'brand' => null,
            'model' => null,
        ];
    }

    $type = readStringInput($post, 'writer_type');
    $brands = readStringListInput($post, 'writer_brand');
    $models = readStringListInput($post, 'writer_model');

    if (
        !in_array($type, ['CD Writer', 'DVD Writer'], true)
        || count($brands) !== 1
        || count($models) !== 1
        || $brands[0] === ''
        || $models[0] === ''
    ) {
        throw new InvalidArgumentException(
            'اطلاعات Writer کامل نیست.'
        );
    }

    return [
        'enabled' => 1,
        'type' => $type,
        'brand' => $brands[0],
        'model' => $models[0],
    ];
}

/**
 * Validates power supply brand, model, and wattage.
 */
function validatePowerSupply(array $post): array
{
    $brand = readStringInput($post, 'power_brand');
    $model = readStringInput($post, 'power_model');
    $wattageW = parsePositiveInteger(
        readStringInput($post, 'power_wattage_w'),
        'توان Power Supply',
        65535
    );

    if ($brand === '' || $model === '') {
        throw new InvalidArgumentException(
            'اطلاعات Power Supply کامل نیست.'
        );
    }

    return [
        'brand' => $brand,
        'model' => $model,
        'wattage_w' => $wattageW,
    ];
}

/**
 * Validates chassis brand and model fields.
 */
function validateChassis(array $post): array
{
    $brand = readStringInput($post, 'case_brand');
    $model = readStringInput($post, 'case_model');

    if ($brand === '' || $model === '') {
        throw new InvalidArgumentException(
            'اطلاعات Chassis کامل نیست.'
        );
    }

    return [
        'brand' => $brand,
        'model' => $model,
    ];
}

/**
 * Validates the complete case form and returns structured component data.
 */
function validateCaseForm(array $post): array
{
    return [
        'case_numbers' => validateCaseNumbers($post),
        'cpu' => validateCpu($post),
        'motherboard' => validateMotherboard($post),
        'gpu' => validateGpu($post),
        'ram' => validateRam($post),
        'storage' => validateStorage($post),
        'writer' => validateWriter($post),
        'power_supply' => validatePowerSupply($post),
        'chassis' => validateChassis($post),
    ];
}
