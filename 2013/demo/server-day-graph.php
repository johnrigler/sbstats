<?
header("Content-type: image/png");

session_start(); 

include '/home/secrets/www/sbstats/2013/library.php';

$report=$_REQUEST['report'];

$img = imagecreatetruecolor(930,170);


$color = array();

$color[0] = imagecolorallocate($img, 0, 66, 132);
$color[1] = imagecolorallocate($img, 255,   66,   8);
$color[2] = imagecolorallocate($img, 255, 214,   33);

//$color[0] = imagecolorallocate($img,251,151,0);
//$color[1] = imagecolorallocate($img,0,251,251);
//$color[2] = imagecolorallocate($img,251,251,0);
$color[3] = imagecolorallocate($img,0,0,201);
$color[4] = imagecolorallocate($img,211,0,145);
$color[5] = imagecolorallocate($img,0,251,0);
$color[6] = imagecolorallocate($img,151,70,175);
$color[7] = imagecolorallocate($img,241,46,48);
$color[8] = imagecolorallocate($img,251,100,251);
$color[9] = imagecolorallocate($img,0,251,251);




$white = imagecolorallocate($img, 255,255,255);
$frame = imagecolorallocate($img, 100,100,100);
$background = imagecolorallocate($img,155,155,155);

$Graph['daily']['innerframe'] = array(40,140,902,40);
$Graph['daily']['unit'] = array(1,2);
$Graph['daily']['space'] = array(0,0);
$Graph['daily']['key'] = array(450,10);

// allocate some colors
//$criteria = $_SESSION['report'][$report][$type][$source][2];

//foreach ($criteria as $colorname => $colorresource)
  //      $color[$colorname] = imagecolorallocate($img, $colorresource[0], $colorresource[1], $colorresource[2]);



//i$Report['base']['aix']['vmstat'][0] = "HH:MM";
//$Report['base']['aix']['vmstat'][2] = "cpu-sy,cpu-us,cpu-wa";

/// Transform file data if necessary
foreach ($_SESSION as $WM => $rest)

   foreach($rest as $YRW => $rest2)
    foreach($rest2 as $Host => $data)
    if($data['D'])
     foreach($data['D'] as $day => $file)
	{
        list( $date,$serial,$machinedesc,$host,$type,$source) = explode(":",$file);
	$temp = explode(".",$source);
	$source = $temp[0];
//	print_r($Types[$type][$source]);
	$Titles = explode(",",$Types[$type][$source][2]);
///	print_r($Titles);
        if(($handle = fopen("$Host/$file", "r")) !== FALSE)
          {
          while (($data = fgetcsv($handle, 1000, " ")) !== FALSE)
                {
                $hhmm = substr($data[0],0,2) . substr($data[0],3,2);
                $datum = explode(",",$data[2]);
                foreach($datum as $key2 => $item)
			{
//			echo "<br>$key2  $Titles[$key2]";	
                                $_SESSION[$WM][$YRW][$host]['R'][$type][$date][$serial][$hhmm][$source][$Titles[$key2]] = $item;
			unset($_SESSION[$WM][$YRW][$host]['D']);
			}

                }
          fclose($handle);
          }

	}

$ThisSession = array();


foreach ($_SESSION as $WM => $rest)
   foreach($rest as $YRW => $rest2)
    foreach($rest2 as $Host => $data)
    if($data['R'])
     foreach($data['R'] as $type => $rest)
       foreach($rest as $stamp => $rest)
	foreach($rest as $serial => $rest)
	  foreach($rest as $time => $rest)
	    foreach($rest as $source => $rest)
	     foreach($rest as $key => $value)
		{
		  $TempStr = $Report[$report][$type][$source][2];

		  if(in_array($key,explode(',',$TempStr)))
		 $ThisSession[$WM][$YRW][$Host]['S'][$type][$stamp][$serial][$time][$source][$key] = $value;
		 } 

//print_r($ThisSession);

imagefilledrectangle($img, 0, 0, 930,170, $background);
$immerframe = $Graph['daily']['innerframe'];
imagefilledrectangle($img, $innerframe[0],$innerframe[1], $innerframe[2], $innerframe[3], $white);

/// Draw everything


foreach ($ThisSession as $WM => $next)
  foreach ($next as $YRW => $next)
   foreach($next as $Host => $rest)
     foreach($rest['S'] as $type => $rest)
  foreach ($rest as $stamp => $rest)
   foreach ($rest as $serial => $rest)
       foreach($rest as $time => $rest)
	  foreach($rest as $source => $rest)
		{
//		print_r($rest['cpu-sy']);
	     	$hour = substr($time,0,2);
	     	$minute = substr($time,2,2);
     		$minutepos = (($hour*60) + $minute)/1.59;
		$bottom = 140;
		$labelno = 0;
		$firstlabel = 0;
		$leftlabel = 50;
		$leftspace = 20;


	        foreach($rest as $label => $value)
		   {
		   if($firstlabel == 0)
		       ImageString($img,4,$leftlabel,150,$label,$color[$labelno]);
		
		   if($labelno == 0)$bottom = 140;

		   imageline($img,$leftspace + $minutepos,$bottom,$leftspace + $minutepos,$bottom - $value,$color[$labelno]);
		   $bottom = $bottom - $value;
		   $leftlabel += 75;
		   $labelno += 1;
		   }
		$firstlabel = 1;
		}

/*

   {
   ImageString($img,5,20,16,"$hostname $date $serial $report $type $source",$white);
   foreach ($data as $time => $data)
     {
     $hour = substr($time,0,2);
     $minute = substr($time,2,2);
     $minutepos = $minute/5;

     ImageString($img,5,$innerframe[0] + 36*$hour,$innerframe[1],"$hour",$white);
     $x = ($innerframe[0] + 36*$hour) + ($minutepos * 3);
     $bottom = $innerframe[1];
//     foreach ($data as $label => $value)
        foreach ($criteria as $label => $trash)
        {
        $top = ($bottom - $data[$label] * $scale);
        imageline($img,$x,$bottom,$x,$top,$color[$label]);
        $bottom = $top;
        }
     }
   }



*/


// output image in the browser
imagepng($img);



// free memory
imagedestroy($img);



?>
