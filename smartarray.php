<?

class OffsetData {

public $name;
public $value;
public $type;
public $offset;
public $number;

/**/

function OffsetData ( $data , $offset = 0 ) {

	$this->name = $data[0];
	$this->value = $data[1];
	$this->number = $data[2];
	$this->type = $data[3];
	$this->offset = $offset;
	}

/**/

function render () {
	
	$array['name'] = $this->name;
	$array['start'] = $this->offset - 1;
	$array['stop'] = $this->offset - $this->value;
	$array['type'] = $this->type;
	$array['number'] = $this->number;
	return $array;
	}

function offset () {

	return $this->offset - $this->value;
	}
/**/
}

class GridArray {

public $X1;
public $Y1;
public $X2;
public $Y2;
public $X3;
public $Y3;
public $color;
public $name;
public $height;
public $width;

function GridArray() {
	global $Pen;
	$newarray = array();
	foreach(func_get_args() as $args)
	if(is_array($args))
		{
		$newarray []= $args[0];
                $newarray []= $args[1];
		}
		else
		{
		$newarray []= $args;
		}

	$this->X1 = $newarray[0];
        $this->Y1 = $newarray[1];
        $this->X2 = $newarray[2];
        $this->Y2 = $newarray[3];
        $this->X3 = $newarray[4];
        $this->Y3 = $newarray[5];
	$this->height = $this->Y2 - $this->Y1;
        $this->width = $this->X2 - $this->X1;
	$this->color = $Pen;

	}

function coordinates() {
	$RealX1 = $this->X1+$this->X3;
        $RealY1 = $this->Y1+$this->Y3;
        $RealX2 = $this->X2+$this->X3;
        $RealY2 = $this->Y2+$this->Y3;


	$string =  "( $RealX1 $RealY1 $RealX2 $RealY2)";
	return $string;
	}


function point() {
        $torender = array($this->X1,$this->Y1);
        return $torender;
        }

function rectangle() {
        $torender = array($this->X1,$this->Y1,$this->X2,$this->Y2);
        return $torender;
        }

function offsetrectangle() {
	$torender = array($this->X1,$this->Y1,$this->X2,$this->Y2,$this->X3,$this->Y3);
	return $torender;
	}
}



class GridPoint extends GridArray {

	function render() {
        $RealX1 = $this->X1+$this->X3;
        $RealY1 = $this->Y1+$this->Y3;

        $torender = array($RealX1,$RealY1);
        return $torender;
	}

}


class GridRectangle extends GridArray {

        function render() {
        $RealX1 = $this->X1+$this->X3;
        $RealY1 = $this->Y1+$this->Y3;
        $RealX2 = $this->X2+$this->X3;
        $RealY2 = $this->Y2+$this->Y3;
        $torender =  array( $RealX1,$RealY1,$RealX2,$RealY2);

        return $torender;
        }

}

class GridFilledRectangle extends GridArray {

        function render() {
        $RealX1 = $this->X1+$this->X3;
        $RealY1 = $this->Y1+$this->Y3;
        $RealX2 = $this->X2+$this->X3;
        $RealY2 = $this->Y2+$this->Y3;
        $torender =  array( $RealX1,$RealY1,$RealX2,$RealY2);

        return $torender;
        }

}



function myImagePoint($array,$thiscolor)
        {
        global $color;
        global $Destination;
	$X = $array[0];
	$Y = $array[1];
        imageline($Destination,$X - 5,$Y,$X + 5,$Y,$color[$thiscolor]);
        imageline($Destination,$X,$Y - 5,$X,$Y + 5,$color[$thiscolor]);
        }

function myImageRectangle($thisarray,$thiscolor,$filled=0)
        {
        global $color;
        global $Destination;
        $TLX = $thisarray[0];
        $TLY = $thisarray[1];
        $BRX = $thisarray[2];
        $BRY = $thisarray[3];
	if ($filled == 0) imagerectangle($Destination,$TLX,$TLY,$BRX,$BRY,$color[$thiscolor]);
        if ($filled == 1) imagefilledrectangle($Destination,$TLX,$TLY,$BRX,$BRY,$color[$thiscolor]);

        }

