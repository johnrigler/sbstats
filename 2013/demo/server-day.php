<? session_start(); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">


<html>

<head>
 <meta http-equiv="content-type" content="text/html;charset=UTF-8">

<?

include '/home/secrets/www/sbstats/2013/library.php';

if ($format !="htmldoc")
	{
	echo ' <title>Secret Beach Solutions</title>
 		<link rel="stylesheet" type="text/css" href="/style.css">';
	}

if ($format =="htmldoc")
	{
	echo '<link rel="stylesheet" type="text/css" href="/htmldoc.css">';
	}	

?>

</head>


<?

$dayno=$_REQUEST['day'];

$report = $_REQUEST['report'];
$Data = $_SESSION['W'];

//unset($_SESSION['W']);


?>

<!-- Begin Menu Choices -->
<div class=menuframe>
<?php

if($format != "htmldoc")
	echo "<h1>Secret Beach Solutions</h1>";

// map this directory



// Draw out the path

$length = strlen($_SERVER[DOCUMENT_ROOT]);
$cwd = getcwd();

if ($cwd[1] == ":") 
	{
	$path = explode('\\',substr($cwd,$length));
	}
	else
	{
	$path = explode('/',substr($cwd,$length));
	}

if($format != "htmldoc")
{
foreach($path as $index => $directory)
  {
  $countup = 0;
  $urlpath [] = "/" . $directory;
  echo "<div class=shortspacing style='float:left'>
   <div class=contentsbox>
      <a href=";

  while($countup <= $index)
	{
	$trimmed = $urlpath[$countup];
	echo "$trimmed";
	if($index == 0)
		{ 
		$urlpath[0] = "";
		$directory = "home";
		}
		
	$countup++;
	}

	echo " style=color:white> $directory </a> 
    </div>
  </div>
";


//	if ($countup > 3)echo "</div><div class=contentsbox>";
  } 
        echo "<div class=shortspacing style='float:left'><div class=contentsbox>
<a href=weekindex.php?host=$host style=color:white>$host</a></div></div>"; 
}

//echo "</div>";




?>
</div>

<!-- End Menu Choices -->

<!-- Begin Table of Contents -->

<? 

if(count($dirs) > 0)
  {
  echo "<div class=contentsframe>";
  foreach($dirs as $subdir)
   {

?>
  <!-- Begin Table of Contents Entry -->
  <div class=shortspacing>
    <div class=contentsbox><a style='color:white' href=<? echo "weekindex.php?host=$subdir > $subdir"; ?></a></div>

  </div>
  <div class=spacing>
  <p><? if( file_exists("$subdir/desc.php"))include "$subdir/desc.php"; ?></p>
  <hr>
  <!-- End Table of Contents Entry -->

  </div>
<?   } ?>
</div>
<? } ?>

<!-- End Table of Contents -->

<div style='border:none;background-color: transparent'>


  <!-- Begin Body -->

<?

//   }


if($sections[$thispage])
{
echo "<div class=bodyframe>";

foreach ($sections[$thispage] as $file)
  {
  list($prefix,$postfix) = explode(".",$file);
  list($page,$section) = explode("-",$prefix);
  if($page == $thispage)
  {
    if($postfix == "php")
        {
        if($format != "htmldoc")echo "<div class=spacing><span class=section>$section</span>";
        include "$file";
        echo "</div>";
        }
    if($postfix == "png")
        if($format != "htmldoc")echo "
<div class=spacing><span class=section>$section</span>
  <div>
   <img src=$file>
  </div>
</div>
";
   }
  }
 }

?>
   </div>
 </div>

<?

echo "<pre>";

foreach ($_SESSION as $WM => $rest)
   foreach($rest as $YRW => $rest2)
    foreach($rest2 as $Host => $data)
     if($data['D'])
      foreach($data['D'] as $day => $file)
	if($dayno != $day)
	  unset($_SESSION[$WM][$YRW][$Host]['D'][$day]);

//print_r($_SESSION);

echo "<br><img src=server-day-graph.php?report=base>";
echo "<br><img src=server-day-graph.php?report=page>";
echo "<br><img src=server-day-graph.php?report=memory>";

?>



</body>
</html>
