<?

include '/home/secrets/www/stats/2013/library.php';

session_start();

$FileName = "$_GET[file]";
$ReportName = "$_GET[report]";

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
$Types['aix']['vmstat'][2] = "kth-r,kth-b,memory-avm,memory-fre,page-re,page-pi,page-po,page-fr,page-sr,page-cy,faults-in,faults-sy,faults-cs,cpu-us,cpu-sy,cpu-id,cpu-wa,skip,cpu-pc,skip,cpu-ec";

$Report['base']['aix']['vmstat'][0] = "HH:MM";
$Report['base']['aix']['vmstat'][2]['cpu-sy'] = array(0, 66, 132);
$Report['base']['aix']['vmstat'][2]['cpu-us'] = array(255,   66,   8);
$Report['base']['aix']['vmstat'][2]['cpu-wa'] = array(255, 214,   33);

$Report['page']['aix']['vmstat'][0] = "HH:MM";
$Report['page']['aix']['vmstat'][2]['page-pi'] = array(0, 66, 132);
$Report['page']['aix']['vmstat'][2]['page-po'] = array(255,   66,   8);

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
        list( $date,$serial,$machinedesc,$host,$type,$report) = explode(":",$basename[1]);
//	echo "$type,$report";
        $temp = explode(".",$report);
        $report = $temp[0];

	$Titles = explode(",",$Types[$type][$report][2]);


foreach($Report as $Index => $ThisReport)
        foreach($ThisReport as $ThisType => $ThisReport)
                foreach($ThisReport as $ThisReportName => $ThisReport)
                if($Index == $ReportName)
                  if($ThisType == $type)
                   {
                    if($ThisReportName == $report)
                        {
        //                $criteria = explode(",",$ThisReport[2]);
			$criteria = $ThisReport[2];
                        }
                }


        if(($handle = fopen($file, "r")) !== FALSE)
        {
        while (($data = fgetcsv($handle, 1000, " ")) !== FALSE)
                {
                $key = substr($data[0],0,2) . substr($data[0],3,2);
		$datum = explode(",",$data[2]);
		foreach($datum as $key2 => $item)
			{
			$criteriacount = count($criteria);
			if(array_key_exists($Titles[$key2],$criteria))
			    { 
//			    $ReportPos = array_search($Titles[$key2],$criteria);
		       //     $TempArray[$ReportPos][$Titles[$key2]] = $item;
				$TempArray[$Titles[$key2]] = $item;
			    }

                       $actualcount = count($TempArray);
			if($criteriacount==$actualcount)
			   {
	//			print_r($TempArray);
	//			ksort($TempArray);
			   foreach($TempArray as $Key => $Item)
			    //foreach($TempArrayitem as $Key => $Item)
				$Target[$host][$date][$serial][$ReportName][$key][$Key] = $Item;
		           }
			}

                }
        fclose($handle);
        }
    }

//echo "<pre>";

$_SESSION['key'] = $criteria;
$_SESSION['dimension'] = $Graph['daily'];
$_SESSION['data'] = $Target;

echo "<br><img src=sessiongraph.php>";
echo "<br><img src=sessiongraph.php>";
echo "<br><img src=sessiongraph.php>";


print_r($_SESSION['data']);
echo "</pre>";
?>
