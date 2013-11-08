<?

global $img;

$GraphBackgroundColor = imagecolorallocate($img, 230,230,230);
$TextColor = imagecolorallocate($img, 100,100,80);


$ChartOne = imagecolorallocate($img, 0, 66, 132);
$ChartTwo = imagecolorallocate($img, 255, 66, 8);
$ChartThree = imagecolorallocate($img, 255, 214, 33);
$ChartFour = imagecolorallocate($img, 82, 156, 24);
$ChartFive = imagecolorallocate($img, 123,0,33);
$ChartSix = imagecolorallocate($img, 132, 206, 255);
$ChartSeven = imagecolorallocate($img, 49, 66, 0);
$ChartEight = imagecolorallocate($img, 173, 206, 0);
$ChartNine = imagecolorallocate($img, 74,24,107);
$ChartTen = imagecolorallocate($img, 255, 148, 8);
$ChartEleven = imagecolorallocate($img, 198, 0, 8);
$ChartTwelve = imagecolorallocate($img, 0, 132, 214);

$ChartBar[0] = imagecolorallocate($img, 100, 66, 132);
$ChartBar[1] = imagecolorallocate($img, 255, 66, 8);
$ChartBar[2] = imagecolorallocate($img, 0, 66, 132);
$ChartBar[3] = imagecolorallocate($img, 123,0,33);
$ChartBar[4] = imagecolorallocate($img, 49, 66, 0);
$ChartBar[5] = imagecolorallocate($img, 173, 206, 0);
$ChartBar[6] = imagecolorallocate($img, 74,24,107);
$ChartBar[7] = imagecolorallocate($img, 255, 148, 8);
$ChartBar[8] = imagecolorallocate($img, 198, 0, 8);
$ChartBar[9] = imagecolorallocate($img, 0, 132, 214);
$ChartBar[10] = imagecolorallocate($img, 255, 214, 33);
$ChartBar[11] = imagecolorallocate($img, 132, 206, 255);
$ChartBar[12] = imagecolorallocate($img, 82, 100, 24);
$ChartBar[13] = imagecolorallocate($img, 183,0,73);
$ChartBar[14] = imagecolorallocate($img, 0,0,200);



$Gray = imagecolorallocate($img, 155, 155, 155);
$Black = imagecolorallocate($img, 0,0,0);

$GridColor = $Gray;
$TextColor = $Black;

$color_one =  $ChartNine;
$color_two = $ChartEight;
$color_three = $ChartOne;

$BorderColor = $ChartSix;


?>
