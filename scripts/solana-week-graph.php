<?php
declare(strict_types=1);
/** Graph 7 UTC days of Get Money and Purple Squirrel sampled activity using GD.
 * Source: week.json; persists PNGs in the same ISO-week directory.
 * No attempt to invent missing hourly samples or claim mint-specific transfers.
 */
function sbstatsSolanaCharts(string $directory): array {
    $weekFile=$directory.'/week.json';
    if (!is_file($weekFile) || !extension_loaded('gd')) return [];
    $week=json_decode((string)file_get_contents($weekFile),true,512,JSON_THROW_ON_ERROR);
    if (($week['schema']??'')!=='sbstats/week-v1' || !isset($week['subjects']))return [];
    $subjects=array_intersect_key($week['subjects'],array_flip(['get-money','purple-squirrel']));
    $charts=[];
    $subjects=array_filter($subjects,static fn($s)=>array_sum(array_map(static fn($p)=>(int)($p['count']??0),$s['points']??[]))>0);
    foreach ($subjects as $token=>$series) {
        $file=$directory.'/'.$token.'-daily.png';
        if (!sbstatsSolanaFresh($file,$weekFile)) sbstatsSolanaPaint($file,$week,[$token=>$series]);
        $charts[]=basename($file);
    }
    if(count($subjects)===2){
        $file=$directory.'/activity-comparison.png';
        if(!sbstatsSolanaFresh($file,$weekFile)) sbstatsSolanaPaint($file,$week,$subjects);
        $charts[]=basename($file);
    }
    return $charts;
}
function sbstatsSolanaFresh(string $png,string $src):bool {
    return is_file($png)&&filesize($png)>0&&filemtime($png)>=filemtime($src);
}
function sbstatsSolanaPaint(string $png,array $week,array $series):void {
    $width=1280;$height=475;$left=72;$right=1218;$top=95;$bottom=373;
    $im=imagecreatetruecolor($width,$height);
    $colors=[];
    foreach(['bg'=>[250,252,254],'grid'=>[222,231,239],'text'=>[34,53,72],
        'get-money'=>[36,104,143],'purple-squirrel'=>[113,85,155]] as $key=>$rgb)
        $colors[$key]=imagecolorallocate($im,...$rgb);
    imagefilledrectangle($im,0,0,$width,$height,$colors['bg']);
    $max=1;$labels=[];
    foreach($series as $subject) foreach(($subject['points']??[]) as $i=>$row) {
        $max=max($max,(int)($row['count']??0));
        $labels[$i]=$row['date']??'';
    }
    $ticks=5;$scale=(int)(ceil($max/($ticks-1))*($ticks-1));
    $scale=max($ticks-1,$scale);
    for($i=0;$i<$ticks;$i++){
        $value=(int)round($scale*$i/($ticks-1));
        $y=$bottom-(int)round(($bottom-$top)*$i/($ticks-1));
        imageline($im,$left,$y,$right,$y,$colors['grid']);
        imagestring($im,3,20,$y-8,(string)$value,$colors['text']);
    }
    $weekId=(string)($week['id']??'unknown');
    imagestring($im,5,24,19,'SB Stats / Solana / '.$weekId,$colors['text']);
    imagestring($im,3,24,48,'Daily sampled distinct signatures | UTC | top-20 account sample (not all mint transfers)',$colors['text']);
    $tokens=array_keys($series);$n=count($tokens);
    for($day=0;$day<7;$day++){
        $band=($right-$left)/7;$center=$left+$band*($day+.5);
        foreach($tokens as $j=>$token){
            $v=(int)($series[$token]['points'][$day]['count']??0);
            $barWidth=$n===1?90:49;
            $x=(int)round($center-($n*$barWidth)/2+$j*$barWidth);
            $y=$bottom-(int)round(($bottom-$top)*$v/$scale);
            if($v>0) imagefilledrectangle($im,$x,$y,$x+$barWidth-3,$bottom,$colors[$token]);
            imagestring($im,3,$x+10,max(77,$y-18),(string)$v,$colors['text']);
        }
        $date=substr((string)($labels[$day]??''),5);
        imagestring($im,3,(int)$center-28,$bottom+14,['Mon','Tue','Wed','Thu','Fri','Sat','Sun'][$day],$colors['text']);
        imagestring($im,2,(int)$center-22,$bottom+37,$date,$colors['text']);
    }
    $x=24;
    foreach($tokens as $token){
        imagefilledrectangle($im,$x,424,$x+17,441,$colors[$token]);
        imagestring($im,3,$x+23,425,$token,$colors['text']);
        $x+=205;
    }
    imagestring($im,2,24,455,'Sampled archive data; blank or zero days do not establish inactivity.',$colors['text']);
    $meta=['schema'=>'sbstats/png-v1','week'=>$weekId,'timezone'=>'UTC',
           'metric'=>'sampled_distinct_signatures','series'=>$series,
           'source'=>'week.json','coverage'=>'Partial top-20 token-account signature sample, not verified transfers'];
    if(function_exists('imagepng')) {
        // Ordinary valid PNG, with machine-readable exact source in week.json beside it.
        $temp=tempnam(dirname($png),'.solana-');
        if($temp===false){imagedestroy($im);throw new RuntimeException('Cannot create temporary PNG');}
        try {
            if(!imagepng($im,$temp))throw new RuntimeException('PNG encode failure');
            if(!rename($temp,$png))throw new RuntimeException('Cannot save PNG in '.dirname($png));
        } finally {if(is_file($temp))unlink($temp);imagedestroy($im);}
    }
}
