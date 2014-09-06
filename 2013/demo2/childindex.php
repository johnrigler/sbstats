<?

$dirname = ".";
$dir = opendir($dirname);

$dirFiles = array();

while(false != ($file = readdir($dir)))
{
$stuff = explode(".",$file);
if($stuff[1] == "sbstats")
	$dirFiles[] = $file;

}

sort ($dirFiles);

foreach($dirFiles as $file)
	{
	list( $date,$serial,$machinedesc,$host,$type,$report) = explode(":",$file);
	$temp = explode(".",$report);
	$report = $temp[0];
	//echo "$date,$serial,$machinedesc,$host,$type,$report";	
	echo "<img src=graphday.php?file=$file&report=aix1>";
	}

?>
