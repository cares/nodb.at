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
include("time.php");

comment("preparing bench");

$repeats = 100;

$nodbObj = new nodb("./databases"); // specify root folder where all databases reside in
$nodbObj->logging = false; // logging slows it down
$nodbObj->accessRights = 0770; // create databases/files/tables with these accessrights
$nodbObj->warnings = false; // no slowdowns please

comment("create database");
success($nodbObj->addDatabase("databaseBench"));

comment("create table");
success($nodbObj->addTable("tableBench1","databaseBench"));

comment("create column");
success($nodbObj->addColumn("Name","tableBench1","databaseBench"));

comment("create column");
success($nodbObj->addColumn("Phone","tableBench1","databaseBench"));

comment("create column");
success($nodbObj->addColumn("EMail","tableBench1","databaseBench"));

global $start_timestamp;
$start_timestamp = getMicrotime(); // save start time

echo "<hr><h1 style=\"color: red;\">starting Bench: ".currentTime()."</h1>";

comment("sarting write Bench: writing ".$repeats.".x records");
for($i = 0;$i < $repeats;$i++)
{
	$nodbObj->add("Name:Nodb.at - Opensource Database;Phone:+11(111) 11 11 1;EMail:open@source.org;");
}

echo "<h1 >write bench completed in: ".currentTimeMS(). " ms</h1><hr>";

comment("starting modify Bench: modifying ".$repeats."x records");
for($i = 0;$i < $repeats;$i++)
{
	$nodbObj->update($i,"Name:Nodb.at - Opensource Database - works great;Phone:+22(222) 22 22 1;EMail:open@sourceRocks.org;");
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

/* print an array or variable like print_r would do it but with browser readable <br> instead of \n linebreaks */
function print_r_html($input)
{
	echo str_replace(array("\r\n", "\r","\n"), "<br>", print_r($input,true));
}

/* explain what is being done */
function comment($input)
{
	echo "<h1>".strval($input)."</h1> ";
}
// colorful output about the outcomes of the functions
function success($worked)
{
	if($worked)
	{
		echo "<span style=\"color:green;\">worked; </span>";
	}
	else
	{
		echo "<span style=\"color:red;\">failed; </span>";
	}
}

// destroy the instance, free the ram
unset($nodbObj); // happens automatically on end of program

?>
</body>
</html>
