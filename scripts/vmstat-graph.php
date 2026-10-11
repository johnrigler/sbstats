<?php
declare(strict_types=1);
// Render original 2010 five-minute AIX .sbstats data through PHP GD.
// Intended for the local SB Stats development server only.
$root = realpath(dirname(__DIR__));
$rel = $_GET['file'] ?? '';
if (!is_string($rel) || !preg_match('#^2010/(?:month/)?[a-zA-Z0-9_/-]+\\.sbstats$#', $rel)
    || str_contains($rel, '..')) { http_response_code(400); exit('Invalid stats path'); }
$path=realpath($root.'/'.$rel);
if ($path===false || !str_starts_with($path,$root.DIRECTORY_SEPARATOR.'2010'.DIRECTORY_SEPARATOR)
    || !is_file($path)) { http_response_code(404); exit('No stats file'); }
if (!extension_loaded('gd')) { http_response_code(503); exit('PHP GD is required: apt install php-gd'); }
$fp=fopen($path,'rb');
$first=fgets($fp);
if ($first===false) { fclose($fp); http_response_code(422);exit('Empty stats file'); }
$labels=preg_split('/\\s+/',trim($first));
$fields=array_slice($labels,2); // pseries :SERIALNO precede comma-separated measurement fields
$index=array_flip($fields);
$required=['CPU_US','CPU_SY','CPU_WA'];
foreach ($required as $key) if(!isset($index[$key])){fclose($fp);http_response_code(422);exit('Unsupported stats header: '.$key);}
$points=[];
while (($line=fgets($fp))!==false) {
    $line=trim($line);
    if(!preg_match('/^(\\d{4})\\s+\\S+\\s+(.+)$/',$line,$m))continue;
    $hh=(int)substr($m[1],0,2);$mm=(int)substr($m[1],2,2);
    if($hh>23||$mm>59||$mm%5!==0)continue;
    $parts=array_map('trim',explode(',',$m[2]));
    $get=static fn(string $field):float=>isset($index[$field],$parts[$index[$field]])
        && is_numeric($parts[$index[$field]])? max(0.,(float)$parts[$index[$field]]):0.;
    $slot=$hh*12+intdiv($mm,5);
    $points[$slot]=['time'=>$m[1], 'user'=>$get('CPU_US'),'sys'=>$get('CPU_SY'),
        'wait'=>$get('CPU_WA'),'pi'=>$get('#PAGE_PI'),'po'=>$get('#PAGE_PO')];
}
fclose($fp);
ksort($points);
$W=1280;$H=485;$left=62;$right=1230;$top=75;$bottom=365;
$im=imagecreatetruecolor($W,$H);
$colors=[];
foreach(['bg'=>[250,252,254],'grid'=>[216,227,238],'text'=>[29,50,69],
'user'=>[64,133,175],'sys'=>[46,80,116],'wait'=>[213,157,69],
'pi'=>[180,45,83],'po'=>[119,50,148]] as $name=>$rgb)
    $colors[$name]=imagecolorallocate($im,...$rgb);
imagefilledrectangle($im,0,0,$W,$H,$colors['bg']);
$font=3;$peak=0.;
foreach($points as $r)$peak=max($peak,$r['pi'],$r['po']);
$scale=max(1,$peak);
for($t=0;$t<=100;$t+=20){
 $y=$bottom-(int)(($bottom-$top)*$t/100);
 imageline($im,$left,$y,$right,$y,$colors['grid']);
 imagestring($im,2,9,$y-6,$t.'%', $colors['text']);
}
for($h=0;$h<=24;$h+=2){
 $x=$left+(int)(($right-$left)*$h/24);
 imageline($im,$x,$top,$x,$bottom,$colors['grid']);
 imagestring($im,2,$x-13,$bottom+9,sprintf('%02d:00',$h),$colors['text']);
}
imagestring($im,5,20,14,'SB Stats | '.basename($path).' | 5-minute AIX vmstat',$colors['text']);
imagestring($im,3,20,36,'CPU stacked: User / System / I/O Wait  |  Page In/Out overlay (independent right scale)',$colors['text']);
$bar=max(2,(int)(($right-$left)/288)-1);
foreach($points as $slot=>$r){
 $x=$left+(int)($slot*($right-$left)/288);
 $sum=0.;
 foreach(['user','sys','wait'] as $key){
    $a=min(100.,$sum);$sum=min(100.,$sum+$r[$key]);
    $y1=$bottom-(int)($a*($bottom-$top)/100);
    $y2=$bottom-(int)($sum*($bottom-$top)/100);
    imagefilledrectangle($im,$x,$y2,$x+$bar,max($y2,$y1),$colors[$key]);
 }
 foreach(['pi','po'] as $key){
    if($r[$key]<=0)continue;
    $height=(int)round($r[$key]/$scale*($bottom-$top));
    imageline($im,$x,$bottom,$x,$bottom-$height,$colors[$key]);
 }
}
imagestring($im,3,16,399,'User', $colors['user']);
imagestring($im,3,90,399,'System', $colors['sys']);
imagestring($im,3,180,399,'I/O Wait', $colors['wait']);
imagestring($im,3,285,399,'Page In', $colors['pi']);
imagestring($im,3,390,399,'Page Out', $colors['po']);
imagestring($im,3,510,399,'Right axis max: '.(string)$scale.' pages / sample',$colors['text']);
imagestring($im,2,16,436,count($points).' observed five-minute slots; missing slots are blank; paging overlaid without changing CPU scale.',$colors['text']);
header('Content-Type: image/png');
header('Cache-Control: no-store');
imagepng($im);
imagedestroy($im);
