<?php

class Coordinate {
	public $X;
	public $Y;

function Coordinate($setX,$setY) {
	$this->X = $setX;
	$this->Y = $setY;
	}

function displayCoordinate()  {
	echo "$this->X $this->Y";
	} 

function returnCoordinateArray()  {
	return array($this->X,$this->Y);
        }
}

class RelativeCoordinate {

	public $X = 3;
	public $Y = 2;
	public $Parent;

function RelativeCoordinate($x,$y,$parent) {
	$this->X = $x + $parent[0];
        $this->Y = $y + $parent[1];
	}

function displayCoordinate() {
        echo "$this->X $this->Y";
	}
}

class Image {

    public $img;
    var $white_color;
    var $X = 200;
    var $Y = 200;
    var $white; 

    function Image(){
          $this->img = imagecreatetruecolor($this->X, $this->Y);
	  $this->white = imagecolorallocate($this->img,255,255,255);
    }

    function setY($value){
        $this->Y=$value;
    }
    function setX($value){
        $this->X=$value;
    }

    function setColor($value){
        $this->box_color=$value;
    }

    function addCircle($CenterX,$CenterY,$Radius){

	imagearc($this->img, 100,100,100,100, 0, 360, $this->white);
	}

   function displayBox(){
	header("Content-type: image/png");
	imagepng($this->img);
    }

   function __destructor(){
        imagepng($this->img);
        imagedestroy($this->img);

   }
}

function MyImageCreate(){ 
	$x = 5; 
}

$Base = new Coordinate(300,300);
//$Image = MyImageCreate($Base->returnCoordinatesArray());

$Image = MyImageCreate(){
return imagecreatetruecolor(50,50);
}

$Image = imagecreatetruecolor(50,50);
$white = imagecolorallocate($Image,255,255,255);

//$box=new Image();

//$box->setX(300);
//$box->setY(300); 
//$box->addCircle(100,100,80);
//$box->displayBox();

//$Base = new Coordinate(5,10);

//$Base->displayCoordinate();

//print_r($Base->returnCoordinateArray());

//$Child = new RelativeCoordinate(2,2,$Base->returnCoordinateArray());
//$Child->displayCoordinate();

header("Content-type: image/png");
imagepng($Image);


?> 
