<?php
// page2.php

session_start();

function ReversePrettyDay ( $Day ) {

if($Day == "Sun")$Day = 1;
if($Day == "Mon")$Day = 2;
if($Day == "Tue")$Day = 3;
if($Day == "Wed")$Day = 4;
if($Day == "Thu")$Day = 5;
if($Day == "Fri")$Day = 6;
if($Day == "Sat")$Day = 7;

return $Day;

}

function HHMMtoInt ( $HHMM ) {

$HH = substr($HHMM,0,2) * 12;
$MM = substr($HHMM,2,2) / 5;

return $HH + $MM;

}

function PrettyDay( $Day ) {

if($Day == 0)$Day = "Sun";
if($Day == 1)$Day = "Mon";
if($Day == 2)$Day = "Tue";
if($Day == 3)$Day = "Wed";
if($Day == 4)$Day = "Thu";
if($Day == 5)$Day = "Fri";
if($Day == 6)$Day = "Sat";

return $Day;

}


function DebugArrayRecursive( $Array , $Name="(unknown)" )
  {
  if(array_key_exists('debug',$_GET))
      {
      echo "<table class=report><tr><td><br>[$Name]";
      foreach($Array as $Index => $Value)
        {
        if(is_array($Value))
          {
          DebugArrayRecursive( $Value , $Index );
          }
          else
          echo "<br>$Index => $Value";
        }
      echo "</td></tr></table>\n";
      }
  }




foreach($_SESSION as $label => $element)
	{
//	$value = '$Array' . $element;
//	eval($value);
	if(substr($label,0,4) == $_GET['id'])
	   eval('$Array' . $element);
	}

//DebugArrayRecursive($Array);

$week = array();

$host = "";
$search = "";
$group = 0;
$unit = 0;

$ImageHeight = 170;
$Magnify = 1;

foreach($Array as $customer => $rest)
 foreach($rest as $year => $rest)
  foreach($rest as $host => $rest)
   foreach($rest as $search => $rest)
    foreach($rest as $group => $rest) 
     foreach($rest as $unit => $rest)
        {
	$filepath = $host . "/" . $rest;
	$result = explode(":",$rest);
 	include $_GET['report'] . "." . $result[4] . ".php" ;

//	print_r($vmstat);
//	echo "<br>xxxxx $result[4]";
	//echo "<br>$search $group $unit $filepath";

        if(($handle = fopen($filepath, "r")) !== FALSE)
           {
//	echo "<br>$filepath";
        while (($data = fgetcsv($handle, 1000, " ")) !== FALSE)
                {
                $key = substr($data[0],0,2) . substr($data[0],3,2);
                $temparray = explode(",",$data[2]);

	        foreach($position as $index => $pos)
			$week[$group][$unit][$key][$index] = $temparray[$pos];
	        }
           }
        fclose($handle);
      }

//DebugArrayRecursive($week,"week");

$GraphBottom = $ImageHeight - 30;
$GraphLeft = 40;
$DayWidth = 136; 

$SegmentBottom = $GraphBottom;

$adjustedweek = array();

foreach($week as $weekno => $rest)
  foreach($rest as $day => $rest)
    {
    foreach($rest as $time => $rest)
      { 
    $dayno = ReversePrettyDay($day) -1;
    $SegmentBottom = $GraphBottom;
	foreach($rest as $label => $datum)
	{ 
	$SegmentTop = $SegmentBottom - $datum * $Magnify;
//	$XPos = ($DayWidth * $dayno) + $GraphLeft;
	$timeno = HHMMtoInt($time);
	$XPos = ($dayno  * $DayWidth) + ( .45 * $timeno) ;
	//echo "<br>$label $dayno $timeno $XPos $SegmentBottom $SegmentTop";
	$adjustedweek[$dayno][$timeno][$label] = array( $XPos,$SegmentBottom,$SegmentTop );
	$SegmentBottom = $SegmentTop - 1;
	}
      }
//    echo "<br>$dayno";
    }

DebugArrayRecursive($adjustedweek,"adjustedweek");

//echo '<br /><a href="current.php">return</a>';

$img = imagecreatetruecolor(1000, $ImageHeight);

