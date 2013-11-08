<?php

function LeftArrow ( $img , $color, $x , $y ,$size )
{
// set up array of points for polygon
$values = array(
            $x,  $y + $size,  // Point 1 (x, y)
            $x + $size * 2, $y,  // Point 2 (x, y)
            $x + $size * 2, $y + $size * 2   // Point 3 (x, y)
            );

// draw a polygon
imagefilledpolygon($img, $values, 3, $color);

}

function RightArrow ( $img , $color, $x , $y ,$size )
{
$x = $x + $size * 2;
// set up array of points for polygon
$values = array(
            $x,  $y + $size,  // Point 1 (x, y)
            $x - $size * 2, $y,  // Point 2 (x, y)
            $x - $size * 2, $y + $size * 2   // Point 3 (x, y)
            );


// draw a polygon
imagefilledpolygon($img, $values, 3, $color);

}

// create image
$img = imagecreatetruecolor(30, 30);

include 'colors.php';

// allocate colors
$bg   = imagecolorallocate($img, 0, 0, 0);
$blue = imagecolorallocate($img, 0, 0, 255);

// fill the background
imagefilledrectangle($img, 0, 0, 249, 249, $BorderColor);

LeftArrow($img,$blue,5,5,10);

//RightArrow($img,$blue,35,5,10);

// flush image
header('Content-type: image/png');
imagepng($img);
imagedestroy($img);
?> 
