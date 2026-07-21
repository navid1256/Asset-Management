<?php

require_once __DIR__ . '/config.php';

$dsn = "mysql:host={$database_config->host};"
    . "dbname={$database_config->db};"
    . "port={$database_config->port};"
    . "charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO(
        $dsn,
        $database_config->user,
        $database_config->pass,
        $options
    );
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    die('Database connection failed');
}
