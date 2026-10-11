<?php
// PHP's built-in server router. Return false to serve files in document root.
// Prevent unintended execution of legacy scripts; never expose private files.
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = is_string($path) ? rawurldecode($path) : '/';
if (str_contains($path, "\\0") || preg_match('#(?:^|/)\\.(?:git|env)(?:/|$)#i', $path)) {
    http_response_code(403); echo "Forbidden"; return true;
}
if (preg_match('/\\.(?:pem|crt|key|bash|sh|py|tar|sbstats)$/i', $path)) {
    http_response_code(403); echo "Forbidden"; return true;
}
$allowLegacy = getenv('SBSTATS_LEGACY') === '1';
if (!$allowLegacy && preg_match('#^/(?:2010|2013|2014|keep)(?:/|$)#', $path)) {
    http_response_code(403); echo "Legacy PHP disabled. Restart with --legacy on loopback."; return true;
}
if (!$allowLegacy && preg_match('/\\.php$/i', $path) && !str_starts_with($path, '/examples/php/')) {
    http_response_code(403); echo "PHP endpoint disabled"; return true;
}
return false;
