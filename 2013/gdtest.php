<?php
// Create a blank image and add some text
$im = imagecreatetruecolor(120, 20);
$text_color = imagecolorallocate($im, 255, 127, 38);


imagestring($im, 1, 5, 5,  "A Simple Text String", $text_color);

// Output the image

header("Content-type: image/png");
imagepng($im);

// Free up memory
imagedestroy($im);
?>
