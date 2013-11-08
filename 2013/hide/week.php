<?

$week = array();

for ($x = 0 ; $x < 7; $x++)
	{
	for ($y = 0; $y < 24; $y++)
		{
		$valy = sprintf('%02d', $y);
		for ($z = 0; $z < 60; $z = $z + 5)
			{
			$valz = sprintf('%02d', $z);
			$val = $valy . $valz;
			$week[$x][$val] = "";
			}
		}
	}

print_r($week);


?>
