<?php

class Coordinates
{ 
public $X1;
public $Y1;
public $X2;
public $Y2; 

function Coordinates($a,$b,$c,$d)
	{
	if(is_array($a))
	$this->CoordinatesArray($a);
	else
	$this->CoordinatesPoints($a,$b,$c,$d);
	}

function CoordinatesPoints($initX1,$initY1,$initX2,$initY2) {
	$this->X1 = $initX1;
	$this->Y1 = $initY1;
	$this->X2 = $initX2;
	$this->Y2 = $initY2;
	}

function CoordinatesArray($thisarray) {
        $this->X1 = $thisarray[0];
        $this->Y1 = $thisarray[1];
        $this->X2 = $thisarray[2];
        $this->Y2 = $thisarray[3];
	}

function descend($thisarray) {
        $this->X1 += $thisarray[0];
        $this->Y1 += $thisarray[1];
        $this->X2 += $thisarray[0];
        $this->Y2 += $thisarray[1];
	} 

function ascend($thisarray,$arraybelow = array(0,0,0,0)) {
        $this->X1 += $thisarray[0];
        $this->Y1 += $thisarray[3] - $this->Y2 + $thisarray[1]; 
        $this->X2 += $thisarray[0];
        $this->Y2 += $thisarray[3] - $this->Y2 + $thisarray[1];
        }


function render() {
	$thisarray = array($this->X1,$this->Y1,$this->X2,$this->Y2);
	return $thisarray;
	}
}

function mymerge($mgsrc,$mgdest,$color) {

global $Destination;
$LocalSource =  imagecreatetruecolor($mgdest[0],$mgdest[1]);
imagerectangle($LocalSource,0,0,$mgdest[0],$mgdest[1],$color);

# mgsrc describes where to put the image on $Destination
# mgdest describes the size of destination image to get


imagecopymerge($Destination, $LocalSource, $mgsrc[0],$mgsrc[1],0,0,$mgdest[0],$mgdest[1],100);
#imagecopymerge($Destination, $LocalSource, 0,0,0,0,$mgdest[0],$mgdest[1],100);
imagedestroy($LocalSource);
}

function myImageRectangle($thisarray,$color)
	{
	global $Destination;
	imagerectangle($Destination,$thisarray[0],$thisarray[1],$thisarray[2],$thisarray[3],$color);
	}

$Destination = imagecreatefrompng('dailybase.png');
$white = imagecolorallocate($Destination,255,255,255);
$red = imagecolorallocate($Destination,255,0,0);


$GridBody = new Coordinates(30,71,1095,271);
$SmallBox = new Coordinates(array(0,0,180,120));
$SmallBox2 = new Coordinates(array(0,0,80,100));

$SmallBox->ascend($GridBody->render());
$SmallBox2->ascend($GridBody->render(),$SmallBox->render());

//$SmallBox = new Coordinates(10,5,$Start->renderArray());
//$BigBox = new Coordinates(50,50);

//imagerectangle($Destination, $GridBody->X1,$GridBody->Y1,$GridBody->X2,$GridBody->Y2, $red);
myImageRectangle($GridBody->render(),$red);
myImageRectangle($SmallBox->render(),$red);
myImageRectangle($SmallBox2->render(),$red);


# This says to put SmallBox at Start position on image.
 
//mymerge($Start->renderArray(),$SmallBox->renderArray(),$red);
//mymerge($Start->renderArray(),$BigBox->renderArray(),$red);


header('Content-Type: image/png');
imagepng($Destination);

imagedestroy($Destination);
?>