function graphLines() {

        $Skip = $InnerFrame->width / $AllIterations;
        $Width = $Skip - $Margin;
        $Frame = $InnerFrame->render();
        $Height = $InnerFrame->height;

        $Iteration[$x] = 
		new GridFilledRectangle
		(array($Skip*$x+$Margin,$Height - $toshow[$x]),
		 array($Skip*$x+$Width,$Height),
		 $InnerFrame->point());

//        $Iteration[$x]->name = "Iteration[$x]";
}


function renderFinalImage( $passedT ) {

global $Destination;
global $color;

if ( is_array($passedT[0]) ) { $T = $passedT; } else { $T = array( $passedT ); }

foreach ($T as $Imageset)
        {

        foreach( $Imageset as $Element )
                {
 //		$Line += 10;
 		$ElementType = get_class($Element);
 //		$ElementArray = $Element->offsetrectangle();
 //		$Coordinates = $Element->coordinates();
 //		ImageString( $Destination , 3 , 10 , $Line , "$ElementType $Element->name $Coordinates" , $color[$Element->color] );

		if($ElementType == 'GridPoint') myImagePoint($Element->render(),$Element->color);
                if($ElementType == 'GridRectangle') myImageRectangle($Element->render(),$Element->color);
                if($ElementType == 'GridFilledRectangle') myImageRectangle($Element->render(),$Element->color,$filled=1);

                } 
        }
header('Content-Type: image/png');
imagepng($Destination);

}

function showArray( $passedT ) {

global $Destination;
global $color;

if ( is_array($passedT[0]) ) { $T = $passedT; } else { $T = array( $passedT ); }

foreach ($T as $Imageset)
        {

        foreach( $Imageset as $Element )
              {
              $Line += 10;
              $ElementType = get_class($Element);
              $ElementArray = $Element->offsetrectangle();
              $Coordinates = $Element->coordinates();
              ImageString( $Destination , 3 , 10 , $Line , "$ElementType $Element->name $Coordinates" , $color[$Element->color] );
              }
        }
}

function showThisArray( $passed,$X,$Y ) {

global $Destination;
global $color;

	$Line = $Y;

        foreach( $passed as $index => $value )
              {
              $Line += 10;
	      imagefilledrectangle( $Destination, $X, $Line + 1, $X + 120, $Line + 10, $color['white'] );
              ImageString( $Destination , 3 , $X, $Line , "$index => $value" , $color['red'] );
              }

}

function showKey( $passed,$X,$Y ) {

global $Destination;
global $color;

        $Line = $Y;


        imagefilledrectangle( $Destination, $X, $Line, $X + 15, $Line + 15, $color[$passed[1]] );
        imagerectangle( $Destination, $X, $Line, $X + 15, $Line + 15, $color['black'] );
        ImageString( $Destination , 3 , $X + 28 , $Line , "$passed[0]" , $color['white'] );

}


function loadSegment( $Position,$ToLoad )
{
global $Pen;
global $Final;
global $InnerFrame;
global $Width;
global $Skip;
global $Margin;

        foreach ($ToLoad as $index => $load)
        {
        $Pen = $index;

        if ($index == 0)
                $MyData []= new OffsetData($load,$InnerFrame->Y2);
                        else
                $MyData []= new OffsetData($load,$MyData[$index-1]->offset());

        $Element = $MyData[$index]->render();
        $Final []= new GridFilledRectangle($InnerFrame->X1 + $Margin + ($Position * ($Width + $Margin)),
	$Element[start],$InnerFrame->X1 + $Width + $Margin + ($Position * ($Width +$Margin)),$Element[stop]);
        }

}

function reduceReport( $template ) {

foreach ($template as $item)
	{
	list($index,$value) = explode(";",$item);
	$targetarray[$index] = $value;
	}

return $targetarray;
}

$Destination = imagecreatefrompng('dailybase.png');

include 'colors2.php';

$color = $ChartBar;

$color['green'] = imagecolorallocate($Destination,0,255,0);
$color['blue'] = imagecolorallocate($Destination,0,0,255);
$color['red'] = imagecolorallocate($Destination,255,0,0);
$color['black'] = imagecolorallocate($Destination,0,0,0);

$Pen = "blue";
$Final []= $InnerFrame    = new GridRectangle(array(29,71),array(1096,271));
$InnerFrame->name = "InnerFrame";

$AllIterations = 96;
$Margin = 2;
$Skip = $InnerFrame->width / $AllIterations;
$Width = $Skip - $Margin;
$Frame = $InnerFrame->render();
$Height = $InnerFrame->height;
$UnitHeight = 2;

