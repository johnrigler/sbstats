<?

include '../sbstatarrays_lib.php';

session_start();
session_destroy();
session_start();

$report = $_GET['report'];

$report = "vmstat";


function ShowWeeks($report,$host,$weeks)
{
  foreach($weeks as $year => $week)
    foreach($week as $weekno => $Element)
      {
      $index = $year . "-" . $weekno;
      $_SESSION[$index] = $Element;
      echo  "<br><a href=dashboardwrapper.php?host=$host&report=$report&match=$index&debug=yes >$index</a>\n";
      }

//	print_r($_SESSION);

//  echo "<br><a href=dashboardwrapper.php?host=$host&report=$report><img src=dashboardweek.php?id=$week&report=$report ></a>";
/*
  echo "<br><a href=dashboardwrapper.php?host=$host&report=vmstat1><img src=dashboardweek.php?id=$week&report=vmstat1 ></a>";
  echo "<br><a href=dashboardwrapper.php?host=$host&report=vmstat2><img src=dashboardweek.php?id=$week&report=vmstat2 ></a>";
  echo "<br><a href=dashboardwrapper.php?host=$host&report=vmstat3><img src=dashboardweek.php?id=$week&report=vmstat3 ></a>";
  echo "<br><a href=dashboardwrapper.php?host=$host&report=vmstat4><img src=dashboardweek.php?id=$week&report=vmstat4 ></a>";
  echo "<br><a href=dashboardwrapper.php?host=$host&report=vmstat5><img src=dashboardweek.php?id=$week&report=vmstat5 ></a>";


    echo "<br><a href=dashboardweek.php?id=$week&host=$host&debug=yes&report=$report>$week </a>";
*/
  };

$host = $_GET['host'];
$host = "unixp21";


$dir = opendir("$host");
//    opendir("alliance");

$weeks = array();
$temp = array();

$count = 0;

while(false != ($file = readdir($dir)))
{

if(strstr($file,".sbstats"))
   {
   $temp[$count] = $file;
   $count++;
   }
}

sort($temp);

foreach($temp as $file)
{


   $year = substr($file,0,4);
   $month = substr($file,4,2);
   $day = substr($file,6,2);
   $week = date("W", mktime(0, 0, 0, $month, $day, $year));
   $dayno = date("w", mktime(0, 0, 0, $month, $day, $year));
   if($dayno == 0)$dayno = 7;
   $weeks[$year][$week][$dayno] = $file;

//   echo "<br>$host/$file $year $month $day $week $dayno";
}


//sort($weeks);


ExpandArray($weeks);
ShowWeeks($report,$host,$weeks);
?>
