<?php

$GraphDimensions['05']['TotalWidth'] = 3.7;
$GraphDimensions['05']['OuterPadding'] = 1;
$GraphDimensions['05']['GraphLeft'] = 30;
$GraphDimensions['05']['UnitsPerHour'] = 12;
$GraphDimensions['05']['MapArgument'] = 1;

$GraphDimensions['15']['TotalWidth'] = 11;
$GraphDimensions['15']['OuterPadding'] = 1;
$GraphDimensions['15']['GraphLeft'] = 40;
$GraphDimensions['15']['UnitsPerHour'] = 4;
$GraphDimensions['15']['MapArgument'] = 3;

$GraphDimensions['60']['TotalWidth'] = 44.2;
$GraphDimensions['60']['OuterPadding'] = 3;
$GraphDimensions['60']['GraphLeft'] = 40;
$GraphDimensions['60']['UnitsPerHour'] = 1;
$GraphDimensions['60']['MapArgument'] = 12;

function Map ( $modulator ) {

$counter = 0;

global $oneday;
global $GraphLeft;
global $ChartBar;
global $TotalWidth;
global $MyFacts;
global $img;

$counter = 0;



foreach($oneday as $i => $value) {
//	echo "$i<br>";
        $x = explode(",",$value);
        $mod = $counter % $modulator;
        if($mod == 0)
        {
	foreach($MyFacts as $i => $Fact)
	   {
//	    $Data = explode(";",$Fact);
//	    $Position = $Data[0];
	    $NickName = $Data[1];

	    if($x[0] != "")
		$Val[$i] = $x[$i];
		else 
		$Val[$i] = 0;
     	}
	
	$last = 0;
        foreach($MyFacts as $i => $Seg)
	 {
//	  echo "$i $Seg<br>";
           $Stripped = ereg_replace("[^A-Za-z0-9]", "", $Val[$i]);
	   if($Val[$i])
	   {
	 //  Segment( $GraphLeft + ( $TotalWidth * ($counter/$modulator)), $last , $Stripped  , $ReverseColor[$i]);
	   Segment( $GraphLeft + ( $TotalWidth * ($counter/$modulator)), $last , $Stripped  , $ChartBar[$i]);
	   $last = $last + $Stripped;
	   }
         }

        }
        $counter++;
}
}

?>
