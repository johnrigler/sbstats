<?php
$image = new Imagick('test.jpg');

$x = 1;
$y = 1;
$pixel = $image->getImagePixelColor($x, $y);

$colors = $pixel->getColor();
print_r($colors); // produces Array([r]=>255,[g]=>255,[b]=>255,[a]=>1);

?>

