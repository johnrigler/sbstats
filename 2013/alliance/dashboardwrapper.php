<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<? $format=$_GET['format']; ?>

<head>
 <meta http-equiv="content-type" content="text/html;charset=UTF-8">
<? if ($format !="htmldoc")
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

$host=$_GET['host'];


?>

<!-- Begin Menu Choices -->
<div class=menuframe>
<?php

if($format != "htmldoc")
	echo "<h1>Secret Beach Solutions</h1>";

// map this directory

$dir = opendir("$host");

while(false != ($file = readdir($dir))) 
{
if($file == "images")continue;
if($file == "sbreports")continue;
if($file == "cgi-bin")continue;
if($file == ".")continue;
if($file == "..")continue;

echo "<br>$file";

$files [] = $file;
}

sort($files);

foreach($files as $file)
{

if(is_dir($file) == 1)$dirs [] = $file;
if(is_file($file) == 1)
  if(is_numeric($file[0]))
    {
    $files [] = $file;
    $sections[$file[0]] []= $file;
    }

}

// Read the format 

$format=($_GET[format]);

// figure out what page this is. 

$thispage=($_GET[page]);

if (! $thispage)$thispage=1;

if($thispage==1)
   {
   $nextpage = 2;
   }
   else
   {
   $nextpage = $thispage + 1;
   $prevpage = $thispage - 1;
   }

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

	echo " style=color:white> $directory</a> 
    </div>
  </div>
";

	if ($countup > 3)echo "</div><div class=contentsbox>";
  } 
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

<? 
if($format != "htmldoc")
{
 if($sections[$prevpage])
	$visibility="visible";
	else
	$visibility="hidden";

	echo "<div class=shortspacing style='float:left;visibility: $visibility '>
      <div class=contentsbox>
    <a href=?page=$prevpage style='color:white'> Prev </a>
      </div>
   </div>";


if($sections[$nextpage])
        $visibility="visible";
        else
        $visibility="hidden";

  echo "<div class=shortspacing style='border:none;float:left;visibility: $visibility '>
      <div class=contentsbox style='border:none'>
      <a href=?page=$nextpage style='color:white'> Next </a>
      </div>
   </div>
</div>";
}

?>

<!-- End Forward/Back Choices -->


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
</body>
</html>
