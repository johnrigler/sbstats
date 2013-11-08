
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
	echo "<hr><img src=graph.php?file=$file&report=aix1> $file</img>";
	echo "<hr><a href=graph.php?file=$file&report=aix1> $file</a>";
	}



?>
