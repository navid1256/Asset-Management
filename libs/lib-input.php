<?php

function normalizeDigitsToEnglish(string $value): string
{
    return strtr($value, [
        '۰' => '0',
        '۱' => '1',
        '۲' => '2',
        '۳' => '3',
        '۴' => '4',
        '۵' => '5',
        '۶' => '6',
        '۷' => '7',
        '۸' => '8',
        '۹' => '9',
    ]);
}

function readStringInput(array $input, string $key): string
{
    $value = $input[$key] ?? '';

    if (!is_string($value)) {
        throw new InvalidArgumentException(
            'ساختار اطلاعات فرم معتبر نیست.'
        );
    }

    return trim($value);
}

function readStringListInput(array $input, string $key): array
{
    $values = $input[$key] ?? [];

    if (!is_array($values)) {
        throw new InvalidArgumentException(
            'ساختار اطلاعات فرم معتبر نیست.'
        );
    }

    return array_map(
        static function (mixed $value): string {
            if (!is_string($value)) {
                throw new InvalidArgumentException(
                    'ساختار اطلاعات فرم معتبر نیست.'
                );
            }

            return trim($value);
        },
        array_values($values)
    );
}

function parsePositiveInteger(
    string $value,
    string $fieldLabel,
    int $maximum
): int {
    $normalizedValue = normalizeDigitsToEnglish($value);

    if (!preg_match('/^[0-9]+$/', $normalizedValue)) {
        throw new InvalidArgumentException(
            "{$fieldLabel} باید یک عدد صحیح باشد."
        );
    }

    $number = (int) $normalizedValue;

    if ($number < 1 || $number > $maximum) {
        throw new InvalidArgumentException(
            "{$fieldLabel} خارج از محدوده مجاز است."
        );
    }

    return $number;
}
