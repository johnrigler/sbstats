<?php
// Local-only SB Stats PHP router. Directories cascade into a readable index.
// Legacy 2010/2013 PHP is opt-in and may be incompatible with PHP 8.
$path=parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH);
$path=is_string($path)?rawurldecode($path):'/';
$root=realpath(dirname(__DIR__));
if (!str_starts_with($path,'/') || str_contains($path,"\0") || str_contains($path,'\\') ||
    preg_match('#(?:^|/)\.\.?(/|$)#',$path) || preg_match('#(?:^|/)\.(?:git|env)(?:/|$)#i',$path)) {
    http_response_code(403);echo 'Forbidden';return true;
}
$allowLegacy=getenv('SBSTATS_LEGACY')==='1';
if (preg_match('/\.(?:pem|crt|key|bash|sh|py|tar|sbstats|ext|int|hide)$/i',$path) ||
    preg_match('#/(?:keep|\.git)(?:/|$)#i',$path)) {
    http_response_code(403);echo 'Forbidden';return true;
}
$real=realpath($root . $path);
if ($real===false || !($real===$root || str_starts_with($real,$root.DIRECTORY_SEPARATOR))) {
    http_response_code(404);echo 'Not found';return true;
}
$legacyPath=(bool)preg_match('#^/(?:2010|2013|2014)(?:/|$)#',$path);
// Browsing directory names is useful even when historical code execution is disabled.
if (is_dir($real)) {
    $directory=$real;
    require __DIR__.'/directory-index.php';
    return true;
}
if (!$allowLegacy && $legacyPath && preg_match('/\.php$/i',$path)) {
    http_response_code(403);echo 'Legacy PHP execution disabled. Restart with --legacy on loopback.';return true;
}
if (!$allowLegacy && preg_match('/\.php$/i',$path) && !str_starts_with($path,'/examples/php/')) {
    http_response_code(403);echo 'PHP endpoint disabled';return true;
}
return false;
