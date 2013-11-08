<?php

error_reporting(0);

include '/home/secrets/www/stats/library.php';
include '/home/secrets/www/stats/site-config.php';

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


function PrettyHour( $Hour ) {

if($Hour == 0 )$Hour = "12pm";
if($Hour == 1 )$Hour = "1am";
if($Hour == 2 )$Hour = "2am";
if($Hour == 3 )$Hour = "3am";
if($Hour == 4 )$Hour = "4am";
if($Hour == 5 )$Hour = "5am";
if($Hour == 6 )$Hour = "6am";
if($Hour == 7 )$Hour = "7am";
if($Hour == 8 )$Hour = "8am";
if($Hour == 9 )$Hour = "9am";
if($Hour == 10 )$Hour = "10am";
if($Hour == 11 )$Hour = "11am";
if($Hour == 12 )$Hour = "12am";
if($Hour == 13 )$Hour = "1pm";
if($Hour == 14 )$Hour = "2pm";
if($Hour == 15 )$Hour = "3pm";
if($Hour == 16 )$Hour = "4pm";
if($Hour == 17 )$Hour = "5pm";
if($Hour == 18 )$Hour = "6pm";
if($Hour == 19 )$Hour = "7pm";
if($Hour == 20 )$Hour = "8pm";
if($Hour == 21 )$Hour = "9pm";
if($Hour == 22 )$Hour = "10pm";
if($Hour == 23 )$Hour = "11pm";

return $Hour;

}

function CreateGrid( $color ) {

global $GraphBottom;
global $img;
global $GraphRight;
global $GraphLeft;
global $UnitHeight;

global $GridHeight;
global $HorizontalGridMark;
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


  $UnitArray = "oneday";

include '/home/secrets/www/stats/oneday.php';
include '/home/secrets/www/stats/mapday.php';


if (($handle = fopen("$FileName", "r")) !== FALSE) 
    {
    $header = fgetcsv($handle, 1000, " ");
    while (($data = fgetcsv($handle, 1000, " ")) !== FALSE) 
	{
        $key = $data[0];
        if(isset($data[2]))$oneday[$key] = $data[2];
        if(is_numeric(substr($key,2,1)))
		if($Week)
			if(isset($data[2]))
				$oneweek[$key] = $data[3];

        }
    fclose($handle);
    }

list($Class,$Subclass,$Data) = explode(" ",$header);

$reverseheader = array_reverse($header);
$Class = array_pop($reverseheader);
$SubClass = array_pop($reverseheader);
$Header = array_reverse($reverseheader);

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

DebugArray("Header");
DebugArray("oneday");

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

include '/home/secrets/www/stats/colors.php';

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

  CreateGrid( $GridColor );

  Map($GraphDimensions[$Granularity]['MapArgument']);

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

