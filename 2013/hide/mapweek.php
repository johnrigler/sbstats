<?php

function Map ( $modulator ) {

$counter = 0;

global $week;
global $GraphLeft;
global $ChartBar;
global $TotalWidth;
global $MyFacts;
global $img;

$Color1 = imagecolorallocate($img, 100,100,80);

$counter = 0;

DebugArray("week");



foreach($week as $day => $value) {

//	echo "<hr>$day<br>";
	$hour = array();
	$finalhour = array();

	for ($x = 0; $x < 60; $x = $x+5)
		{
		$valx = sprintf('%02d', $x);
		$time = '00' . $valx;
		$temp = $value[$time];
		$datum = explode(",",$temp);

		foreach($datum as $key => $data)
			{
			$hour[$key] += $data;
			}

	foreach($hour as $key => $data)
		{
		if(($key == 14) | ($key == 15) | ($key == 16))
		$finalhour[$key] = $data / 12;
		}

		print_r($finalhour[15]);lhour = array();

		echo "<hr>";
		$DataWidth = 4;
		imageline($img,$counter * $DataWidth ,$finalhour[15],$counter * $DataWidth,0,$Color1);
		$counter++;
		}
	echo "<hr>";
//	print_r($finalhour);
	}
	Segment( 10,20,30,$TextColor );
	imageline($img,10,20,30,40,$Color1);


/*
           $Stripped = ereg_replace("[^A-Za-z0-9]", "", $Val[$i]);
	   if($Val[$i])
	   {
	  Segment( 10,20,30, $ChartBar[0]);
	 //  Segment( $GraphLeft + ( $TotalWidth * ($counter/$modulator)), $last , $Stripped  , $ReverseColor[$i]);
/	   Segment( $GraphLeft + ( $TotalWidth * ($counter/$modulator)), $last , $Stripped  , $ChartBar[$i]);
//	   $last = $last + $Stripped;
//	   }
  //      $counter++;*/
}

?>
