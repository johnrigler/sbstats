<link rel="stylesheet" type="text/css" href="stats.css" />
<html>


<?

$Config = $_GET['config'];
include 'stream-data.php';
include 'library.php';
include 'site-config.php';


$YMD = $_GET['ymd'];


echo "<br><br>";

global $FileDir;
global $Group;
global $StreamData;
global $Associations;

$ThisAssociation = $Associations[$Config];

DebugArray("ThisAssociation");


$Today = $_GET['ymd'];
$TodayYear = substr($Today,0,4);
$Yesterday = CalculateYesterday($Today);
$YesterdayYear = substr($Yesterday,0,4);
$Tomorrow = CalculateYesterday($Today,-1);
$TomorrowYear = substr($Tomorrow,0,4);


foreach($ThisAssociation as $Stream)
	{
	$ThisStreamData = $StreamData[$Stream];
	DebugArray("ThisStreamData");	
	foreach($Granularities as $Granularity)
	  {
	  $Temp = "$FileDir/$YesterdayYear/$Stream/$Granularity/$Yesterday" . "_" . $Granularity . "_" . "$Stream" . ".sbstats";
	  if(file_exists($Temp))
		{
		$YesterdayResults[$Stream][$Granularity] = $Temp;
		}

          $Temp = "$FileDir/$TodayYear/$Stream/$Granularity/$Today" . "_" . $Granularity . "_" . "$Stream" . ".sbstats";
          if(file_exists($Temp))
                {
		$TodayResults[$Stream][$Granularity] = $Temp;
		$TodayResults[$Stream]['Type'] = $ThisStreamData[1];
		}

          $Temp = "$FileDir/$TomorrowYear/$Stream/$Granularity/$Tomorrow" . "_" . $Granularity . "_" . "$Stream" . ".sbstats";
          if(file_exists($Temp))
                $TomorrowResults[$Stream][$Granularity] = $Temp;


	  }
	}

DebugArray("YesterdayResults");
DebugArray("TodayResults");
DebugArray("TomorrowResults");

if($YesterdayResults)
foreach($YesterdayResults as $Display)
  {
  if($Display)$YesterdayPath="graphset.php?config=$Config&ymd=" . $Yesterday;
  }

if($TomorrowResults)
foreach($TomorrowResults as $Display)
  {
  if($Display)$ShowTomorrow="Y";
  if($Display)$TomorrowPath="graphset.php?config=$Config&ymd=" . $Tomorrow;
  }

?>


<table><tr>

<?
        echo "<td><a href=$YesterdayPath> <img src='leftarrow.png'> </td></a>";
        echo "<td><a href=$TomorrowPath> <img src='rightarrow.png'> </td></a>";
?>

	<td><a href=/index.php> [Site Homepage] </a></td>
	<td><a href=index.php> [Stats Homepage] </a></td>
</tr>
</table>

<?

DebugArray("TodayResults");

foreach($TodayResults as $index => $Display)
  { 
  DebugArray("Display");
  $Thisname = $index;
  $ThisGran = key($Display);
  $ThisStream = $Display[05];
  $ThisType = $Display['Type'];
  
//  $BaseName = basename($Display);
  if($Display)
    if(! $_GET['debug'])
        echo "<br><img src=graph.php?stream=$Thisname&ymd=$Today&granularity=05&type=$ThisType>";
    else
        echo "img src=graph.php?stream=$Thisname&ymd=$Today&granularity=05&type=$ThisType";

// http://nad0019linux01/stats.0810/graph.php?stream=icash-bags&ymd=20100809&granularity=05&type=icash
 
  }

?>
