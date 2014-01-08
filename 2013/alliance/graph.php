<?

include '/home/secrets/www/stats/2013/library.php';

$FileName = "$_GET[file]";

//// Define and Setup Visual Informaiton

// create a 200*200 image
$img = imagecreatetruecolor(1000, 300);

// allocate some colors
$color = array();
$color['cpu-us'] = imagecolorallocate($img, 0, 66, 132);
$color['cpu-sy'] = imagecolorallocate($img, 255,   66,   8);
$color['cpu-wa'] = imagecolorallocate($img, 255, 214,   33);
$white = imagecolorallocate($img, 255,255,255);
$frame = imagecolorallocate($img, 100,100,100);
$background = imagecolorallocate($img,155,155,155);

/// Fill and paint frame

imagefilledrectangle($img, 0, 0, 1020, 300, $background);
imagerectangle($img, 40,60, 902, 241, $frame);


/////  Define Array Information

$Files = array();
$Types = array();
$Report = array();

$Files[0] = "20130827342CDT:0306A00D2:4204000000-true-2-enable-PowerPC_POWER6:uxdrtgwdv1:aix:vmstat.sbstats";
$Files[0] = $FileName;


$Types['aix']['vmstat'][0] = "HH:MM";
$Types['aix']['vmstat'][1] = "LCPU:MEM-MB";
$Types['aix']['vmstat'][2] = "kth-r,kth-b,memory-avm,memory-fre,page-re,page-pi,page-po,page-fr,page-sr,page-cy,faults-in,faults-sy,faults-cs,cpu-us,cpu-sy,cpu-id,cpu-wa,skip,cpu-pc,skip,cpu-ec";

$Report['aix1']['sunos']['aix'][0] = "HH:MM";
$Report['aix1']['sunos']['aix'][2] = "cpu-sy,cpu-us,cpu-wa";


$Types['sunos']['vmstat'][0] = "HH:MM";
$Types['sunos']['vmstat'][1] = "LCPU:MEM-MB";
$Types['sunos']['vmstat'][2] = "kth-r,kth-b,memory-avm,memory-fre,page-re,page-pi,page-po,page-fr,page-sr,page-cy,faults-in,faults-sy,faults-cs,cpu-us,cpu-sy,cpu-id,cpu-wa,skip,cpu-pc,skip,cpu-ec";

$Report['sunos1']['sunos']['vmstat'][0] = "HH:MM";
$Report['sunos1']['sunos']['vmstat'][2] = "cpu-sy,cpu-us,cpu-wa";


$Types['aix']['vmstat'][0] = "HH:MM";
$Types['aix']['vmstat'][1] = "LCPU:MEM-MB";
$Types['aix']['vmstat'][2] = "kth-r,kth-b,memory-avm,memory-fre,page-re,page-pi,page-po,page-fr,page-sr,page-cy,faults-in,faults-sy,faults-cs,cpu-us,cpu-sy,cpu-id,cpu-wa,skip,cpu-pc,skip,cpu-ec";

$Report['base']['aix']['vmstat'][0] = "HH:MM";
$Report['base']['aix']['vmstat'][2] = "cpu-sy,cpu-us,cpu-wa";

$Types['sunos']['vmstat'][0] = "HH:MM";
$Types['sunos']['vmstat'][1] = "LCPU:MEM-MB";
$Types['sunos']['vmstat'][2] = "kth-r,kth-b,kth-w,memory-swap,memory-free,page-re,page-mf,page-pi,page-po,page-fr,page-de,page-sr,disk-mo,disk-m1,disk-m5,disk-m6,faults-in,faults-sy,faults-cs,cpu-us,cpu-sy,cpu-id";

$Report['base']['sunos']['vmstat'][0] = "HH:MM";
$Report['base']['sunos']['vmstat'][2] = "cpu-sy,cpu-us";

$Types['sco']['vmstat'][0] = "HH:MM";
$Types['sco']['vmstat'][2] = 'procs-r,procs-b,procs-w,paging-frs,paging-dmd,paging-sw,paging-cch,paging-fil,paging-pft,paging-frp,paging-pos,paging-pif,paging-pis,paging-rso,paging-rsi,system-sy,system-cs,cpu-us,cpu-su,cpu-id';


