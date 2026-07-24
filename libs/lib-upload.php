<?php

declare(strict_types=1);

const CASE_UPLOAD_MAX_BYTES = 5 * 1024 * 1024;

const CASE_UPLOAD_MIME_EXTENSIONS = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'application/pdf' => 'pdf',
];

function storeRequiredUploadedFile(array $file, string $destinationDirectory, string $storedPathPrefix): string
{
    $uploadError = $file['error'] ?? UPLOAD_ERR_NO_FILE;

    if (!is_int($uploadError)) {
        throw new InvalidArgumentException(
            'اطلاعات فایل بارگذاری‌شده معتبر نیست.'
        );
    }

    if ($uploadError === UPLOAD_ERR_NO_FILE) {
        throw new InvalidArgumentException(
            'بارگذاری برگه تحویل الزامی است.'
        );
    }

    if ($uploadError !== UPLOAD_ERR_OK) {
        throw new RuntimeException(
            'هنگام بارگذاری برگه تحویل خطایی رخ داد.'
        );
    }

    $temporaryPath = $file['tmp_name'] ?? null;
    $fileSize = $file['size'] ?? null;

    if (
        !is_string($temporaryPath)
        || !is_uploaded_file($temporaryPath)
    ) {
        throw new InvalidArgumentException(
            'فایل بارگذاری‌شده معتبر نیست.'
        );
    }

    if (
        !is_int($fileSize)
        || $fileSize < 1
        || $fileSize > CASE_UPLOAD_MAX_BYTES
    ) {
        throw new InvalidArgumentException(
            'حجم برگه تحویل باید حداکثر ۵ مگابایت باشد.'
        );
    }

    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($temporaryPath);

    $extension = is_string($mimeType)
        ? CASE_UPLOAD_MIME_EXTENSIONS[$mimeType] ?? null
        : null;

    if ($extension === null) {
        throw new InvalidArgumentException(
            'فرمت برگه تحویل باید JPG، PNG، WebP یا PDF باشد.'
        );
    }

    if (
        !is_dir($destinationDirectory)
        && !mkdir($destinationDirectory, 0755, true)
        && !is_dir($destinationDirectory)
    ) {
        throw new RuntimeException(
            'پوشه ذخیره‌سازی فایل ایجاد نشد.'
        );
    }

    $fileName = bin2hex(random_bytes(16))
        . '.'
        . $extension;

    $destinationPath = $destinationDirectory
        . DIRECTORY_SEPARATOR
        . $fileName;

    if (!move_uploaded_file($temporaryPath, $destinationPath)) {
        throw new RuntimeException(
            'ذخیره برگه تحویل انجام نشد.'
        );
    }

    $normalizedPrefix = trim(
        str_replace('\\', '/', $storedPathPrefix),
        '/'
    );

    return $normalizedPrefix . '/' . $fileName;
}

function deleteUploadedFileIfExists(
    string $absoluteFilePath
): void {
    if (!is_file($absoluteFilePath)) {
        return;
    }

    if (!unlink($absoluteFilePath)) {
        throw new RuntimeException(
            'حذف فایل بارگذاری‌شده انجام نشد.'
        );
    }
}
