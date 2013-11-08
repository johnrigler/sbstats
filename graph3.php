<?php


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

function PrettyDay( $Day ) {

if($Day == 1)$Day = "Mon";
if($Day == 2)$Day = "Tue";
if($Day == 3)$Day = "Wed";
if($Day == 4)$Day = "Thu";
if($Day == 5)$Day = "Fri";
if($Day == 6)$Day = "Sat";
if($Day == 7)$Day = "Sun";

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
$GridBottom = $GraphBottom + 1;

imagerectangle( $img, $GraphLeft - 1, $GridBottom, $GraphRight + 1, $GridBottom - $GridHeight * $UnitHeight, $color);

//echo "xxxx $GridHeight $HorizontalGridMark<br>";

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

function PrettyYMD( $ymd ) {

$tempymd1 = substr($ymd,0,4);
$tempymd2 = substr($ymd,4,2);
$tempymd3 = substr($ymd,6,2);

return "$tempymd2/$tempymd3/$tempymd1";


}


// MAIN Start Here also Begin

// create base image

include 'library.php';
include 'site-config.php';

global $ChartBar;
global $GraphConfiguration;

$Stream = $_GET['stream'];
list($StreamName,$StreamType) = explode("_",$Stream);
$Type = $_GET['report'];
$Week = $_GET['week'];
$YMD = $_GET['ymd'];
$Granularity = $_GET['granularity'];

if ($Week)
  {
  $NewName[0] = $FileDir;
  $NewName[1] =  substr($_GET['week'],0,4);
  $NewName[2] = $StreamName;
  $NewName[3] = "week";
  $NewName[4] = $_GET['week'] . "_" .  $Stream . ".sbstats";
  include 'oneweek.php';
  include 'mapweek.php';
//echo "/buxs/sbstats2/2010/nad0019aixd02/week/2010-W32_nad0019aixd02_aixbase.sbstats";

  }

if ($YMD)
  {
  $NewName[0] = $FileDir;
  $NewName[1] = substr($YMD,0,4);
  $NewName[2] = $StreamName;
  $NewName[3] = $Granularity;
  $NewName[4] = $YMD . "_" . $Granularity . "_" .  $Stream . ".sbstats";
  $ServerYear = substr($YMD,0,4);
  $ServerMonth = substr($YMD,4,2);
  $ServerDay = substr($YMD,6,2);
  $PrettyYMD = PrettyYMD( $YMD );
  $UnitArray = "oneday";

  include 'oneday.php';
  include 'mapday.php';
  }

//DebugArray( "GraphConfiguration" );

//$ServerYear = substr($YMD,0,4);
//$ServerMonth = substr($YMD,4,2);
//$ServerDay = substr($YMD,6,2);
//$PrettyYMD = PrettyYMD( $YMD );

DebugArray( "NewName" );

$FileName = CatArray($NewName , "/");

$GraphData = array ( $FileName , $ServerYear , $ServerMonth , $ServerDay , $Stream , $PrettyYMD );

DebugArray( "GraphData" );

if (($handle = fopen("$FileName", "r")) !== FALSE) 
    {
    $header = fgetcsv($handle, 1000, " ");
    while (($data = fgetcsv($handle, 1000, " ")) !== FALSE) 
	{
        $key = $data[0];
        if($YMD){if(isset($data[2]))$oneday[$key] = $data[2];}
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
//$MyFacts = $GraphConfiguration[$Type]]['facts'];

if ($GraphConfiguration[$Type]['facts'])	
  {
  $MyFacts = $GraphConfiguration[$Type]['facts'];
  DebugArray("MyFacts");
 
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

DebugArray("MyFacts");
DebugArray("Header");
//DebugArray("oneday");

$Title = $GraphData[5];

if($Week)
   {
   $oneweek[$header[0]] = $header[2];

  foreach ($oneweek as $index => $csv)
    {
     $shoulddiscard = substr($index,2,1);

     $factoids = explode(",",$csv);
     foreach($MyFacts as $index2 => $FactSet)
       {
       list($Position,$Description) = explode(";",$FactSet);
       $ResortingArray[$index2] = $factoids[$Position];
       }
     $oneweek[$index] = implode(",",$ResortingArray);
   }


   DebugArray("oneweek");
   $UnitArray = "oneweek";

  foreach ($MyFacts as $index => $FactDescription)
    {
    $TempFactDescription = explode(";",$FactDescription);
    $MyFacts[$index] = $TempFactDescription[1];
    }


}

DebugArray( "MyFacts" );

$GridHeight = $GraphConfiguration[$Type]['gridheight'];
$UnitHeight = $GraphConfiguration[$Type]['unitheight'];
$HorizontalGridMark = $GraphConfiguration[$Type]['gridmarks'];

$PageHeight = $GridHeight * $UnitHeight + 140;

$img = imagecreatetruecolor(1120, $PageHeight);

include 'colors.php';

// Set the Background of the Graph

imagefilledrectangle( $img, 0, 0, 1120, $PageHeight, $GraphBackgroundColor );

if($Week)$Title = $Week;

//ImageString($img,4,10,10,"$Stream",$TextColor);
//ImageString($img,4,10,24,"$Title",$TextColor);

// Weekly Increments

if ($Week)
{
$PercentHeight = 40;
$TotalWidth = 6;
$OuterPadding = 1;
$GraphBottom = 250;
$GraphLeft = 40;
$GraphRight = $GraphLeft + ($TotalWidth * 168);
CreateGrid( $GridColor );
Map(1);
$GraphBottom = $GraphBottom + 20;
for ($x = 0 ; $x < 168 ; $x = $x + 24)
{
Segment( $GraphLeft + ($TotalWidth * $x), 0,5, $GridColor);
$Day = ($x / 24) + 1; 
ImageString($img, 8, $GraphLeft + ($TotalWidth * $x), $GraphBottom + 5, PrettyDay($Day), $GridColor);
}

}

global $GraphDimensions;

  $GraphBottom = $PageHeight - 70;

  $TotalWidth = $GraphDimensions[$Granularity]['TotalWidth'];
  $OuterPadding = $GraphDimensions[$Granularity]['OuterPadding'];
  $GraphLeft = $GraphDimensions[$Granularity]['GraphLeft'];
  $UnitsPerHour = $GraphDimensions[$Granularity]['UnitsPerHour'];
  $TotalUnits = $UnitsPerHour * 24;

  $GraphRight = $GraphLeft + ($TotalWidth * $TotalUnits);

  if($YMD)CreateGrid( $GridColor );

  if($YMD){Map($GraphDimensions[$Granularity]['MapArgument']);}

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


//	imagefilledrectangle($img,$keyX,$keyY,$keyX + $keyW,$keyY + $keyH ,$ChartBar[$i]);
//	imagerectangle($img,$keyX,$keyY,$keyX + $keyW,$keyY + $keyH ,$Black);
//	$MyFact2 = explode(";",$MyFact);
//	if($MyFact2[1])$MyFact = $MyFact2[1];
//	ImageString( $img , 3 , $keyX + 27 , $keyY + 4 , $MyFact , $TextColor );
//	$keyX = $keyX + 151;
//	$Counter++;
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

