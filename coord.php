<?php

class Point 
{ 
public $X;
public $Y;
public $color;

function Point($a,$b,$c = 0) {
	global $Pen;

	if (is_array($c))
	$this->RelativeValuesArray($a,$b,$c,$Pen);
	else
		{
		if(is_array($a))
		$this->PointArray($a,$Pen);
		else
		$this->PointValues($a,$b,$Pen);
		}
	}

function PointValues($initX,$initY,$thiscolor) {
	$this->X = $initX;
	$this->Y = $initY;
	$this->color = $thiscolor;
	}

function PointArray($thisarray,$thiscolor) {
        $this->X = $thisarray[0];
        $this->Y = $thisarray[1];
        $this->color = $thiscolor;
	}

function RelativeValuesArray($initX,$initY,$thisarray,$thiscolor) {
        $this->X = $initX + $thisarray[0];
        $this->Y = $initY + $thisarray[1];
        $this->color = $thiscolor;
        }

function render() {
	$thisarray = array($this->X,$this->Y);
	return $thisarray;
	}
}

class Rectangle {
	public $TopLeft;
	public $BottomRight;

function Rectangle($TL,$BR) {
	global $Pen;

               if(is_array($a[0]))
                $this->RectangleArray($a,"red");
               else
		$this->RectangleValues($TL,$BR,$Pen);
	}

function RectangleValues($TL,$BR,$thiscolor) {
        $this->TopLeft = $TL;
        $this->BottomRight = $BR;
        $this->color = $thiscolor;
        }

function RectangleArray($thisarray,$thiscolor) {
        $this->TopLeft = $thisarray[0];
        $this->BottomRight = $thisarray[1];
        $this->color = $thiscolor;
        }


function render() {
        $thisarray = array($this->TopLeft,$this->BottomRight);
        return $thisarray;
        }

function renderTL() {
	return $this->TopLeft;	
	}
}

function myImagePoint($thisarray,$thiscolor)
	{
	global $color;
	global $Destination;
	imageline($Destination,$thisarray[0] - 5,$thisarray[1],$thisarray[0] + 5,$thisarray[1],$color[$thiscolor]);
        imageline($Destination,$thisarray[0],$thisarray[1] - 5,$thisarray[0],$thisarray[1] + 5,$color[$thiscolor]);
	}

function myImageRectangle($thisarray,$thiscolor)
        {
        global $color;
        global $Destination;
	$TL = $thisarray[0];
	$BR = $thisarray[1];
	imagerectangle($Destination,$TL[0],$TL[1],$BR[0],$BR[1],$color[$thiscolor]);
        }

function renderFinalImage( $passedT ) {

global $Destination;

if ( is_array($passedT[0]) )
	{  
	$T = $passedT;		
	}
	else
	{
	$T = array( $passedT );
	}

foreach ($T as $Imageset)
	{
   	foreach( $Imageset as $Element )
       		{
       		$ElementType = get_class($Element);
       		if($ElementType == 'Point')
       		myImagePoint($Element->render(),$Element->color);
       		if($ElementType == 'Rectangle')
       		myImageRectangle($Element->render(),$Element->color);
		}
       	}

header('Content-Type: image/png');
imagepng($Destination);

}

$Destination = imagecreatefrompng('dailybase.png');
$color['green'] = imagecolorallocate($Destination,0,255,0);
$color['blue'] = imagecolorallocate($Destination,0,0,255);
$color['red'] = imagecolorallocate($Destination,255,0,0);

$Pen = "red";
$Align []= $MyPoint       = new Point(100,200);

$Pen = "blue";
$Final []= $InnerFrame    = new Rectangle(array(29,71),array(1096,271));
$Final []= $Random 	  = new Rectangle(array(array(60,70),array(100,200)));
$Final []= $Random2       = new Rectangle(array(160,170),array(100,200));
$Final []= $MyPointOffset = new Point(10,20,$InnerFrame->renderTL());

//ImageString( $Destination , 3 , 10 , 10 , get_class($Final[1]) , $color['red'] );

renderFinalImage( array($Align,$Final) );
renderFinalImage( $Final );

?>
