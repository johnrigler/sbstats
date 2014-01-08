<?

//include '../sbstatarrays_lib.php';

session_start();
//session_destroy();
//session_start();

$report = $_GET['report'];

$report = "vmstat";

//echo "<pre>";

//print_r($_SESSION);

//echo "</pre>";


$host = $_SESSION['host'];
//$host = "unixp21";


$dir = opendir("$host");
//    opendir("alliance");

$weeks = array();
$temp = array();

$count = 0;

while(false != ($file = readdir($dir)))
{

if(strstr($file,".sbstats"))
   {
   $temp[$count] = $file;
   $count++;
   }
}

sort($temp);

foreach($temp as $file)
{
   $year = substr($file,0,4);
   $month = substr($file,4,2);
   $day = substr($file,6,2);
   $week = date("W", mktime(0, 0, 0, $month, $day, $year));
   $dayno = date("w", mktime(0, 0, 0, $month, $day, $year));
   if($dayno == 0)$dayno = 7;
   $weeks[$year][$week][$dayno] = $file;
}

ExpandArray($weeks);

//echo "<pre>";

//print_r($weeks);

//echo "</pre>";
ShowWeeks($report,$host,$weeks);
?>
