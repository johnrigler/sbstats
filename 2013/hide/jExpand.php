<? include 'jExpandlib.php'; 

$Array = array();

$Array[0] = 'US';
$Array[1] = 'GB';
$Array[2] = 'India';
$Array[3] = 'Canada';

?>

<body>

<?
ExpandableDebugArray($Array,"Country");
?>

</body>
</html>
