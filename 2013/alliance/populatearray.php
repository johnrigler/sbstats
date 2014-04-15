<?

include '/home/secrets/www/stats/2013/library.php';

session_start();

$FileName = "$_GET[file]";
//$ReportName = "$_GET[report]";

$ReportNameArr = array();
$ReportNameArr[0] = "base";
$ReportNameArr[1] = "page";

//// Define and Setup Visual Informaiton

// create a 200*200 image
$img = imagecreatetruecolor(1000, 300);

// allocate some colors
$color = array();
$color['cpu-us'] = imagecolorallocate($img, 0, 66, 132);
$color['cpu-sy'] = imagecolorallocate($img, 255,   66,   8);
$color['cpu-wa'] = imagecolorallocate($img, 255, 214,   33);
$white = imagecolorallocate($img, 255,255,255);
$frame = imagecolorallocate($img, 100,100,100);
$background = imagecolorallocate($img,155,155,155);

/// Fill and paint frame

imagefilledrectangle($img, 0, 0, 1020, 300, $background);
imagerectangle($img, 40,60, 902, 241, $frame);


/////  Define Array Information

$Types = array();
$Report = array();

$Types['aix']['vmstat'][0] = "HH:MM";
$Types['aix']['vmstat'][1] = "LCPU:MEM-MB";
$Types['aix']['vmstat'][2] = "kth-r,kth-b,memory-avm,memory-fre,page-re,page-pi,page-po,page-fr,page-sr,page-cy,faults-in,faults-sy,faults-cs,cpu-us,cpu-sy,cpu-id,cpu-wa,skip,cpu-pc,skip,cpu-ec";

$Report['aix1']['sunos']['aix'][0] = "HH:MM";
$Report['aix1']['sunos']['aix'][2] = "cpu-sy,cpu-us,cpu-wa";


$Types['sunos']['vmstat'][0] = "HH:MM";
$Types['sunos']['vmstat'][1] = "LCPU:MEM-MB";
$Types['sunos']['vmstat'][2] = "kth-r,kth-b,memory-avm,memory-fre,page-re,page-pi,page-po,page-fr,page-sr,page-cy,faults-in,faults-sy,faults-cs,cpu-us,cpu-sy,cpu-id,cpu-wa,skip,cpu-pc,skip,cpu-ec";

$Report['sunos1']['sunos']['vmstat'][0] = "HH:MM";
$Report['sunos1']['sunos']['vmstat'][2] = "cpu-sy,cpu-us,cpu-wa";


$Types['aix']['vmstat'][0] = "HH:MM";
$Types['aix']['vmstat'][1] = "LCPU:MEM-MB";
$Types['aix']['vmstat'][2] = "kth-r,kth-b,mem-avm,mem-fre,page-re,page-pi,page-po,page-fr,page-sr,page-cy,flts-in,flts-sy,flts-cs,cpu-us,cpu-sy,cpu-id,cpu-wa,skip,cpu-pc,skip,cpu-ec";

$Report['cpu']['aix']['vmstat'][0] = "HH:MM";
$Report['cpu']['aix']['vmstat'][2]['cpu-sy'] = array(0, 66, 132);
$Report['cpu']['aix']['vmstat'][2]['cpu-us'] = array(255,   66,   8);
$Report['cpu']['aix']['vmstat'][2]['cpu-wa'] = array(255, 214,   33);
$Report['cpu']['aix']['vmstat'][3] = array(1);

$Report['faults']['aix']['vmstat'][0] = "HH:MM";
$Report['faults']['aix']['vmstat'][2]['flts-in'] = array(0, 66, 132);
$Report['faults']['aix']['vmstat'][2]['flts-sy'] = array(255,   66,   8);
$Report['faults']['aix']['vmstat'][2]['flts-cs'] = array(255, 214,   33);
$Report['faults']['aix']['vmstat'][3] = array(.0005);


$Report['page']['aix']['vmstat'][0] = "HH:MM";
$Report['page']['aix']['vmstat'][2]['page-pi'] = array(20, 66, 100);
$Report['page']['aix']['vmstat'][2]['page-po'] = array(205,   120,   8);
$Report['page']['aix']['vmstat'][3] = array(1);

$Report['memory']['aix']['vmstat'][0] = "HH:MM";
$Report['memory']['aix']['vmstat'][2]['mem-avm'] = array(33, 66, 132);
$Report['memory']['aix']['vmstat'][2]['mem-fre'] = array(255,   66,   68);
$Report['memory']['aix']['vmstat'][3] = array(.00002);


$Graph['daily']['frame'] = array(930,170);
$Graph['daily']['innerframe'] = array(40,140,902,40);
$Graph['daily']['unit'] = array(1,2);
$Graph['daily']['space'] = array(0,0);
$Graph['daily']['key'] = array(450,10);

$Types['sunos']['vmstat'][0] = "HH:MM";
$Types['sunos']['vmstat'][1] = "LCPU:MEM-MB";
$Types['sunos']['vmstat'][2] = "kth-r,kth-b,kth-w,memory-swap,memory-free,page-re,page-mf,page-pi,page-po,page-fr,page-de,page-sr,disk-mo,disk-m1,disk-m5,disk-m6,faults-in,faults-sy,faults-cs,cpu-us,cpu-sy,cpu-id";

$Report['base']['sunos']['vmstat'][0] = "HH:MM";
$Report['base']['sunos']['vmstat'][2] = "cpu-sy,cpu-us";

$Types['sco']['vmstat'][0] = "HH:MM";
$Types['sco']['vmstat'][2] = 'procs-r,procs-b,procs-w,paging-frs,paging-dmd,paging-sw,paging-cch,paging-fil,paging-pft,paging-frp,paging-pos,paging-pif,paging-pis,paging-rso,paging-rsi,system-sy,system-cs,cpu-us,cpu-su,cpu-id';


//$rest = MultiDayArray( $Files , $Types , $Report['base'] );

//echo"<pre>$_GET[file]</pre>";

$dirFiles[0] = $FileName;

$Target = array();
$criteria = array();

echo "<pre>";

foreach($dirFiles as $file)
        {
	$basename = explode("/",$file);
        $day = substr($file,10,1);
  //      $dirDays[$day] = $file;
        list( $date,$serial,$machinedesc,$host,$type,$source) = explode(":",$basename[1]);
//	echo "$type,$report";
        $temp = explode(".",$source);
        $source = $temp[0];


	$Titles = explode(",",$Types[$type][$source][2]);


        if(($handle = fopen($file, "r")) !== FALSE)
        {
        while (($data = fgetcsv($handle, 1000, " ")) !== FALSE)
                {
                $hhmm = substr($data[0],0,2) . substr($data[0],3,2);
		$datum = explode(",",$data[2]);
		foreach($datum as $key2 => $item)
				$Target[$host][$date][$serial][$hhmm][$Titles[$key2]] = $item;

                }
        fclose($handle);
        }
  //  }
}

//echo "<pre>";

$_SESSION['report'] = $Report;
$_SESSION['key'] = $criteria;
$_SESSION['dimension'] = $Graph['daily'];
$_SESSION['data'] = $Target;

echo "<a href=sessiongraph.php?report=memory&type=$type&source=$source> aaaa </a>";

echo "<br><img src=sessiongraph.php?report=cpu&type=$type&source=$source>";
echo "<br><img src=sessiongraph.php?report=page&type=$type&source=$source>";
echo "<br><img src=sessiongraph.php?report=memory&type=$type&source=$source>";
echo "<br><img src=sessiongraph.php?report=faults&type=$type&source=$source>";


//print_r($Target);
echo "</pre>";
?>
