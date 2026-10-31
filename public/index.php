<?php
declare(strict_types=1);

$app = require dirname(__DIR__) . '/app/bootstrap.php';

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';

$result = $app['router']->dispatch($method, $path);
if ($result === null) {
    echo $app['site']->notFound();
    return;
}
if (is_string($result)) {
    echo $result;
}
