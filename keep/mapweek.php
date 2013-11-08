<?php

function Map ( $modulator ) {

$counter = 0;

global $oneweek;
global $GraphLeft;
global $ChartBar;
global $TotalWidth;
global $MyFacts;
global $img;

$counter = 0;



foreach($oneweek as $i => $value) {
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
