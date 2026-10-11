<?php
declare(strict_types=1);
/** Month-by-month vertical sample lines, derived exclusively from archived week.json records. */
function sbstatsSolanaMonths(string $yearDir): array {
    if (!extension_loaded('gd')) throw new RuntimeException('PHP GD extension required');
    $daily=[];$latest=0;
    foreach(glob($yearDir.'/W[0-9][0-9]/week.json')?:[] as $json) {
        $latest=max($latest,filemtime($json));
        $week=json_decode((string)file_get_contents($json),true);
        if(!is_array($week))continue;
        foreach(['get-money','purple-squirrel'] as $token)
          foreach(($week['subjects'][$token]['points']??[]) as $point){
            $date=$point['date']??'';
            if(!preg_match('/^\\d{4}-\\d{2}-\\d{2}$/',$date))continue;
            // Merge overlapping ISO week data by date rather than double-counting.
            $daily[substr($date,0,7)][$token][$date]=(int)($point['count']??0);
          }
    }
    $result=[];
    foreach($daily as $month=>$subjects){
        if(substr($month,0,4)!==basename($yearDir))continue;
        foreach($subjects as $token=>$points){
            if(array_sum($points)===0)continue;
            $file=$yearDir.'/'.$month.'-'.$token.'-daily.png';
            if(!is_file($file)||filemtime($file)<$latest) sbstatsSolanaMonthPng($file,$month,$token,$points);
            $result[]=basename($file);
        }
    }
    sort($result,SORT_NATURAL);
    return $result;
}
function sbstatsSolanaMonthPng(string $file,string $month,string $token,array $points):void {
    $W=1280;$H=475;$l=65;$r=1225;$top=87;$bottom=375;
    $im=imagecreatetruecolor($W,$H);
    $bg=imagecolorallocate($im,250,252,254);
    $grid=imagecolorallocate($im,216,227,238);
    $text=imagecolorallocate($im,31,52,72);
    $ink=$token==='get-money'?imagecolorallocate($im,36,104,143):imagecolorallocate($im,113,85,155);
    imagefill($im,0,0,$bg);
    imagestring($im,5,20,16,'SB Stats / Solana / '.$month.' / '.$token,$text);
    imagestring($im,3,20,44,'UTC daily sampled signatures | partial top-account sample, not verified transfers',$text);
    $days=(int)date('t',strtotime($month.'-01'));
    $max=max(1,max($points));
    $scale=max(4,(int)ceil($max/4)*4);
    for($i=0;$i<=4;$i++){
        $y=$bottom-(int)(($bottom-$top)*$i/4);
        imageline($im,$l,$y,$r,$y,$grid);
        imagestring($im,3,8,$y-7,(string)round($scale*$i/4),$text);
    }
    $step=($r-$l)/$days;
    for($d=1;$d<=$days;$d++){
        $date=sprintf('%s-%02d',$month,$d);
        $x=(int)round($l+($d-.5)*$step);
        if(isset($points[$date])&&$points[$date]>0){
            $y=$bottom-(int)round(($bottom-$top)*$points[$date]/$scale);
            imageline($im,$x,$bottom,$x,$y,$ink);
            imageline($im,$x+1,$bottom,$x+1,$y,$ink);
        }
        if($d===1||$d%5===0||$d===$days)imagestring($im,2,$x-7,$bottom+12,(string)$d,$text);
    }
    imagestring($im,3,20,421,'Total observed: '.array_sum($points).' | '.count($points).' dated sample entries',$text);
    imagestring($im,2,20,451,'Unobserved days are blank, not assumed zero; source: ISO-week week.json files',$text);
    $tmp=tempnam(dirname($file),'.month-');
    if($tmp===false){imagedestroy($im);throw new RuntimeException('Cannot create temp PNG');}
    try{
      if(!imagepng($im,$tmp))throw new RuntimeException('PNG write failed');
      if(!rename($tmp,$file))throw new RuntimeException('PNG rename failed');
    } finally{imagedestroy($im);if(file_exists($tmp))unlink($tmp);}
}
