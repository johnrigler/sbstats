<?

function DebugArray( $Name )
  {
  
  global $_GET;
  global ${$Name};
  $Contents = "${$Name}";
   if($Contents)
    {
    if (array_key_exists('debug',$_GET))
      {
      echo "<div style='color:green;background-color:white'><br>[DEBUG ARRAY] $Name ----------------";

      echo "<br>Array: [$Name]";
      foreach(${$Name} as $index => $value)
             {if(is_array($value))
	     echo "<br>index = [$index]<br>"; 
 	     print_r($value);
             echo "<hr>[$index] = $value";
             }
  echo "<br>[END DEBUG ARRAY]-----------------<br><br></div>";
       }
    }
    else 
     if(array_key_exists('debug',$_GET))
      echo "<div style='color:red;background-color:white'><br>[DEBUG ARRAY] No value for $Name<br><br></div>";
  }

function CatArray( $Array , $Divider )
  {
  $FinalString = "";
  foreach($Array as $Value)
    {
    $FinalString = $FinalString . $Divider . $Value;
    }
  return substr($FinalString,1);
  }


function CalculateYesterday( $YMD , $Daysago = 1 )
{
$Year = substr($YMD,0,4);
$Month = substr($YMD,4,2);
$Day = substr($YMD,6,2);
$today = mktime(0,0,0,$Month,$Day,$Year);
return date("Ymd", $today - 86400 * $Daysago);
}

function CalculateLastWeek( $ISOWeek , $Weeksago = 1 )
{
$Week = substr($ISOWeek,6,2);
$Year = substr($ISOWeek,0,4);

if ($Week > 1)
  {
  $LastWeek = $Week - $Weeksago ;
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

?>
