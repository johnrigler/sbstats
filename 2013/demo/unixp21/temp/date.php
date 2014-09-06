<?php
// set the default timezone to use. Available since PHP 5.1
date_default_timezone_set('UTC');


// Prints something like: Monday
echo date("l");

echo "\n";

// Prints something like: Monday 8th of August 2005 03:12:46 PM
echo date('l jS \of F Y h:i:s A');

echo "\n";

// Prints: July 1, 2000 is on a Saturday
$day = "13";
$month = "09";
$year = "2010";
echo date("Ww dmY", mktime(0, 0, 0, $month, $day, $year));

$day = "14";

echo "\n";

echo date("Ww dmY", mktime(0, 0, 0, $month, $day, $year));


echo "\n";

/* use the constants in the format parameter */
// prints something like: Wed, 25 Sep 2013 15:28:57 -0700
echo date(DATE_RFC2822);

echo "\n";

// prints something like: 2000-07-01T00:00:00+00:00
echo date(DATE_ATOM, mktime(0, 0, 0, 7, 1, 2000));
?> 
