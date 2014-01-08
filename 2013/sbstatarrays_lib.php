<?

include 'timedate_lib.php';

function LoadMultiDayArray ( $Name,$Year,$LimitDay = -1 ) {

if (substr($Limit,0,1) == 'W')$Unit="Week";
if (substr($Limit,0,1) == 'M')$Unit="Month";

$Files = array();
$Array = array();
$Count = 0;

$Directory = "$Name";
$WriteDirectory = "$Customer][$Year][$Name";

$dir = opendir("$Directory");

while(false != ($file = readdir($dir)))
{
$stuff = explode(".",$file);
if($stuff[1] == "sbstats")
        $Files[] = $file;
}

closedir($dir);

sort($Files);

foreach ($Files as $File)
  {
         $MonthNo = substr($File,4,2);
	 $Month = PrettyMonth($MonthNo);
         $Day = substr($File,6,2);

         $Week = "W-" . substr($File,8,2);
	 $SafeWeek = "W" . substr($File,8,2);
         $WeekDay = PrettyDay(substr($File,10,1));


         if($Unit == "Month")
	    if($MonthNo == substr($Limit,3))
	      if($LimitDay == $Day)
                $Array[$WriteDirectory][$Limit][$Month][$Day] = "$File";
	      else
		if($LimitDay == -1)
                 $Array[$WriteDirectory][$Limit][$Month][$Day] = "$File";
 


         if($Unit == "Week")
	    if($Week == $Limit)
		$Array[$WriteDirectory][$SafeWeek][$SafeWeek][$WeekDay] = "$File";
  }

return $Array;

}

function ExpandArray($Array)
{

$Lambda = rand();
$ThisArray = array();
$FinalArray = array();
static $blob;

$blob = LocalExpandArray($Lambda,$Array);


$ThisArray = explode("\n",$blob);

foreach($ThisArray as $Line)
	{
	  $pos = strpos($Line,":");
	  $ChildLambda = substr($Line,0,$pos);
	  $Rest = substr($Line,$pos + 1);
	  if($Lambda == $ChildLambda)
		$FinalArray[] .= $Rest;
	}

return $FinalArray;

}


function LocalExpandArray($Lambda,$Array,$Level = 0)
{

/*

This Transforms an Array of Arrays from a Hierarchical state to a flat state which could be 
evaluated or included if it is a file form.  For example, this:

Array
(
    [World] => Array
        (
            [USA] => Array
                (
                    [Texas] => Array
                        (
                            [Dallas] => 1212 Honeycut Lane
                            [Austin] => 753 Main Street
                        )
                )
        )
)

would be transformed to An Array Stream containing two elements:

[World][USA][Texas][Dallas] = '1212 Honeycut Lane';
[World][USA][Texas][Dallas] = '753 Main Street';

if you called:

$ArrayName = ExpandArray($OriginalArray);

*/

static $ReturnArray = array();

static $zero,$one,$two,$three;
static $blob; 

foreach($Array as $Key => $Value)
	{  
	if($Level == 0)$zero = "$Key";
	if($Level == 1)$one = "$Key";
	if($Level == 2)$two = "$Key";
	if($Level == 3)$three = "$Key";

	$tree = ""; 
        if($zero)$tree = $tree . "[$zero]";
	if($one)$tree = $tree . "[$one]";
	if($two)$tree = $tree . "[$two]";
	if($three)$tree = $tree . "[$three]";

        if(!is_array($Value))
        	$blob = $blob . sprintf("$Lambda:$tree = '$Value';\n"); 

	if(is_array($Value))
	   $ReturnArray .= LocalExpandArray($Lambda,$Value,$Level + 1); 
	}

return $blob;
}

function ShowWeeks($report,$host,$weeks)
{
  foreach($weeks as $year => $week)
    foreach($week as $weekno => $Element)
      {
      $index = $year . "-" . $weekno;
      $_SESSION[$host][$index] = $Element;
      echo  "<br><a href=dashboardwrapper.php?host=$host&report=$report&match=$index&debug=yes >$index</a>\n";
      }

  };


function ArrayToFile( $Array, $ArrayName, $Filename) {

print_r($Array);

$fp = fopen("test.php",'w');

fwrite($fp,'<?' . "\n");
foreach ($Array as $Line)
        {
	fwrite($fp,"$ArrayName$Line");
        }

fwrite($fp,'?>' . "\n");

fclose($fp);

}
?>
