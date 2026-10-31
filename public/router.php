<?php
declare(strict_types=1);

$uri = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
$file = __DIR__ . $uri;
if ($uri !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
