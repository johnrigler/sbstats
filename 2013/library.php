<?

//include 'jExpandlib.php';

function DebugArray( $Name )
  {
  global $_GET;
  global ${$Name};
  $Contents = "${$Name}";
   if($Contents)
    {
    if (array_key_exists('debug',$_GET))
      {
      echo "<div style='color:green;background-color:white'>
                 <br>[DEBUG ARRAY] $Name ----------------";

      echo "<br>Array: [$Name]";
      foreach($Contents as $index => $value)
             {if(is_array($value))
                 {
	          echo "<br>index = [$index]<br>"; 
 	     print_r($value);
	         DebugArray( "value" ); 
	         }
             echo "<hr>[$index] = $value";
             }
  echo "<br>[END DEBUG ARRAY]-----------------<br><br></div>";
       }
    }
    else 
     if(array_key_exists('debug',$_GET))
      echo "<div style='color:red;background-color:white'><br>[DEBUG ARRAY] No value for $Name<br><br></div>";
  }

function DebugArrayRecursive( $Array , $Name="(unknown)" )
  {
  if(array_key_exists('debug',$_GET))
      {
      echo "<table class=report><tr><td><br>[$Name]";
      foreach($Array as $Index => $Value)
	{
	if(is_array($Value)) 
	  {
	  DebugArrayRecursive( $Value , $Index );
	  }
	  else
	  echo "<br>$Index => $Value";
        }  
      echo "</td></tr></table>\n"; 
      } 
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

function MultiDayArray ( $Files , $Types , $Report ) {

$Month_r = array();
$Week_r = array();
$Count = 0;

foreach ($Files as $File)
  { 
   if (($handle = fopen("$File", "r")) !== FALSE)
         {
	 $Year = substr($File,0,4);
	 $Month = PrettyMonth(substr($File,4,2));
	 $Day = substr($File,6,2);
	 $Week = "W-" . substr($File,8,2);
	 $WeekDay = PrettyDay(substr($File,10,1));
	 $TZ = substr($File,11,3); 
	 list($Discard,$SerialNo,$Desc,$Name,$OS,$Type) = explode(":",$File); 
	 $Temp = explode(".",$Type);
	 $Type = $Temp[0];


   //      $Array[$Year][$Month][$Week][$WeekDay][$Day][$TZ][$SerialNo][$Desc][$Name][$OS][$Type] = "";
	 $Month_r[$Name][$Year][$Month][$Day] = array ( $TZ,$SerialNo,$Desc,$Name,$OS,$Type );
	 $Week_r[$Name][$Year][$Week][$WeekDay] =  array ( $TZ,$SerialNo,$Desc,$Name,$OS,$Type ); 

         while (($data = fgetcsv($handle, 1000, " ")) !== FALSE)
          {

	    list($HH,$MM) = explode(":",$data[0]);
            $Time = $HH . ":" . $MM;

           if($MM == "01")$MM = "00";
           if($MM == "02")$MM = "00";
           if($MM == "03")$MM = "00";
           if($MM == "04")$MM = "00";
           if($MM == "06")$MM = "05";
	   if($MM == "07")$MM = "05";
           if($MM == "08")$MM = "05";
           if($MM == "09")$MM = "05";

           if($MM == "11")$MM = "10";
           if($MM == "12")$MM = "10";
           if($MM == "13")$MM = "10";
           if($MM == "14")$MM = "10";
           if($MM == "16")$MM = "15";
           if($MM == "17")$MM = "15";
           if($MM == "18")$MM = "15";
           if($MM == "19")$MM = "15";

           if($MM == "21")$MM = "20";
           if($MM == "22")$MM = "20";
           if($MM == "23")$MM = "20";
           if($MM == "24")$MM = "20";
           if($MM == "26")$MM = "25";
           if($MM == "27")$MM = "25";
           if($MM == "28")$MM = "25";
           if($MM == "29")$MM = "25";

           if($MM == "31")$MM = "30";
           if($MM == "32")$MM = "30";
           if($MM == "33")$MM = "30";
           if($MM == "34")$MM = "30";
           if($MM == "36")$MM = "35";
           if($MM == "37")$MM = "35";
           if($MM == "38")$MM = "35";
           if($MM == "39")$MM = "35";

           if($MM == "41")$MM = "40";
           if($MM == "42")$MM = "40";
           if($MM == "43")$MM = "40";
           if($MM == "44")$MM = "40";
           if($MM == "46")$MM = "45";
           if($MM == "47")$MM = "45";
           if($MM == "48")$MM = "45";
           if($MM == "49")$MM = "45";

           if($MM == "51")$MM = "50";
           if($MM == "52")$MM = "50";
           if($MM == "53")$MM = "50";
           if($MM == "54")$MM = "50";
           if($MM == "56")$MM = "55";
           if($MM == "57")$MM = "55";
           if($MM == "58")$MM = "55";
           if($MM == "59")$MM = "55";





            $Array[$Year][$Month][$Day][$Week][$WeekDay][$TZ][$SerialNo][$Desc][$Name][$OS][$Type][$Time] = $data[2];
	    $Datum = explode(",",$data[2]);
	    $DataPointArray = array();
            $TypeNames = explode(",",$Types[$OS][$Type][2]);
            $ToInclude = explode(",",$Report[$OS][$Type][2]);
            foreach($Datum as $Index => $DataPoint)
	       {
	       if(in_array($TypeNames[$Index],$ToInclude))
	         $DataPointArray[$TypeNames[$Index]] =  $DataPoint;
	       $Array[$Year][$Month][$Day][$Week][$WeekDay][$TZ][$SerialNo][$Desc][$Name][$OS][$Type][$Time] = $DataPointArray;
	       }

           // print_r($DataPoint); 
         //   print_r($TypeNames);
//	    print_r($ToInclude);
//	    print_r($DataPointArray);
          }
    fclose($handle);
  $Count++;
//  return $Array;
//	print_r($Month_r);
	print_r($Week_r);
return $Week_r;
     }
  }
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

if($Day == 0)$Day = "Sun";
if($Day == 1)$Day = "Mon";
if($Day == 2)$Day = "Tue";
if($Day == 3)$Day = "Wed";
if($Day == 4)$Day = "Thu";
if($Day == 5)$Day = "Fri";
if($Day == 6)$Day = "Sat";

return $Day;

}

function ReversePrettyDay ( $Day ) {

if($Day == "Sun")$Day = 1;
if($Day == "Mon")$Day = 2;
if($Day == "Tue")$Day = 3;
if($Day == "Wed")$Day = 4;
if($Day == "Thu")$Day = 5;
if($Day == "Fri")$Day = 6;
if($Day == "Sat")$Day = 7;

return $Day;

}

function PrettyMonth( $Month ) {

if($Month == 1)$Month = "Jan";
if($Month == 2)$Month = "Feb";
if($Month == 3)$Month = "Mar";
if($Month == 4)$Month = "Apr";
if($Month == 5)$Month = "May";
if($Month == 6)$Month = "Jun";
if($Month == 7)$Month = "Jul";
if($Month == 8)$Month = "Aug";
if($Month == 9)$Month = "Sep";
if($Month == 10)$Month = "Oct";
if($Month == 11)$Month = "Nov";
if($Month == 12)$Month = "Dec";

return $Month;

}

function HHMMtoInt ( $HHMM ) {

$HH = substr($HHMM,0,2) * 12; 
$MM = substr($HHMM,2,2) / 5;

return $HH + $MM;

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
