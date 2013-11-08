
<?

$thispage=basename($_SERVER[PHP_SELF]);


$dirname = "/buxs/sbstats2/2010";
$dir = opendir($dirname);

$foundnext = "false";

$physical = array();

while(false != ($file = readdir($dir)))
{

if ( substr($file,0,1) == "0" )
  {
  $nicename =  substr($file,2,2) . "-" . substr($file,4,5);
  $physical[$file] = $nicename;
  }
}

closedir($dir);

// Run for each serial number

foreach ( $physical as $serial => $niceserial )
   {
	echo "<br>$niceserial";
        $dir = opendir("/buxs/sbstats2/2010/$serial/05");
        while(false != ($file = readdir($dir)))
           {
           echo "<br>    $file";
	   }
         closedir($dir);
   }




?>

