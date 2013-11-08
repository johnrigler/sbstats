<link rel="stylesheet" type="text/css" href="stats.css" />
<html>


<?

$Config = $_GET['config'];
include 'site-config.php';
include 'library.php';

global $FileDir;
global $Servers;
global $Group;
global $Granularities;

include 'stream-data.php';

global $FileDir;
global $Group;
global $StreamData;
global $Associations;

$ThisAssociation = $Associations[$Config];

DebugArray("StreamData");

DebugArray("ThisAssociation");



/*
function yesterday( $YMD , $Daysago = 1 )
{
$Year = substr($YMD,0,4);
$Month = substr($YMD,4,2);
$Day = substr($YMD,6,2);
$today = mktime(0,0,0,$Month,$Day,$Year);
return date("Ymd", $today - 86400 * $Daysago);
}*/

function lastweek( $ISOWeek )
{
$Week = substr($ISOWeek,6,2);
$Year = substr($ISOWeek,0,4);

if ($Week > 1)
  { 
  $LastWeek = $Week - 1 ;
  $LastWeekF = sprintf("%02d",$LastWeek);
  return "$Year-W$LastWeekF";
  }

if ($Week == 1)
  {
  $LastYear = $Year - 1;
  $LastWeek = 52;
  return "$LastYear-W$LastWeek";
  }


}

function nextweek( $ISOWeek )
{
$Week = substr($ISOWeek,6,2);
$Year = substr($ISOWeek,0,4);

if ($Week < 51)
  {
  $NextWeek = $Week + 1 ;
  $NextWeekF = sprintf("%02d",$NextWeek);
  return "$Year-W$NextWeekF";
  }

if ($Week == 52)
  {
  $NextYear = $Year + 1;
  $NextWeek = 1;
  return "$NextYear-W$NextWeek";
  }


}




$ISOWeek = $_GET['week'];
$TodayYear = substr($ISOWeek,0,4);
$Yesterday = CalculateYesterday($Today);
$YesterdayYear = substr($Yesterday,0,4);
$Tomorrow = CalculateYesterday($Today,-1);
$TomorrowYear = substr($Tomorrow,0,4);

$ThisWeekYear = substr($ISOWeek,0,4);
$LastWeek = lastweek( $ISOWeek );
$NextWeek = nextweek( $ISOWeek );
$LastWeekYear = substr($LastWeek,0,4);
$NextWeekYear = substr($NextWeek,0,4);

 

foreach($ThisAssociation as $index => $Server)
	{
	  $Temp = "$FileDir/$LastWeekYear/$Server/week/$LastWeek" . "_" . "$Server" . ".sbstats";
	  if(file_exists($Temp))
		$LastWeekResults[$Server] = $Temp;

          $Temp = "$FileDir/$ThisWeekYear/$Server/week/$ISOWeek" . "_" . "$Server" . ".sbstats";
          if(file_exists($Temp))
                $ThisWeekResults[$Server] = $Temp;

          $Temp = "$FileDir/$NextWeekYear/$Server/week/$NextWeek" . "_" . "$Server" . ".sbstats";
          if(file_exists($Temp))
                $NextWeekResults[$Server] = $Temp;


	}

DebugArray("LastWeekResults");
DebugArray("ThisWeekResults");
DebugArray("NextWeekResults");

$LastWeekPath="";
$NextWeekPath="";

if($LastWeekResults)
foreach($LastWeekResults as $Display)
  {
  if($Display)$LastWeekPath="weekset.php?config=$Config&week=" . $LastWeek;
  }

if($NextWeekResults)
foreach($NextWeekResults as $Display)
  {
  if($Display)$NextWeekPath="weekset.php?config=$Config&week=" . $NextWeek;
  }

?>

<table><tr>

<?
        echo "<td><a href=$LastWeekPath> <img src=leftarrow.png></a>";
        echo "<td><a href=$NextWeekPath> <img src=rightarrow.png></a>";
?>

        <td><a href=/index.php> [Site Homepage] </a></td>
        <td><a href=index.php> [Stats Homepage] </a></td>

</tr>
</table>

<?

foreach($ThisAssociation as $index => $Server)
  { 
  $Type = $StreamData[$Server][1];
  if($Display)
        echo "<img src=graph.php?stream=$Server&week=$ISOWeek&type=$Type><br>";
  }

?>
