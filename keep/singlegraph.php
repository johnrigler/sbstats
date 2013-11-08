<link rel="stylesheet" type="text/css" href="stats.css" />
<html>
<?

include 'site-config.php';  
include 'library.php';

global $Sets; // From site-config.php
global $Reports; // From site-config.php
global $FileDir; // From site-config.php

$Stream = $_GET['stream'];
list( $StreamDir,$Trash) = explode("_",$Stream);
$Report = $_GET['report'];
$Granularity = $_GET['granularity'];

$Today = $_GET['ymd'];
$TodayYear = substr($Today,0,4);
$Yesterday = CalculateYesterday($Today);
$ThisWeek = $_GET['week'];
$LastWeek = CalculateLastWeek($ThisWeek);
$NextWeek = CalculateLastWeek($ThisWeek,-1);
$YesterdayYear = substr($Yesterday,0,4);
$Tomorrow = CalculateYesterday($Today,-1);

$TomorrowYear = substr($Tomorrow,0,4);
$ThisWeekYear = substr($ThisWeek,0,4);
$LastWeekYear = substr($LastWeek,0,4);
$NextWeekYear = substr($NextWeek,0,4);


if($Today)
{
$Date[0] = $Yesterday;
$Date[1] = $Today;
$Date[2] = $Tomorrow;

$Year[0] = $YesterdayYear;
$Year[1] = $TodayYear;
$Year[2] = $TomorrowYear;
}

if($ThisWeek)
{
$Date[0] = $LastWeek;
$Date[1] = $ThisWeek;
$Date[2] = $NextWeek;

$Year[0] = $LastWeekYear;
$Year[1] = $ThisWeekYear;
$Year[2] = $NextWeekYear;
}

if($Today)
{
for( $Day = 0 ; $Day <= 2; $Day++ )
  {
  $FullFileName[$Day] = CatArray(array($FileDir,$Year[$Day],$StreamDir,$Granularity,$Date[$Day] . "_$Granularity" . "_$Stream.sbstats"),"/");
    if(file_exists($FullFileName[$Day]))$AnyDay[$Day]++;
  }
}

if($ThisWeek)
{
for( $Day = 0 ; $Day <= 2; $Day++ )
  {
  $FullFileName[$Day] = CatArray(array($FileDir,$Year[$Day],$StreamDir,"week",$Date[$Day] . "_$Stream.sbstats"),"/");
    if(file_exists($FullFileName[$Day]))$AnyDay[$Day]++;
  }

}

DebugArray("FullFileName");
DebugArray("AnyDay");

?>

<table><tr>

<?
if($Today)
   {
   if($AnyDay[0])
	{
	$YesterdayPath="singlegraph.php?stream=$Stream&ymd=$Yesterday&granularity=$Granularity&report=$Report";
        echo "<td><a href=$YesterdayPath> <img src='leftarrow.png'> </td></a>";
	}

   if($AnyDay[2])
	{
	$TomorrowPath="singlegraph.php?stream=$Stream&ymd=$Tomorrow&granularity=$Granularity&report=$Report";
	echo "<td><a href=$TomorrowPath> <img src='rightarrow.png'> </td></a>";
	}
   }

if($ThisWeek)
   {
   if($AnyDay[0])
        {
        $LastWeekPath="singlegraph.php?stream=$Stream&week=$LastWeek&report=$Report";
        echo "<td><a href=$LastWeekPath> <img src='leftarrow.png'> </td></a>";
        }

   if($AnyDay[2])
        {
        $NextWeekPath="singlegraph.php?stream=$Stream&week=$NextWeek&report=$Report";
        echo "<td><a href=$NextWeekPath> <img src='rightarrow.png'> </td></a>";
        }
   }

?>

        <td><a href=/index.php> [Site Homepage] </a></td>
        <td><a href=index.php> [Stats Homepage] </a></td>
</tr>
</table>

<?

  if($Today)
    {
	echo "<img src=graph.php?stream=$Stream&ymd=$Today&granularity=$Granularity&report=$Report>";
    }

  if($ThisWeek)
    {
        echo "<img src=graph.php?stream=$Stream&week=$ThisWeek&report=$Report>";
    }


?>
</html>
