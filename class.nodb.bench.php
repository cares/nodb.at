<html>
<head>
<style>
p {
	font-size: 12px;
}
h1 {
	font-size: 12px;
	font-weight: bold;
	color: blue;
}
</style>
</head>
<body>
<?php
echo "<hr><h3>bench <a target=\"blank\" href=\"http://nodb.at\">nodb.at</a> database</h3><hr>";

include("class.nodb.php");

comment("preparing bench");

$repeats = 3000;

$accessRights = 0770; // create databases/files/tables with these accessrights 
$nodbObj = new nodb("./databases"); // specify root folder where all databases reside in
$nodbObj->logging = false; // logging slows it down
$nodbObj->warnings = false; // no slowdowns please

comment("create database");
success($nodbObj->addDatabase("databaseBench",$accessRights));

comment("create table");
success($nodbObj->addTable("tableBench1","databaseBench",$accessRights));

comment("create column");
success($nodbObj->addColumn("Name","tableBench1","databaseBench",$accessRights));

comment("create column");
success($nodbObj->addColumn("Phone","tableBench1","databaseBench",$accessRights));

comment("create column");
success($nodbObj->addColumn("EMail","tableBench1","databaseBench",$accessRights));

global $start_timestamp;
$start_timestamp = microtime(); // save start time

echo "<hr><h1 style=\"color: red;\">starting Bench: ".currentTime()."</h1>";

comment("sarting write Bench: writing ".$repeats.".x records");
for($i = 0;$i < $repeats;$i++)
{
	$nodbObj->add("Name:Nodb.at - Opensource Database;Phone:+11(111) 11 11 1;EMail:open@source.org;");
}

echo "<hr><h1 >write bench completed in: ".currentTimeMS(). " ms";

comment("starting modify Bench: modifying ".$repeats."x records");
for($i = 0;$i < $repeats;$i++)
{
	$nodbObj->change($i,"Name:Nodb.at - Opensource Database - works great;Phone:+22(222) 22 22 1;EMail:open@sourceRocks.org;");
}

echo "<hr><h1 >modify bench completed in: ".currentTimeMS(). " ms";

comment("starting read Bench: reading ".$repeats."x records");
for($i = 0;$i < $repeats;$i++)
{
	$nodbObj->read($i);
}

echo "<hr><h1 >read bench completed in: ".currentTimeMS(). " ms";

comment("delete Bench: delete ".$repeats."x records");
$repeats_delete = $repeats-1;
for($i = $repeats_delete;$i > -1;$i--)
{
	$nodbObj->delete($i);
}

echo "<hr><h1 style=\"color: red;\">all benchmarks completed in: ".currentTimeMS(). " ms</h1>";

comment("delete database");
success($nodbObj->delDatabase("databaseBench"));

/* return time as human readable string between bench start and current time*/
function currentTime()
{
	$micro_date = microtime();

	global $start_timestamp;
	$stop_timestamp = microtime(); // save start time
	
	$timeTaken = $stop_timestamp-$start_timestamp;
	
	$date_array = explode(" ",$micro_date);
	$date = date("Y-m-d H:i:s",$date_array[1]);
	
	return $date." ". $date_array[0];
}

/* return time between bench start and current time in ms */
function currentTimeMS()
{
	$micro_date = microtime();

	global $start_timestamp;
	$stop_timestamp = microtime(); // save start time
	
	$timeTaken = $stop_timestamp-$start_timestamp;
	
	return $timeTaken;
}

/* print an array or variable like print_r would do it but with browser readable <br> instead of \n linebreaks */
function print_r_html($input)
{
	echo str_replace(array("\r\n", "\r","\n"), "<br>", print_r($input,true));
}

/* explain what is being done */
function comment($input)
{
	echo "<p>".strval($input)."____________________________________________________________</p>";
}
// colorful output about the outcomes of the functions
function success($worked)
{
	if($worked)
	{
		echo "<p style=\"color:green\";>worked</p>";
	}
	else
	{
		echo "<p style=\"color:red;\">failed</p>";
	}
}

// destroy the instance, free the ram
unset($nodbObj); // happens automatically on end of program

?>
</body>
</html>