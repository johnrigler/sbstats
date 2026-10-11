<?php
declare(strict_types=1);
function sbstatsSolanaYear(string $dir): array {
 if(!extension_loaded('gd'))throw new RuntimeException('Install php-gd');
 $daily=[];$mtime=0;$year=basename($dir);
 foreach(glob($dir.'/W*/week.json')?:[] as $file){
   $mtime=max($mtime,filemtime($file));
   $data=json_decode((string)file_get_contents($file),true);
   foreach(['get-money','purple-squirrel'] as $token)
     foreach($data['subjects'][$token]['points']??[] as $p){
       $date=$p['date']??'';
       if(substr($date,0,4)===$year)$daily[$date][$token]=(int)($p['count']??0);
     }
 }
 ksort($daily);
 $daily=array_filter($daily,static fn($v)=>array_sum($v)>0);
 if(!$daily)return [];
 $path=$dir.'/activity-year.png';
 if(is_file($path)&&filemtime($path)>=$mtime)return ['activity-year.png'];
 $im=imagecreatetruecolor(1400,450);
 $white=imagecolorallocate($im,250,252,254);
 $grey=imagecolorallocate($im,209,222,233);
 $black=imagecolorallocate($im,29,49,68);
 $colors=['get-money'=>imagecolorallocate($im,37,105,150),'purple-squirrel'=>imagecolorallocate($im,122,81,159)];
 imagefill($im,0,0,$white);
 imagestring($im,5,20,18,'SB Stats / Solana / '.$year,$black);
 imagestring($im,3,20,48,'Daily signatures: Get Money + Purple Squirrel | compressed active-day timeline',$black);
 $max=1;foreach($daily as $values)$max=max($max,...array_values($values));
 $height=270;$base=357;$left=60;$width=1300;
 for($i=0;$i<=4;$i++){
   $y=$base-(int)($height*$i/4);
   imageline($im,$left,$y,$left+$width,$y,$grey);
   imagestring($im,2,6,$y-7,(string)round($max*$i/4),$black);
 }
 $dates=array_keys($daily);$step=$width/count($dates);$lastMonth='';
 foreach($dates as $i=>$date){
   $x=(int)($left+($i+.5)*$step);
   $month=substr($date,0,7);
   if($month!==$lastMonth){
     if($i>0)imageline($im,$x,$base-$height,$x,$base,$grey);
     imagestring($im,2,$x,$base+10,substr($month,5),$black);
     $lastMonth=$month;
   }
   $layers=[];
   foreach($colors as $token=>$color)if(($daily[$date][$token]??0)>0)$layers[]=['v'=>$daily[$date][$token],'c'=>$color];
   usort($layers,static fn($a,$b)=>$b['v']<=>$a['v']);
   foreach($layers as $layer){
     $y=$base-(int)round($height*$layer['v']/$max);
     imagefilledrectangle($im,$x-3,$y,$x+3,$base,$layer['c']);
   }
 }
 imagestring($im,3,20,400,'Blue: Get Money     Purple: Purple Squirrel',$black);
 imagestring($im,2,20,428,'Sampled signatures only. Unobserved and inactive days are compressed, not plotted as zeros.',$black);
 $tmp=tempnam($dir,'.year-');
 if($tmp===false)throw new RuntimeException('Cannot create PNG');
 try{if(!imagepng($im,$tmp)||!rename($tmp,$path))throw new RuntimeException('Cannot save PNG');}
 finally{imagedestroy($im);if(is_file($tmp))unlink($tmp);}
 return ['activity-year.png'];
}
