<?php
require_once( "glib.php" );

$ge = new GraphicsEnvironment( 400, 400 );

$ge->addColor( "black", 0, 0, 0 );
$ge->addColor( "red", 255, 0, 0 );
$ge->addColor( "green", 0, 255, 0 );
$ge->addColor( "blue", 0, 0, 255 );

$gobjs = array();
$gobjs []= new Line( "black", 10, 5, 100, 200 );
$gobjs []= new Line( "blue", 200, 150, 390, 380 );
$gobjs []= new Line( "red", 60, 40, 10, 300 );
$gobjs []= new Line( "green", 5, 390, 390, 10 );

foreach( $gobjs as $gobj ) { $gobj->render( $ge ); }

$ge->saveAsPng( "test.png" );

$Destination = imagecreatefrompng('test.png');

header('Content-Type: image/png');
imagepng($Destination);

?>
