<?


$file="vmstat.out";

$hour=12;
$minute=35;

#kthr :    memory   :           page          :    faults   :     cpu
#-----:  -----------: ------------------------: ------------: -----------

$titles = array('skip','kthr_r','kthr_b','mem_avm','mem_fre','page_re','page_pi','page_po','page_fr','page_sr','page_cy','flts_in','flts_sy','flts_cs','cpu_us','cpu_sy','cpu_id','cpu_wa','skip');


$line=0;
$interface = "";

$result = array();
$temparray = array();

if(($handle = fopen($file, "r")) !== FALSE)
        while (($data = fgets($handle, 1000)) !== FALSE)	
	{

		if($line > 5)
		{
		$key = ($line-6);
		$minutes= ($key % 12) * 5;
		$hour = sprintf('%02d',floor($key / 12));
		$minutes = sprintf('%02d', $minutes); 
		$timestamp="$hour:$minutes";
		$temparray = preg_split('/\s+/', $data);
		foreach($temparray as $count => $item)
			{
			$result[$timestamp][$titles[$count]] = $item;
			}
		}		
	$line = $line + 1;
	}

print_r($result);

?>
