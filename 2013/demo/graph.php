<?

session_start();

include '/home/secrets/www/sbstats/2013/library.php';

$FileName = "$_REQUEST[file]";

//// Define and Setup Visual Informaiton

// create a 200*200 image
$img = imagecreatetruecolor(1000, 300);

/// Fill and paint frame

imagefilledrectangle($img, 0, 0, 1020, 300, $background);
imagerectangle($img, 40,60, 902, 241, $frame);

/////  Define Array Information

$Files = array();
$Types = array();
$Report = array();

$Files[0] = $FileName;

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

//echo "<pre>";
//print_r($Target);
//echo "</pre>";

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
