<?php

error_reporting(0);

include '/home/secrets/www/stats/library.php';
include '/home/secrets/www/stats/site-config.php';
include '/home/secrets/www/stats/colors.php';
include '/home/secrets/www/stats/2013/mapweek.php';

$FileName = "$_GET[file]";

list( $Start,$HomeDir, $Year , $Name , $Other )  = explode('/', $_SERVER[REQUEST_URI]);

$Granularity = "05";

function Segment($x, $y1, $y2, $color)
{

global $LineWidth;
global $OuterPadding;
global $TotalWidth;
global $GraphBottom;
global $UnitHeight;
global $img;

$RightEdge =  $x + $TotalWidth - $OuterPadding;
$LeftEdge = $x + $OuterPadding;
$BottomEdge = $GraphBottom - $y1 * $UnitHeight;
$TopEdge = $BottomEdge - $y2 * $UnitHeight;

imagefilledrectangle($img,$LeftEdge, $TopEdge, $RightEdge, $BottomEdge , $color);
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


function CreateGrid( $color ) {

global $GraphBottom;
global $img;
global $GraphRight;
global $GraphLeft;
global $UnitHeight;

global $GridHeight;
global $HorizontalGridMark;

$GraphLeft += 40;
$GraphRight += 1100;
$DayWidth = ($GraphRight - $GraphLeft) / 7;

for ($x = 0 ; $x < 7 ; $x++){
$Xcoord = $GraphLeft + ($DayWidth * $x) -1;
imageline($img,$Xcoord ,$GraphBottom + 16 ,$Xcoord ,$GraphBottom-200, $color);
ImageString($img, 4, $Xcoord, $GraphBottom + 20, PrettyDay($x),$color);
	for($xx = 0; $xx < 24; $xx++)
		{
		$hourwidth = 6.31;
		imageline($img,$Xcoord + ($xx * $hourwidth),$GraphBottom + 1,$Xcoord + ($xx * $hourwidth) ,$GraphBottom + 10,$color);
		}

}



$GridBottom = $GraphBottom + 1;

imagerectangle( $img, $GraphLeft - 1, $GridBottom, $GraphRight + 1, $GridBottom - $GridHeight * $UnitHeight, $color);

for ($x = 0; $x <= $GridHeight; $x = $x + $HorizontalGridMark)
	{
	$GridLine = $GridBottom - $x * $UnitHeight;
	imageline($img,$GraphLeft -1, $GridLine, $GraphRight + 1, $GridLine, $color);
	ImageString($img, 4, $GraphLeft - 28, $GridLine - 7, $x, $color);
	}

}

function Year( $YMD )
{
return substr($YMD,0,4);
}

function PrettyName( $FileName ) {

$tempymd1 = substr($FileName,0,4);
$tempymd2 = substr($FileName,4,2);
$tempymd3 = substr($FileName,6,2);
$tempname = explode(".",substr($FileName,12));

return "$tempymd2/$tempymd3/$tempymd1  $tempname[0]";


}


// MAIN Start Here also Begin

// create base image

//include '/var/www/stats/library.php';
//include '/var/www/stats/site-config.php';

global $ChartBar;
global $GraphConfiguration;

$Type = $_GET['report'];


  $UnitArray = "oneweek";

$dirname = ".";
$dir = opendir($dirname);

$dirFiles = array();
$dirDays = array();
$week = array();

$week['0']['0000'] = "x";


while(false != ($file = readdir($dir)))
{
$stuff = explode(".",$file);
if($stuff[1] == "sbstats")
        $dirFiles[] = $file;

}

sort ($dirFiles);

$week = array();

for ($x = 0 ; $x < 7; $x++)
        {
        for ($y = 0; $y < 24; $y++)
                {
                $valy = sprintf('%02d', $y);
                for ($z = 0; $z < 60; $z = $z + 5)
                        {
                        $valz = sprintf('%02d', $z);
                        $val = $valy . $valz;
                        $week[$x][$val] = "";
                        }
                }
        }


foreach($dirFiles as $file)
        {
	$day = substr($file,10,1);
	$dirDays[$day] = $file;
        list( $date,$serial,$machinedesc,$host,$type,$report) = explode(":",$file);
        $temp = explode(".",$report);
        $report = $temp[0];

 	if(($handle = fopen($dirDays[$day], "r")) !== FALSE) 
    	{
    	while (($data = fgetcsv($handle, 1000, " ")) !== FALSE) 
		{
		$key = substr($data[0],0,2) . substr($data[0],3,2);
		$week[$day][$key] = $data[2];
        	}
    	fclose($handle);
	}
    }	

//print_r($week);


list($Class,$Subclass,$Data) = explode(" ",$header);

$Type = "aix1";

if ($GraphConfiguration[$Type]['facts'])	
  {
  $MyFacts = $GraphConfiguration[$Type]['facts'];
 
  foreach (${$UnitArray} as $index => $csv)
    {
     $factoids = explode(",",$csv);
     foreach($MyFacts as $index2 => $FactSet)
       {
       list($Position,$Description) = explode(";",$FactSet);
       $ResortingArray[$index2] = $factoids[$Position];
       }
     ${$UnitArray}[$index] = implode(",",$ResortingArray);
    }
  }
else
  {
  $MyFacts = $Header;	
  }

DebugArray("week");


$Title = PrettyName($FileName);

  foreach ($MyFacts as $index => $FactDescription)
    {
    $TempFactDescription = explode(";",$FactDescription);
    $MyFacts[$index] = $TempFactDescription[1];
    }

$GridHeight = $GraphConfiguration[$Type]['gridheight'];
$UnitHeight = $GraphConfiguration[$Type]['unitheight'];
$HorizontalGridMark = $GraphConfiguration[$Type]['gridmarks'];

$PageHeight = $GridHeight * $UnitHeight + 140;

$img = imagecreatetruecolor(1120, $PageHeight);

DebugArray("MyFacts");

//include '/home/secrets/www/stats/colors.php';


// Set the Background of the Graph

imagefilledrectangle( $img, 0, 0, 1120, $PageHeight, $GraphBackgroundColor );



ImageString($img,4,10,8,"$Type $Granularity min.",$TextColor);
ImageString($img,4,10,24,"$Title",$TextColor);


global $GraphDimensions;

  $GraphBottom = $PageHeight - 70;

  $TotalWidth = $GraphDimensions[$Granularity]['TotalWidth'];
  $OuterPadding = $GraphDimensions[$Granularity]['OuterPadding'];
  $GraphLeft = $GraphDimensions[$Granularity]['GraphLeft'];
  $UnitsPerHour = $GraphDimensions[$Granularity]['UnitsPerHour'];
  $TotalUnits = $UnitsPerHour * 24;

  $GraphRight = $GraphLeft + ($TotalWidth * $TotalUnits);

$GridColor = imagecolorallocate($img, 100,100,80);

////  CreateGrid( $GridColor );

 // imagefilledrectangle($img,$LeftEdge, $TopEdge, $RightEdge, $BottomEdge , $color);

$TextColor2 = imagecolorallocate($img, 100,100,80);
 imagefilledrectangle($img,10,10,20,20,$TextColor2);

  //Map($GraphDimensions[$Granularity]['MapArgument']);

  $GraphBottom = $GraphBottom + 20;
  $UnitHeight = 2;
 
  for ($x = 0 ; $x < $TotalUnits ; $x = $x + $UnitsPerHour) // Draw Hour Names and tick marks`
  {
  Segment( $GraphLeft + ($TotalWidth * $x), 1,8, $GridColor);
  $Hour = $x / $UnitsPerHour;
  ImageString($img, 8, $GraphLeft + ($TotalWidth * $x), $GraphBottom + 5, PrettyHour($Hour), $GridColor);
  }

  for ($x = 0; $x < $TotalUnits ; $x = $x + ($UnitsPerHour / 2)) // Draw 30 minute Tick Marks 
  {
  Segment( $GraphLeft + ($TotalWidth * $x), 4,5, $GridColor);
  }

  for ($x = 0; $x < $TotalUnits ; $x = $x + ($UnitsPerHour / 4)) // Draw 15 minute Tick Marks
  {
  Segment( $GraphLeft + ($TotalWidth * $x), 6,3, $GridColor);
  }


/**/
//$GraphBottom = 300;

// Create the Key

$keyX = $GraphLeft + 240;
$keyY = 10;
$keyW = 15;
$keyH = 15;

$KeyLeft = 100;

$Count = 0;

$Counter = 0;

foreach ($MyFacts as $i => $MyFact)
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


	imagefilledrectangle($img,$keyX,$keyY,$keyX + $keyW,$keyY + $keyH ,$ChartBar[$i]);
	imagerectangle($img,$keyX,$keyY,$keyX + $keyW,$keyY + $keyH ,$Black);
	$MyFact2 = explode(";",$MyFact);
	if($MyFact2[1])$MyFact = $MyFact2[1];
	ImageString( $img , 3 , $keyX + 27 , $keyY + 4 , $MyFact , $TextColor );
	$keyX = $keyX + 151;
	$Counter++;
	}

// output image in the browser

if(! array_key_exists('debug',$_GET))
  {
  header("Content-type: image/png");
  imagepng($img);
  }


// free memory
imagedestroy($img);

?>

