Start
<?

class Point 
{
public $X;
public $Y;

function Point( $valX,$valY ) {
	$this->X = $valX;
	$this->Y = $valY;
	}

function ret() {
	return array($this->X,$this->Y);
	}
}

class UL extends Point {}
class LR extends Point {} 
class Rectangle extends UL extends LR {}

$ThisUL = new UL(5,10);
$ThisLR = new LR(20,30); 

print_r($ThisUL->ret());
print_r($ThisLR->ret());


?>
