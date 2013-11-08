<link rel="stylesheet" type="text/css" href="stats.css" />
<html>
<?

include 'site-config.php'; 
include 'library.php';

global $Sets; // From site-config.php
global $Reports; // From site-config.php
global $FileDir; // From site-config.php

$Set = $_GET['set'];
$Granularity = $_GET['granularity'];
$ThisWeek = $_GET['week'];
$ThisWeekYear = substr($ThisWeek,0,4);
#$ = CalculateYesterday($Today);
#$YesterdayYear = substr($Yesterday,0,4);
#$Tomorrow = CalculateYesterday($Today,-1);
#$TomorrowYear = substr($Tomorrow,0,4);

echo "<br>$ThisWeekYear<br>";

$Date[0] = $Yesterday;
$Date[1] = $ThisWeek;
$Date[2] = $Tomorrow;

$Year[0] = $YesterdayYear;
$Year[1] = $ThisWeekYear;
$Year[2] = $TomorrowYear;


$ThisSet = $Sets[$Set];  // Use the URL get and site-config to derrive ThisSet

DebugArray("ThisSet");
DebugArray("Reports");

// Now figure out the best possible files and reports for ThisSet

for( $Week = 0 ; $Week <= 2; $Week++ )
  {
  $Position = 0;
  foreach($ThisSet as $SetElement)
    { 
    list($Stream,$Type) = explode("_",$SetElement);

    $TempArray = array($FileDir, $Year[$Week], $Stream, "week" , $Date[$Week] . "_" .  
              $Stream . "_" . $Reports[$Type] . ".sbstats");

    echo "<br>$FileDir $Year[$Week] $Stream ";

    $ToGraph[$Position][$Week] = array(CatArray($TempArray , "/"),$Type);
    echo $ToGraph[$Position][$Week][0];
    if(file_exists($ToGraph[$Position++][$Week][0]))$AnyWeek[$Week]++;
    }
  }

if($AnyWeek[0])$YesterdayPath="graphset.php?set=$Set&ymd=$Yesterday&granularity=$Granularity";
if($AnyWeek[2])$TomorrowPath="graphset.php?set=$Set&ymd=$Tomorrow&granularity=$Granularity";

// Now figure out the current URLs to display on this page for the files that exist

DebugArray("ToGraph");

?>

<table><tr>

<?
        if($AnyWeek[0])echo "<td><a href=$LastWeekPath> <img src='leftarrow.png'> </td></a>";
        if($AnyWeek[2])echo "<td><a href=$NextWeekPath> <img src='rightarrow.png'> </td></a>";
?>

        <td><a href=/index.php> [Site Homepage] </a></td>
        <td><a href=index.php> [Stats Homepage] </a></td>
</tr>
</table>

<?



foreach($ToGraph as $Stream)
  {
//  DebugArray("Stream");
  if(file_exists($Stream[1][0]))
    {
    $TempArray = explode("/",$Stream[1][0]);
    DebugArray("TempArray");
    list($ThisYMD,$ThisGranularity,$ThisStream,$ThisType) = explode("_",$TempArray[6]);
    $ThisType = explode(".",$ThisType); 
    $Report = $Stream[1][1];
    echo "<br><img src=graph.php?ymd=$ThisYMD&granularity=$ThisGranularity&stream=$ThisStream" . "_" . "$ThisType[0]&report=$Report>";
    }
  }

DebugArray("AnyWeek");

?>
</html>
