<?

include '../sbstatarrays_lib.php';

session_start();
session_destroy();
session_start();

$report = $_GET['report'];


function ShowWeek($report,$host,$year,$week)
  {
  $count = 0;

  foreach(ExpandArray(LoadMultiDayArray($host,$year,$week)) as $Element)
        {
        $index = $week . "-" . $count++;
        $_SESSION[$index] = $Element;
        }
//  echo "<br><a href=dashboardwrapper.php?host=$host&report=$report><img src=dashboardweek.php?id=$week&report=$report ></a>";

  echo "<br><a href=dashboardwrapper.php?host=$host&report=vmstat1><img src=dashboardweek.php?id=$week&report=vmstat1 ></a>";
  echo "<br><a href=dashboardwrapper.php?host=$host&report=vmstat2><img src=dashboardweek.php?id=$week&report=vmstat2 ></a>";
  echo "<br><a href=dashboardwrapper.php?host=$host&report=vmstat3><img src=dashboardweek.php?id=$week&report=vmstat3 ></a>";
  echo "<br><a href=dashboardwrapper.php?host=$host&report=vmstat4><img src=dashboardweek.php?id=$week&report=vmstat4 ></a>";
  echo "<br><a href=dashboardwrapper.php?host=$host&report=vmstat5><img src=dashboardweek.php?id=$week&report=vmstat5 ></a>";


    echo "<br><a href=dashboardweek.php?id=$week&host=$host&debug=yes&report=$report>$week </a>";

  };

$host = $_GET['host'];


$dir = opendir("$host");
//    opendir("alliance");

while(false != ($file = readdir($dir)))
{
if($file == ".")continue;
if($file == "..")continue;

if("$host/$file")
   {
   $val = substr($file,8,2);
   $weeks[$val] = "W-$val";
    
   }
}

asort($weeks);

 
foreach ( $weeks as $Week)
	ShowWeek($report,$host,'2013',$Week);

?>
