<?php
declare(strict_types=1);
$root=realpath(dirname(__DIR__));
$directory=realpath($directory ?? $root);
if (!$root||!$directory||!($directory===$root||str_starts_with($directory,$root.DIRECTORY_SEPARATOR))) {http_response_code(403);exit('Forbidden');}
$relative=trim(substr($directory,strlen($root)),DIRECTORY_SEPARATOR);
$prefix='/'.($relative===''?'':str_replace(DIRECTORY_SEPARATOR,'/',$relative).'/');
function sbh(string $s): string{return htmlspecialchars($s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function sbSize(int $n): string{return $n>=1048576?round($n/1048576,1).' MB':($n>=1024?round($n/1024,1).' KB':$n.' B');}
$folders=[];$images=[];$sources=[];$files=[];
foreach(new DirectoryIterator($directory) as $entry){
 if($entry->isDot()||$entry->isLink())continue;
 $name=$entry->getFilename();
 if($name==='index.php'||str_starts_with($name,'.')||$name==='keep'||preg_match('/\\.(?:pem|crt|key|tar|ext|int)$/i',$name))continue;
 if($entry->isDir()){$folders[]=$name;continue;}
 if(!is_file($entry->getPathname()))continue;
 if(preg_match('/\\.(?:png|jpe?g|gif|webp)$/i',$name))$images[$name]=$name;
 elseif(str_ends_with($name,'.sbstats'))$sources[$name]=$name;
 else $files[$name]=$name;
}
natcasesort($folders);ksort($images,SORT_NATURAL);ksort($sources,SORT_NATURAL);ksort($files,SORT_NATURAL);
if(preg_match('#^examples/solana/[0-9]{4}/W[0-9]{2}$#',$relative) && is_file($directory.'/week.json')){
    try {
        require_once __DIR__.'/solana-week-graph.php';
        foreach(sbstatsSolanaCharts($directory) as $imageName) $images[$imageName]=$imageName;
        ksort($images,SORT_NATURAL);
    } catch(Throwable $e) {
        $files['Solana graph generation error']=$e->getMessage();
    }
}
// Materialize missing/stale PNGs next to their sources so all future views use static images.
if(str_starts_with($relative,'2010/')){
 require_once __DIR__.'/vmstat-lib.php';
 foreach($sources as $name){
    $src=$directory.DIRECTORY_SEPARATOR.$name;
    try {
      $png=sbstatsCache($src);
      $images[basename($png)]=basename($png);
    } catch(Throwable $e){$files[$name]='Error building graph: '.$e->getMessage();}
 }
 ksort($images,SORT_NATURAL);
}
$segments=$relative===''?[]:explode(DIRECTORY_SEPARATOR,$relative);
$crumbs=[['SB Stats','/']];$part='';
foreach($segments as $seg){$part.=rawurlencode($seg).'/';$crumbs[]=[$seg,'/'.$part];}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>SB Stats / <?=sbh($relative)?></title><style>
*{box-sizing:border-box}body{margin:0;background:#f5f7fa;color:#193047;font:15px system-ui,sans-serif}
main{max-width:1500px;margin:auto;padding:14px}a{color:#286b99;text-decoration:none}a:hover{text-decoration:underline}
header{padding:4px 0 10px}nav{display:flex;gap:7px;flex-wrap:wrap;font-size:14px;color:#718092}
h1{font-size:22px;margin:12px 0}h2{font-size:17px;margin:10px 0}.folders{display:flex;flex-wrap:wrap;gap:8px;margin:10px 0}
.folders a{background:#e8eef5;padding:8px 12px;border-radius:6px}.gallery{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,590px),1fr));gap:13px}
figure{margin:0;background:#fff;padding:5px;border:1px solid #dce3ea;border-radius:7px}
figure>a{display:block}figure img{display:block;width:100%;height:auto}figcaption{padding:5px;font-size:12px;color:#5f7282;overflow-wrap:anywhere}
.meta{margin-top:17px;color:#647588;font-size:13px}.extras{margin:24px 0 0;font-size:12px}.extras a{margin-right:12px;display:inline-block}
</style></head><body><main><header><nav>
<?php foreach($crumbs as $i=>$c): ?><?= $i?' / ':'' ?><a href="<?=sbh($c[1])?>"><?=sbh($c[0])?></a><?php endforeach; ?>
</nav><h1><?=sbh($relative?:'/')?> <small>(<?=count($images)?> images)</small></h1>
<?php if($relative!==''): ?><a href="../">↩ Parent directory</a><?php endif; ?>
<?php if($folders): ?><div class="folders"><?php foreach($folders as $dir): ?>
<a href="<?=sbh($prefix.rawurlencode($dir).'/')?>">📁 <?=sbh($dir)?></a>
<?php endforeach; ?></div><?php endif; ?>
</header>
<?php if(!empty($files['Solana graph generation error'])): ?>
<p role="alert"><?=sbh($files['Solana graph generation error'])?></p>
<?php endif; ?>
<?php if($images): ?><section class="gallery">
<?php foreach($images as $name): $href=$prefix.rawurlencode($name); ?>
<figure><a href="<?=sbh($href)?>"><img loading="lazy" src="<?=sbh($href)?>" alt="<?=sbh($name)?>"></a>
<figcaption><a href="<?=sbh($href)?>"><?=sbh($name)?></a>
<?php $source=substr($name,0,strrpos($name,'.')).'.sbstats';
if(isset($sources[$source])): ?> · <?=sbh($source)?><?php endif; ?></figcaption></figure>
<?php endforeach; ?></section><?php else: ?><p>No images yet in this directory.</p><?php endif; ?>
<?php if($sources||$files): ?><details class="extras"><summary>Other files (<?=count($sources)+count($files)?>)</summary>
<?php foreach(array_keys($sources+$files) as $name): ?>
<span><?=sbh($name)?></span> ·
<?php endforeach; ?></details><?php endif; ?>
<p class="meta">PNG-first directory view. Generated PNGs are cached beside the original .sbstats data.</p>
</main></body></html>
