<?php

declare(strict_types=1);

const CASE_UPLOAD_MAX_BYTES = 5 * 1024 * 1024;

const CASE_UPLOAD_MIME_EXTENSIONS = [
    'image/jpeg' => 'jpg',
    'image/pjpeg' => 'jpg',
    'image/png' => 'png',
    'image/apng' => 'apng',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
    'image/bmp' => 'bmp',
    'image/x-ms-bmp' => 'bmp',
    'image/tiff' => 'tiff',
    'image/avif' => 'avif',
    'image/heic' => 'heic',
    'image/heif' => 'heif',
    'image/x-icon' => 'ico',
    'image/vnd.microsoft.icon' => 'ico',
    'image/jp2' => 'jp2',
    'image/jpx' => 'jpx',
    'image/jpm' => 'jpm',
    'image/x-portable-bitmap' => 'pbm',
    'image/x-portable-graymap' => 'pgm',
    'image/x-portable-pixmap' => 'ppm',
    'image/x-xbitmap' => 'xbm',
    'image/x-xpixmap' => 'xpm',
    'application/pdf' => 'pdf',
];

/**
 * Resolves a safe extension for PDF files and any detected image MIME type.
 */
function resolveCaseUploadExtension(string $mimeType): ?string
{
    if (isset(CASE_UPLOAD_MIME_EXTENSIONS[$mimeType])) {
        return CASE_UPLOAD_MIME_EXTENSIONS[$mimeType];
    }

    return str_starts_with($mimeType, 'image/')
        ? 'img'
        : null;
}

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
            'هنگام بارگذاری فایل خطایی رخ داد.'
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
            'حجم فایل باید حداکثر ۵ مگابایت باشد.'
        );
    }

    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($temporaryPath);

    $extension = is_string($mimeType)
        ? resolveCaseUploadExtension($mimeType)
        : null;

    if ($extension === null) {
        throw new InvalidArgumentException(
            'فرمت فایل باید یکی از فرمت‌های تصویر مجاز یا PDF باشد.'
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
            'ذخیره فایل انجام نشد.'
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

function cleanupUploadedPaths(array $uploadedPaths): void
{
    foreach ($uploadedPaths as $path) {
        if (!is_string($path) || $path === '') {
            continue;
        }

        $absolutePath = BASE_PATH
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $path);

        try {
            deleteUploadedFileIfExists($absolutePath);
        } catch (RuntimeException $exception) {
            error_log($exception->getMessage() . ': ' . $absolutePath);
        }
    }
}
