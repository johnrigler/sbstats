<?

include 'library.php';

$Files = array();
$Types = array();
$Report = array();


$Files[0] = "20130825340CDT:0306A00D2:4204000000-true-2-enable-PowerPC_POWER6:uxdrtgwdv1:aix:vmstat.sbstats";
$Files[1] = "20130826341CDT:0306A00D2:4204000000-true-2-enable-PowerPC_POWER6:uxdrtgwdv1:aix:vmstat.sbstats";
$Files[2] = "20130827342CDT:0306A00D2:4204000000-true-2-enable-PowerPC_POWER6:uxdrtgwdv1:aix:vmstat.sbstats";
$Files[3] = "20130828343CDT:0306A00D2:4204000000-true-2-enable-PowerPC_POWER6:uxdrtgwdv1:aix:vmstat.sbstats";
$Files[4] = "20130829344CDT:0306A00D2:4204000000-true-2-enable-PowerPC_POWER6:uxdrtgwdv1:aix:vmstat.sbstats";
$Files[5] = "20130830345CDT:0306A00D2:4204000000-true-2-enable-PowerPC_POWER6:uxdrtgwdv1:aix:vmstat.sbstats";
$Files[6] = "20130831346CDT:0306A00D2:4204000000-true-2-enable-PowerPC_POWER6:uxdrtgwdv1:aix:vmstat.sbstats";

/*
$Files[0] = "20130913365EDT:SER:8-sparcv9-2520-MHz:uxcrcdip1:sunos:vmstat.sbstats";
$Files[1] = "20130914366EDT:SER:8-sparcv9-2520-MHz:uxcrcdip1:sunos:vmstat.sbstats";
$Files[2] = "20130915360EDT:SER:8-sparcv9-2520-MHz:uxcrcdip1:sunos:vmstat.sbstats";
$Files[3] = "20130916371EDT:SER:8-sparcv9-2520-MHz:uxcrcdip1:sunos:vmstat.sbstats";
*/

$Types['aix']['vmstat'][0] = "HH:MM";
$Types['aix']['vmstat'][1] = "LCPU:MEM-MB";
$Types['aix']['vmstat'][2] = "kth-r,kth-b,memory-avm,memory-fre,page-re,page-pi,page-po,page-fr,page-sr,page-cy,faults-in,faults-sy,faults-cs,cpu-us,cpu-sy,cpu-id,cpu-wa,skip,cpu-pc,skip,cpu-ec";

$Report['base']['aix']['vmstat'][0] = "HH:MM";
$Report['base']['aix']['vmstat'][2] = "cpu-sy,cpu-us,cpu-wa";

$Types['sunos']['vmstat'][0] = "HH:MM";
$Types['sunos']['vmstat'][1] = "LCPU:MEM-MB";
$Types['sunos']['vmstat'][2] = "kth-r,kth-b,kth-w,memory-swap,memory-free,page-re,page-mf,page-pi,page-po,page-fr,page-de,page-sr,disk-mo,disk-m1,disk-m5,disk-m6,faults-in,faults-sy,faults-cs,cpu-us,cpu-sy,cpu-id";

$Report['base']['sunos']['vmstat'][0] = "HH:MM";
$Report['base']['sunos']['vmstat'][2] = "cpu-sy,cpu-us";

/*

$ ssh uxcrcdip1 vmstat 2 2
Use of this computer system is for authorized and management approved use only. All usage is subject to monitoring. Unauthorized use is strictly prohibited and subject to prosecution and/or corrective action up to and including termination of employment.
 kthr      memory            page            disk          faults      cpu
 r b w   swap  free  re  mf pi po fr de sr m0 m1 m5 m6   in   sy   cs us sy id
 0 0 0 12807416 4573872 435 482 2488 21 21 0 0 2 0 4 1 14313 77029 21397 29 4 67
 0 0 0 12742856 4234048 0 33 0  0  0  0  0  0  0  0  1 6150 602419 5722 11 6 84
$


*/


$rest = PruneMultiDayArray( $Files , $Types , $Report['base'] );

print_r($rest);
?>
