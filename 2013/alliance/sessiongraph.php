<?

session_start();

//// Define and Setup Visual Informaiton

// create a 200*200 image

$frame = $_SESSION['dimension']['frame'];
$innerframe =  $_SESSION['dimension']['innerframe'];
$unit =  $_SESSION['dimension']['unit'];
$space =  $_SESSION['dimension']['space'];
$dimensionkey = $_SESSION['dimension']['key'];
$key = $_SESSION['key'];
$data = $_SESSION['data'];
$report = $_GET['report'];
$type = $_GET['type'];
$source = $_GET['source'];
//echo "<pre>";

$criteria = $_SESSION['report'][$report][$type][$source][2];
$scale = $_SESSION['report'][$report][$type][$source][3][0];
//print_r($criteria);
//print_r($data);
//echo "</pre>";

$img = imagecreatetruecolor($frame[0], $frame[1]);

// allocate some colors
$color = array();
foreach ($criteria as $colorname => $colorresource)
	$color[$colorname] = imagecolorallocate($img, $colorresource[0], $colorresource[1], $colorresource[2]);

$white = imagecolorallocate($img, 255,255,255);
$innerframecol = imagecolorallocate($img, 100,100,100);
$background = imagecolorallocate($img,155,155,155);

/// Fill and paint frame


imagefilledrectangle($img, 0, 0, $frame[0], $frame[1], $background);
imagerectangle($img, $innerframe[0],$innerframe[1], $innerframe[2], $innerframe[3], $innerframecol);

$LeftKey = $dimensionkey[0];

foreach ($color as $colorname => $colorresource)
	{
	ImageString($img, 5 ,$LeftKey,$dimensionkey[1],$colorname, $colorresource); 
	$LeftKey = $LeftKey + 80;
	}


foreach ($data as $hostname => $data)
  foreach ($data as $date => $data)
   foreach ($data as $serial => $data)
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

// output image in the browser
header("Content-type: image/png");
imagepng($img);



// free memory
imagedestroy($img);

?>