$rest = MultiDayArray( $Files , $Types , $Report['base'] );

//echo"<pre>$_GET[file]</pre>";

$dirFiles[0] = $FileName;

$Target = array();


foreach($dirFiles as $file)
        {
	$basename = explode("/",$file);
        $day = substr($file,10,1);
  //      $dirDays[$day] = $file;
        list( $date,$serial,$machinedesc,$host,$type,$report) = explode(":",$basename[1]);
//	echo "$type,$report";
        $temp = explode(".",$report);
        $report = $temp[0];

	$Titles = explode(",",$Types[$type][$report][2]);

        if(($handle = fopen($file, "r")) !== FALSE)
        {
        while (($data = fgetcsv($handle, 1000, " ")) !== FALSE)
                {
//		echo "<pre>";
//		print_r($data);
//		echo "</pre>";
                $key = substr($data[0],0,2) . substr($data[0],3,2);
//		echo "$day $key";
		$datum = explode(",",$data[2]);
		foreach($datum as $key2 => $item)
                	$Target[$host][$date][$serial][$key][$Titles[$key2]] = $item;
                }
        fclose($handle);
        }
    }

//echo "<pre>";
//print_r($Target);
//echo "</pre>";


/*

echo "

  [20131012CDT] => Array
             [0306A00D2] => Array
                   [uxdrtgwdv1] => Array
                     [aix] => Array
                        [vmstat] => Array
                            [00:00] => Array
                                [cpu-us] => 7
                                [cpu-sy] => 6
                                [cpu-wa] => 0

";

*/

$GraphBottom = 240;
$BottomPoint = 0;
$TopPoint = 0;
$XAxis = 40;
$XMultiplier = 2;
$XSpace = 3;

$KeyX = 850;
$KeyY = 40;
$ToLabel = array();

imagerectangle($img,$XAxis,40,902,$GraphBottom+20,$frame); // for bottom frame
imageline($img,$XAxis - 30, $GraphBottom, $XAxis - 30, 40 ,$frame);

for($percent = 0; $percent <= 100; $percent = $percent + 10)
   {
   imageline($img, $XAxis -30, $GraphBottom - ($percent * 2), 900, $GraphBottom - ($percent * 2), $frame);
      if($percent > 0)
   ImageString($img, 3, $XAxis - 22, $GraphBottom - ($percent * 2), $percent,$frame);
   }	

echo "<pre>";
print_r($Target);
echo "</pre>";


foreach($Target as $hostname => $rest)
 foreach($rest as $date => $rest)
   foreach($rest as $serialno => $rest)
	  { 
	  echo "<br>---------- $hostname $date ----------\n";

          foreach($rest as $time => $rest)
	    {
	    $thishour = substr($time,0,2);
	    $thisminute = substr($time,2,2);
            if($thisminute == "00")
            {
	        echo "<hr>-------------- $thishour ----------\n";
            imageline($img, $XAxis, $GraphBottom + 20, $XAxis, 40, $frame);
	    ImageString($img, 3, $XAxis + 3,$GraphBottom + 3, PrettyHour($thishour),$frame);
	    }
	    $BottomPoint = $GraphBottom;
     //     echo "<br>[ $thisminute  ";
	    $ToLabel = $rest;

	    foreach($rest as $label => $datum)
                {
                $TopPoint = $BottomPoint - ($datum * $XMultiplier);
///	        echo "[ $label $BottomPoint $TopPoint] ";
		
		if($datum > 0)
		   imagefilledrectangle($img, $XAxis, $BottomPoint, $XAxis + 2, $TopPoint, $color[$label]);
		$BottomPoint = $TopPoint;
		}
	     $XAxis = $XAxis + $XSpace;
//	    echo "]\n";
	    }
	  }

$LabelY = 40;
foreach($ToLabel as $label => $datum)
	{
	ImageString($img,5, 915,$LabelY,$label,$color[$label]);
	$LabelY += 15; 
	}

ImageString($img,5,20,16,"$hostname - $weekday $month $day $year $tz ($week) $serialno $os-$type",$frame);

// output image in the browser
header("Content-type: image/png");
imagepng($img);

// free memory
imagedestroy($img);

?>