include 'site-config.php';

global $GraphConfiguration;

include 'oneday.php';
$FileName = "/buxs/sbstats2/2010/nad0019aixp21/15/20101027_15_nad0019aixp21_aixbase.sbstats";
$YMD = "20101027";

if (($handle = fopen("$FileName", "r")) !== FALSE)
    {
    $header = fgetcsv($handle, 1000, " ");
    while (($data = fgetcsv($handle, 1000, " ")) !== FALSE)
        {
        $key = $data[0];
        if($YMD){if(isset($data[2]))$oneday[$key] = $data[2];}
        if(is_numeric(substr($key,2,1)))
                if($Week)
                        if(isset($data[2]))
                                $oneweek[$key] = $data[3];
        }
    fclose($handle);
    }

$count = 0;


//$GraphConfiguration['aix1']['facts'] = array( "16;I/O Wait" , "14;System" , "13;User" , "6;Swap Out" , "5;Swap In" );

$showarray = array(array("wait",0),array("usr",1),array("sys",2));

$X = 270;
$Y = -12;
$keycount = 0;
foreach ($showarray as $thisarray)
{
$mod = $keycount % 5;
if($mod == 0)
	{
	$X = 270;
	$Y = $Y + 20;
	}

showKey($thisarray,$X,$Y);
$X = $X + 151;
$keycount = $keycount + 1;

}

$thisseg = $oneday[1100];
$thisseg2 = $oneday[1115];
$thisseg3 = $oneday[1130];

$DataType = array_shift($header);
array_shift($header);

if($GraphConfiguration['aix1']['facts'])
	$header = reduceReport($GraphConfiguration['aix1']['facts']);

showThisArray($GraphConfiguration['aix1']['facts'],750,20);
showThisArray($header,900,20);
ImageString( $Destination , 3 , 100 , 40 , "$thisseg" , $color['black'] );

loadSegment(0,array(array("wait",$thisseg[2] * $UnitHeight,0,"#"),array("usr",$thisseg[3] * $UnitHeight,1,"#"),array("sys",$thisseg[4] * $UnitHeight,2,"#"),array("other",$thisseg[5],3,"#")));
loadSegment(1,array(array("wait",$thisseg2[2],0,"#"),array("usr",$thisseg2[3],1,"#"),array("sys",$thisseg2[4],2,"#"),array("other",$thisseg2[3],3,"#")));
loadSegment(2,array(array("wait",$thisseg3[2],0,"#"),array("usr",$thisseg3[3],1,"#"),array("sys",$thisseg3[4],2,"#"),array("other",42,3,"#")));
loadSegment(3,array(array("wait",$thisseg[0],0,"#"),array("usr",$thisseg[1],1,"#"),array("sys",$thisseg[2],2,"#"),array("other",42,3,"#")));
loadSegment(4,array(array("wait",$thisseg[0],0,"#"),array("usr",$thisseg[1],1,"#"),array("sys",$thisseg[2],2,"#"),array("other",42,3,"#")));
loadSegment(6,array(array("wait",100 * $UnitHeight,0,"#"),array("usr",20,1,"#"),array("sys",50,2,"#"),array("other",4,3,"#")));
loadSegment(7,array(array("wait",10,0,"#"),array("usr",30,1,"#"),array("sys",30,2,"#"),array("other",42,3,"#")));
loadSegment(8,array(array("wait",10,0,"#"),array("usr",20,1,"#"),array("sys",30,2,"#"),array("other",42,3,"#")));
loadSegment(9,array(array("wait",40,0,"#"),array("usr",20,1,"#"),array("sys",30,2,"#"),array("other",14,3,"#")));
loadSegment(10,array(array("wait",15,0,"#"),array("usr",20,1,"#"),array("sys",50,2,"#"),array("other",4,3,"#")));
loadSegment(11,array(array("wait",10,0,"#"),array("usr",30,1,"#"),array("sys",30,2,"#"),array("other",42,3,"#")));
loadSegment(94,array(array("wait",40,0,"#"),array("usr",20,1,"#"),array("sys",30,2,"#"),array("other",14,3,"#")));
loadSegment(95,array(array("wait",15,0,"#"),array("usr",20,1,"#"),array("sys",50,2,"#"),array("other",4,3,"#")));


//showArray( $Final );
renderFinalImage( $Final );

?>
