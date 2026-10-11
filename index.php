<?php
declare(strict_types=1);
/**
 * Canonical SB Stats directory entry point.
 *
 * Nested directories inherit this view through scripts/php-router.php.
 * Historical symlinks such as 2010/index.php -> ../index.php can resolve here.
 * Do not include or evaluate arbitrary PHP files while enumerating a directory.
 */
$directory ??= __DIR__;
require __DIR__ . '/scripts/directory-index.php';