// allocate some colors
$color = array();
$color['cpu-us'] = imagecolorallocate($img, 0, 66, 132);
$color['cpu-sy'] = imagecolorallocate($img, 255,   66,   8);
$color['cpu-wa'] = imagecolorallocate($img, 255, 214,   33);
$color['page-fr'] = imagecolorallocate($img, 200, 66, 132);
$color['page-sr'] = imagecolorallocate($img, 155,   96,   8);
$color['page-cy'] = imagecolorallocate($img, 55, 14,   50);
$color['faults-in'] = imagecolorallocate($img, 200, 0, 0);
$color['faults-sy'] = imagecolorallocate($img, 0, 66, 132);
$color['faults-cs'] = imagecolorallocate($img, 40, 266, 50) ;
$color['kth-r'] = imagecolorallocate($img, 40,50,60);
$color['kth-b'] = imagecolorallocate($img, 100,10,100);
$color['memory-avm'] = imagecolorallocate($img, 140,50,60);
$color['memory-fre'] = imagecolorallocate($img, 30,110,100);

$color['procs-b'] = imagecolorallocate($img, 10,50,100);
$color['procs-w'] = imagecolorallocate($img, 100,100,50);
$color['memory-swap'] = imagecolorallocate($img, 200,10,100);
$color['memory-free'] = imagecolorallocate($img, 100,10,200);

$color['page-re'] = imagecolorallocate($img, 10,200,100);
$color['page-mf'] = imagecolorallocate($img, 100,50,50);
$color['page-pi'] = imagecolorallocate($img, 50,10,100);
$color['page-p'] = imagecolorallocate($img, 200,200,100);
$color['page-fr'] = imagecolorallocate($img, 10,10,50);

$color['disk-de'] = imagecolorallocate($img, 200,200,100);
$color['disk-sr'] = imagecolorallocate($img, 40,50,40);
$color['disk-s0'] = imagecolorallocate($img, 40,100,100);
$color['disk-s1'] = imagecolorallocate($img, 90,90,100);
$color['disk-s2'] = imagecolorallocate($img, 40,110,40);
$color['disk-s3'] = imagecolorallocate($img, 50,50,200);
$color['cpu-id'] = imagecolorallocate($img, 200,10,200);

/*
$ChartBar[0] = imagecolorallocate($img, 100, 66, 132);
$ChartBar[1] = imagecolorallocate($img, 255, 66, 8);
$ChartBar[2] = imagecolorallocate($img, 0, 66, 132);
$ChartBar[3] = imagecolorallocate($img, 123,0,33);
$ChartBar[4] = imagecolorallocate($img, 49, 66, 0);
$ChartBar[5] = imagecolorallocate($img, 173, 206, 0);
$ChartBar[6] = imagecolorallocate($img, 74,24,107);
$ChartBar[7] = imagecolorallocate($img, 255, 148, 8);
$ChartBar[8] = imagecolorallocate($img, 198, 0, 8);
$ChartBar[9] = imagecolorallocate($img, 0, 132, 214);
$ChartBar[10] = imagecolorallocate($img, 255, 214, 33);
$ChartBar[11] = imagecolorallocate($img, 132, 206, 255);
$ChartBar[12] = imagecolorallocate($img, 82, 100, 24);
$ChartBar[13] = imagecolorallocate($img, 183,0,73);
$ChartBar[14] = imagecolorallocate($img, 0,0,200);
*/

$white = imagecolorallocate($img, 255,255,255);
$frame = imagecolorallocate($img, 100,100,100);
$background = imagecolorallocate($img,155,155,155);

/// Fill and paint frame

imagefilledrectangle($img, 0, 0, 1000, $ImageHeight, $background);


foreach($adjustedweek as $day => $rest)
 { 
 ImageString($img,5, $GraphLeft + ($day * $DayWidth),$GraphBottom,PrettyDay($day),$frame);
 foreach($rest as $time => $rest)
   {
   foreach ($position as $index => $trash)
     imageline($img,$GraphLeft + $rest[$index][0], $rest[$index][1], $GraphLeft + $rest[$index][0],$rest[$index][2], $color[$index]);
   }
   $count++;
 }

ImageString($img,5,20,16,"$host $group",$frame);

$keyX = $GraphLeft + 240;
$keyY = 10;
$keyW = 15;
$keyH = 15;

$KeyLeft = 100;

$Count = 0;

$Counter = 0;


foreach ($position as $i => $MyFact)
        {
        if($Counter == 5)
                {
                $keyX = $GraphLeft + 240;
                $keyY = 30;
                }

        if($Counter == 10)
                {
                $keyX = $GraphLeft + 240;
                $keyY = 50;
                }


        imagefilledrectangle($img,$keyX,$keyY,$keyX + $keyW,$keyY + $keyH ,$color[$i]);
        imagerectangle($img,$keyX,$keyY,$keyX + $keyW,$keyY + $keyH ,$Black);
        ImageString( $img , 3 , $keyX + 27 , $keyY + 4 , $i , $TextColor );
        $keyX = $keyX + 151;
        $Counter++;
        }


// output image in the browser
header("Content-type: image/png");
imagepng($img);

// free memory
imagedestroy($img);

?>
