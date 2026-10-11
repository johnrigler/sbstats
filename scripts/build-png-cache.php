#!/usr/bin/env php
<?php
declare(strict_types=1);
require_once __DIR__.'/vmstat-lib.php';
$root=realpath(dirname(__DIR__));
$target=$argv[1] ?? '2010/month';
$force=in_array('--rebuild',$argv,true);
if (str_contains($target,'..') || !str_starts_with($target,'2010/')) {
    fwrite(STDERR,"Usage: php scripts/build-png-cache.php 2010/month[/09/host] [--rebuild]\n");exit(2);
}
$path=realpath($root.'/'.$target);
if (!$path || !str_starts_with($path,$root.DIRECTORY_SEPARATOR.'2010'.DIRECTORY_SEPARATOR)) {
    fwrite(STDERR,"Unknown target\n");exit(2);
}
$inputs=[];
if (is_file($path)) $inputs=[$path];
else {
    $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path,FilesystemIterator::SKIP_DOTS));
    foreach($it as $file) if($file->isFile()&&!$file->isLink()&&str_ends_with($file->getFilename(),'.sbstats'))$inputs[]=$file->getPathname();
}
sort($inputs);$errors=0;
foreach($inputs as $file) {
    if(!str_ends_with($file,'.sbstats'))continue;
    try {
        $png=sbstatsCache($file,$force);
        echo substr($png,strlen($root)+1)."\n";
    } catch(Throwable $e){$errors++;fwrite(STDERR,basename($file).': '.$e->getMessage()."\n");}
}
echo 'Processed '.count($inputs).' source files, errors '.$errors."\n";
exit($errors?1:0);
