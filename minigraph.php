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

if($Hour == 0 )$Hour = "12";
if($Hour == 4 )$Hour = "4";
if($Hour == 8 )$Hour = "8";
if($Hour == 12 )$Hour = "12";
if($Hour == 16 )$Hour = "4";
if($Hour == 20 )$Hour = "8";

return $Hour;

}

function MapDay ( $modulator ) {

$counter = 0;

global $oneday;
global $GraphLeft;
global $TotalWidth;
global $MyFacts;
global $img;

DebugArray("MyFacts");

foreach(array_reverse($MyFacts) as $i => $value)
  {
  $ExValues = explode(";",$value);
  $Position[$i] = $ExValues[0];
  $Title[$i] = $ExValues[1];
  $ExColor = explode(",",$ExValues[2]);
  $Color[$i] = imagecolorallocate($img,$ExColor[0],$ExColor[1],$ExColor[2]);
  $ReverseColor = array_reverse($Color);
  }

$counter = 0;

foreach($oneday as $i => $value) {
        $x = explode(",",$value);
        $mod = $counter % $modulator;
        if($mod == 0)
        {
	foreach($MyFacts as $i => $Fact)
	   {
	    $Data[$i] = explode(";",$Fact);
	 $Val[$i] = $x[$Data[$i][0]];
     	}
	
	$last = 0;
        foreach($Data as $i => $Seg)
	 {
           $Stripped = ereg_replace("[^A-Za-z0-9]", "", $Val[$i]);
	   if($Val[$i])
	   {
	   Segment( $GraphLeft + ( $TotalWidth * ($counter/$modulator)), $last , $Stripped  , $ReverseColor[$i]);
	   $last = $last + $Stripped;
	   }
         }

        }
        $counter++;
}

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

function PrettyYMD( $ymd ) {

$tempymd1 = substr($ymd,0,4);
$tempymd2 = substr($ymd,4,2);
$tempymd3 = substr($ymd,6,2);

return "$tempymd2/$tempymd3/$tempymd1";


}


// MAIN Start Here also Begin

// create base image

/*$GridHeight = 1000;
$UnitHeight = .25; 
$HorizontalGridMark = 50;
$PageHeight = $GridHeight * $UnitHeight + 140;
//$img = imagecreatetruecolor(1120, $PageHeight);
*/

include 'oneday.php';
//include 'colors.php';
include 'library.php';
include 'site-config.php';

$Config = $_GET['config'];
$ConfigName = $Config . "-config.php";
include $ConfigName;

global ${$Config};

$YMD = $_GET['ymd'];
$Granularity = $_GET['granularity'];
$Stream = $_GET['stream'];
$GraphInfo = ${$Config}[$Stream];

//DebugArray( "_GET" );
//DebugArray( "cashx" );


$Name = $_GET['ymd'];

$ServerYear = substr($YMD,0,4);
$ServerMonth = substr($YMD,4,2);
$ServerDay = substr($YMD,6,2);
$PrettyMDY = PrettyYMD( $YMD );

$NewName[0] = $FileDir;
$NewName[1] = substr($YMD,0,4);
$NewName[2] = $GraphInfo['source']; 
$NewName[3] = $Granularity;
$NewName[4] = $Name . "_" . $Granularity . "_" .  $GraphInfo['source'] . ".sbstats";

DebugArray( "NewName" );


DebugArray ( "GraphInfo" );

$FileName = CatArray($NewName , "/");

$GraphData = array ( $FileName , $ServerYear , $ServerMonth , $ServerDay , $GraphInfo['source'] , $PrettyMDY );

DebugArray( "GraphData" );

if (($handle = fopen("$FileName", "r")) !== FALSE) 
    {
    while (($data = fgetcsv($handle, 1000, " ")) !== FALSE) 
	{
        $key = $data[0];
        $oneday[$key] = $data[1];
        }
    fclose($handle);
    }

DebugArray( "oneday" );

$MyFacts = $GraphInfo['facts'];

DebugArray( "MyFacts" );

$GridHeight = $GraphInfo['gridheight'];
$UnitHeight = $GraphInfo['unitheight'];
$HorizontalGridMark = $GraphInfo['gridmarks'];
$PageHeight = $GridHeight * $UnitHeight + 140;

$img = imagecreatetruecolor(240, 260);

include 'colors.php';

// Set the Background of the Graph

imagefilledrectangle( $img, 0, 0, 1120, $PageHeight, $GraphBackgroundColor );

ImageString($img,16,4,0,"$GraphData[4] $GraphData[5]",$TextColor);

// 15 Minute Increments

if ($Granularity == "15")
{
$PercentHeight = 4 ;
$TotalWidth = 2; 
$OuterPadding = 1;
$GraphBottom = 226;
$GraphLeft = 40;
$GraphRight = $GraphLeft + ($TotalWidth * 96);
//$UnitHeight = 2; 
CreateGrid( $GridColor );
MapDay(3);
$GraphBottom = $GraphBottom + 20;
for ($x = 0 ; $x < 96 ; $x = $x + 16)
{
Segment( $GraphLeft + ($TotalWidth * $x), 5,3, $GridColor);
$Hour = $x / 4 ;
ImageString($img, 8, $GraphLeft + ($TotalWidth * $x), $GraphBottom - 8 , PrettyHour($Hour) , $GridColor);
}
}

// 5 Minute Increments

if ($Granularity == "05")
{
$TotalWidth = 3.7;
$OuterPadding = 1;
$GraphBottom = $PageHeight - 70;
$GraphLeft = 30;

$GraphRight = $GraphLeft + ($TotalWidth * 288);
//$UnitHeight = 2;
CreateGrid( $GridColor );

MapDay(1);

$GraphBottom = $GraphBottom + 20;
$UnitHeight = 2;
 
for ($x = 0 ; $x < 288 ; $x = $x + 12) // Draw Hour Names and tick marks
{
Segment( $GraphLeft + ($TotalWidth * $x), 1,8, $GridColor);
$Hour = $x / 12;
//ImageString($img, 8, $GraphLeft + ($TotalWidth * $x), $GraphBottom , PrettyHour($Hour), $GridColor);
}

}

//$GraphBottom = 300;

// Create the Key

$keyX = $GraphLeft - 10;
$keyY = 430;
$keyW = 5;
$keyH = 20;

$KeyLeft = 80;

$Count = 0;

foreach (array_reverse($MyFacts) as $Fact)
	{
	
	$ExFact = explode(";",$Fact);
	$Colors = explode(",",$ExFact[2]);
	$ThisColor = imagecolorallocate($img, $Colors[0], $Colors[1], $Colors[2]);

	imagefilledrectangle($img,$keyX,$keyY,$keyX + $keyW,$keyY + $keyH ,$ThisColor);
	imagerectangle($img,$keyX,$keyY,$keyX + $keyW,$keyY + $keyH ,$Black);
	ImageString( $img , 3 , $keyX + 10 , $keyY + 4 , $ExFact[1] , $TextColor );
	$keyX = $keyX + 70;
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

