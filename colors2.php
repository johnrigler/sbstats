<?

global $Destination;

$GraphBackgroundColor = imagecolorallocate($Destination, 230,230,230);
$TextColor = imagecolorallocate($Destination, 100,100,80);


$ChartOne = imagecolorallocate($Destination, 0, 66, 132);
$ChartTwo = imagecolorallocate($Destination, 255, 66, 8);
$ChartThree = imagecolorallocate($Destination, 255, 214, 33);
$ChartFour = imagecolorallocate($Destination, 82, 156, 24);
$ChartFive = imagecolorallocate($Destination, 123,0,33);
$ChartSix = imagecolorallocate($Destination, 132, 206, 255);
$ChartSeven = imagecolorallocate($Destination, 49, 66, 0);
$ChartEight = imagecolorallocate($Destination, 173, 206, 0);
$ChartNine = imagecolorallocate($Destination, 74,24,107);
$ChartTen = imagecolorallocate($Destination, 255, 148, 8);
$ChartEleven = imagecolorallocate($Destination, 198, 0, 8);
$ChartTwelve = imagecolorallocate($Destination, 0, 132, 214);

$ChartBar[0] = imagecolorallocate($Destination, 0, 66, 132);
$ChartBar[1] = imagecolorallocate($Destination, 255, 66, 8);
$ChartBar[2] = imagecolorallocate($Destination, 82, 156, 24);
$ChartBar[3] = imagecolorallocate($Destination, 123,0,33);
$ChartBar[4] = imagecolorallocate($Destination, 49, 66, 0);
$ChartBar[5] = imagecolorallocate($Destination, 173, 206, 0);
$ChartBar[6] = imagecolorallocate($Destination, 74,24,107);
$ChartBar[7] = imagecolorallocate($Destination, 255, 148, 8);
$ChartBar[8] = imagecolorallocate($Destination, 198, 0, 8);
$ChartBar[9] = imagecolorallocate($Destination, 0, 132, 214);
$ChartBar[10] = imagecolorallocate($Destination, 255, 214, 33);
$ChartBar[11] = imagecolorallocate($Destination, 132, 206, 255);
$ChartBar[12] = imagecolorallocate($Destination, 82, 100, 24);
$ChartBar[13] = imagecolorallocate($Destination, 183,0,73);
$ChartBar[14] = imagecolorallocate($Destination, 0,0,200);



$Gray = imagecolorallocate($Destination, 155, 155, 155);
$Black = imagecolorallocate($Destination, 0,0,0);

$GridColor = $Gray;
$TextColor = $Black;

$color_one =  $ChartNine;
$color_two = $ChartEight;
$color_three = $ChartOne;

$BorderColor = $ChartSix;


?>
