<?php

define('BASE_PATH', dirname(__DIR__));

$documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
$projectRoot = realpath(BASE_PATH);
$baseUrl = '';

if ($documentRoot && $projectRoot) {
    $documentRoot = str_replace('\\', '/', $documentRoot);
    $projectRoot = str_replace('\\', '/', $projectRoot);

    if (strpos($projectRoot, $documentRoot) === 0) {
        $baseUrl = substr($projectRoot, strlen($documentRoot));
        $baseUrl = '/' . trim($baseUrl, '/');
    }
}

define('BASE_URL', $baseUrl === '/' ? '' : $baseUrl);
define('ASSETS_URL', BASE_URL . '/assets');

