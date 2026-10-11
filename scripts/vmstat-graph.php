<?php
declare(strict_types=1);
require_once __DIR__.'/vmstat-lib.php';
try {
    $source=sbstatsSource((string)($_GET['file'] ?? ''));
    $png=sbstatsCache($source,isset($_GET['rebuild']));
    header('Content-Type: image/png');
    header('Cache-Control: no-cache');
    readfile($png);
} catch (Throwable $e) {
    http_response_code(500);header('Content-Type: text/plain; charset=utf-8');
    echo 'SB Stats: '.$e->getMessage();
}
