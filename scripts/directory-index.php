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
if(preg_match('#^examples/solana/[0-9]{4}$#',$relative)){
 try {
  require_once __DIR__.'/solana-year-graph.php';
  $images=[];
  foreach(sbstatsSolanaYear($directory) as $filename)$images[$filename]=$filename;
 }catch(Throwable $e){$files['Year graph error']=$e->getMessage();}
}

if(preg_match('#^examples/solana/[0-9]{4}/W[0-9]{2}$#',$relative) && is_file($directory.'/week.json')){
    try {
        require_once __DIR__.'/solana-week-graph.php';
        foreach(sbstatsSolanaCharts($directory) as $imageName) $images[$imageName]=$imageName;
        foreach(['get-money','purple-squirrel'] as $token){
            $key=$token.'-daily.png';
            if(!in_array($key,sbstatsSolanaCharts($directory),true)) unset($images[$key]);
        }
        if(!in_array('activity-comparison.png',sbstatsSolanaCharts($directory),true))unset($images['activity-comparison.png']);
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

// ISO-week drill-down: one compact seven-day strip, with dates as destinations.
$solanaWeek=null;$solanaDays=[];$solanaChosen=null;
if(preg_match('#^examples/solana/[0-9]{4}/W[0-9]{2}$#',$relative) && is_file($directory.'/week.json')){
 $solanaWeek=json_decode((string)file_get_contents($directory.'/week.json'),true);
 if(is_array($solanaWeek)&&isset($solanaWeek['start'])){
  $start=new DateTimeImmutable($solanaWeek['start']);
  for($i=0;$i<7;$i++){
   $day=$start->modify('+'.$i.' days')->format('Y-m-d');
   $solanaDays[$day]=['get-money'=>0,'purple-squirrel'=>0];
  }
  foreach(['get-money','purple-squirrel'] as $token)
   foreach(($solanaWeek['subjects'][$token]['points']??[]) as $point){
    $day=$point['date']??'';
    if(isset($solanaDays[$day]))$solanaDays[$day][$token]=(int)($point['count']??0);
   }
  $requested=$_GET['day']??null;
  if(is_string($requested)&&isset($solanaDays[$requested]))$solanaChosen=$requested;
  // The seven-day summary is the chart, not three redundant large PNG previews.
  $images=[];
 }
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
.meta{margin-top:17px;color:#647588;font-size:13px}
.weekstrip{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:2px;background:#d5dde8;border:1px solid #d5dde8;margin-top:8px}
.daycell{background:white;display:flex;flex-direction:column;min-width:0;padding:4px;color:#233e57}
.daycell:hover{background:#ecf4fa;text-decoration:none}.daycell.selected{outline:3px solid #173b60;outline-offset:-3px}
.daylabel{text-align:center;font-size:12px;font-weight:600;white-space:nowrap}.daybars{height:190px;display:flex;align-items:flex-end;gap:2px}
.daybar{display:block;flex:1;min-height:0}.gm{background:#24688f}.ps{background:#79519f}
.daycount{text-align:center;font-size:11px;white-space:nowrap;overflow:hidden}
.daily-detail{background:white;padding:12px;margin:8px 0;border:1px solid #d5dde8}
@media(max-width:600px){.daybars{height:145px}.daycell{padding:2px}.daylabel{font-size:10px}.daycount{font-size:9px}}.extras{margin:24px 0 0;font-size:12px}.extras a{margin-right:12px;display:inline-block}
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
<?php if($solanaWeek!==null && $solanaDays):
 $maximum=1;foreach($solanaDays as $measurements)$maximum=max($maximum,...array_values($measurements));
?>
<section aria-label="Seven-day Solana activity">
<p class="meta">Each column is one UTC day. Select a day to inspect its sampled activity. Blue = Get Money; purple = Purple Squirrel. Unobserved values are not proof of inactivity.</p>
<div class="weekstrip">
<?php foreach($solanaDays as $date=>$measurements): ?>
<a class="daycell<?= $solanaChosen===$date?' selected':'' ?>" href="?day=<?=sbh($date)?>" aria-label="<?=sbh($date)?>, Get Money <?= $measurements['get-money'] ?>, Purple Squirrel <?= $measurements['purple-squirrel'] ?>">
<span class="daylabel"><?=sbh((new DateTimeImmutable($date))->format('D'))?><br><?=sbh(substr($date,5))?></span>
<div class="daybars">
<?php foreach(['get-money'=>'gm','purple-squirrel'=>'ps'] as $token=>$cls): $v=$measurements[$token]; ?>
<span class="daybar <?= $cls ?>" style="height:<?= $v>0?max(1,100*$v/$maximum):0 ?>%" title="<?=sbh($token)?>: <?=$v?>"></span>
<?php endforeach; ?>
</div>
<span class="daycount"><?= $measurements['get-money'] ?: '·' ?> / <?= $measurements['purple-squirrel'] ?: '·' ?></span>
</a>
<?php endforeach; ?>
</div>
<?php if($solanaChosen!==null): $one=$solanaDays[$solanaChosen]; ?>
<article class="daily-detail"><h2><?=sbh($solanaChosen)?> UTC</h2>
<p>Get Money: <strong><?= $one['get-money'] ?></strong> sampled signatures. Purple Squirrel: <strong><?= $one['purple-squirrel'] ?></strong> sampled signatures.</p>
<p class="meta">Daily aggregate only. Intraday timestamps have not been collected for this fixture. <a href="?">Return to week</a>.</p>
</article>
<?php endif; ?>
</section>
<?php endif; ?>
<?php if($images): ?><section class="gallery">
<?php foreach($images as $name): $href=$prefix.rawurlencode($name); ?>
<figure><a href="<?=sbh($href)?>"><img loading="lazy" src="<?=sbh($href)?>" alt="<?=sbh($name)?>"></a>
<figcaption><a href="<?=sbh($href)?>"><?=sbh($name)?></a>
<?php $source=substr($name,0,strrpos($name,'.')).'.sbstats';
if(isset($sources[$source])): ?> · <?=sbh($source)?><?php endif; ?></figcaption></figure>
<?php endforeach; ?></section><?php elseif($solanaWeek===null): ?><p>No images yet in this directory.</p><?php endif; ?>
<?php if($sources||$files): ?><details class="extras"><summary>Other files (<?=count($sources)+count($files)?>)</summary>
<?php foreach(array_keys($sources+$files) as $name): ?>
<span><?=sbh($name)?></span> ·
<?php endforeach; ?></details><?php endif; ?>
<p class="meta">PNG-first directory view. Generated PNGs are cached beside the original .sbstats data.</p>
</main></body></html>
