<?php
declare(strict_types=1);
/** Apache/PHP-FPM front controller, mirrors the safe PHP CLI router behavior. */
$root=realpath(dirname(__DIR__));
$uri=parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH);
if(!is_string($uri)||!str_starts_with($uri,'/')||str_contains($uri,"\\")||str_contains($uri,"\0")||preg_match('/%2f|%5c/i',$uri)){
 http_response_code(403);exit('Forbidden');
}
$path=rawurldecode($uri);
if(preg_match('#(?:^|/)\\.\\.?(/|$)#',$path)||preg_match('#/(?:keep|\\.git)(?:/|$)#i',$path)||
 preg_match('/\\.(?:pem|crt|key|bash|sh|py|tar|sbstats|ext|int|hide)$/i',$path)){
 http_response_code(403);exit('Forbidden');
}
$real=realpath($root.$path);
if(!$real||!($real===$root||str_starts_with($real,$root.DIRECTORY_SEPARATOR))){http_response_code(404);exit('Not found');}
if(is_dir($real)||(str_ends_with($path,'/index.php')&&is_link($root.$path))){
 $directory=is_dir($real)?$real:dirname($root.$path);
 require $root.'/index.php';exit;
}
if(!is_file($real)){http_response_code(404);exit('Not found');}
$legacy=getenv('SBSTATS_LEGACY')==='1';
if(preg_match('/\\.php$/i',$path)){
 if(!$legacy && (preg_match('#^/(?:2010|2013|2014)/#',$path)||
   !in_array($path,['/index.php','/scripts/vmstat-graph.php','/examples/php/index.php'],true))){
   http_response_code(403);exit('PHP endpoint disabled');
 }
 if(!$legacy && str_starts_with($path,'/scripts/')){http_response_code(403);exit('PHP endpoint disabled');}
 require $real;exit;
}
$mime=['png'=>'image/png','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','gif'=>'image/gif',
 'webp'=>'image/webp','svg'=>'image/svg+xml','css'=>'text/css','js'=>'text/javascript',
 'json'=>'application/json','html'=>'text/html','txt'=>'text/plain'];
$ext=strtolower(pathinfo($real,PATHINFO_EXTENSION));
header('Content-Type: '.($mime[$ext]??'application/octet-stream'));
readfile($real);
