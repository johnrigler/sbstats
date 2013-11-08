<?

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
