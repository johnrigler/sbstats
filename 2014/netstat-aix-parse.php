<?


$file="netstat-en0.sbstats";

$hour=12;
$minute=35;


#      input   (en0)      output           input   (Total)    output
#  packets  errs  packets  errs colls  packets  errs  packets  errs colls

$titles = array("input_packets","input_errs","output_packets","output_errs");

$line=0;
$interface = "";

$result = array();
$temparray = array();

if(($handle = fopen($file, "r")) !== FALSE)
        while (($data = fgets($handle, 1000)) !== FALSE)	
	{

		if($line ==0)
		    {  
		    $data2 = explode("(",$data);
		    $data3 = explode(")",$data2[1]);
		    $interface = $data3[0];
		    }
	
		$modulus = $line%23;

		if(($line%23)<3)
			{ $line = $line + 1;
			continue;
			}

		if($line > 2)
		{
		$key = ($line-3);
		$minutes= ($key % 12) * 5;
		$hour = sprintf('%02d',floor($key / 12));
		$minutes = sprintf('%02d', $minutes); 
		$timestamp="$hour:$minutes";
		$temparray = preg_split('/\s+/', $data);
		$result[$timestamp][$interface]['input_packets'] = $temparray[1];
                $result[$timestamp][$interface]['input_errs'] = "$temparray[2]";
                $result[$timestamp][$interface]['output_packets'] = $temparray[3];
                $result[$timestamp][$interface]['output_errs'] = $temparray[4];
		}		
	$line = $line + 1;
	}

print_r($result);

?>
