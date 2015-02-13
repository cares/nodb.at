<?php
/*
# make shure you have php5-cli - commandline-Interpreter for scripting language PHP5 installed (php5-cgi is not enough)
# debian way of installing

apt-get update
apt-get install php5-cli

# than you should be able to call it from command line like this:

php5-cli -f class.nodb.bench.terminal.php 100 # run the benchmark with 100 records to write, modify, read and delete.
*/

echo 
"================== running nodb.at benchmark ================== 
/## /##                      /##                                
| ##| ##                     | ##                               
/#######   /######   /#######| #######      /######  /######    
| ##__  ## /##__  ## /##__  ##| ##__  ##    |____  ##|_  ##_/   
| ##  \ ##| ##  \ ##| ##  | ##| ##  \ ##     /#######  | ##     
| ##  | ##| ##  | ##| ##  | ##| ##  | ##    /##__  ##  | ## /## 
| ##  | ##|  ######/|  #######| #######//##|  #######  |  ####/ 
|__/  |__/ \______/  \_______/|_______/|__/ \_______/   \___/   
================== running nodb.at benchmark ================== 
";

include("class.nodb.php");
include("time.php");

global $argv;
var_dump($argv);

if (isset($argv[1])) {
	$repeats = $argv[1];
}
else
{
	$repeats = 10;
}

echo
"================== ".$repeats." records will be written, modified, read and deleted
";


comment("preparing bench");

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

echo "###################### starting Bench: \n";

comment("sarting write Bench: writing ".$repeats.".x records");
for($i = 0;$i < $repeats;$i++)
{
	$nodbObj->add("Name:Nodb.at - Opensource Database;Phone:+11(111) 11 11 1;EMail:open@source.org;");
}

echo "write bench completed in: ".currentTimeMS(). " ms\n";

comment("starting modify Bench: modifying ".$repeats."x records");
for($i = 0;$i < $repeats;$i++)
{
	$nodbObj->change($i,"Name:Nodb.at - Opensource Database - works great;Phone:+22(222) 22 22 1;EMail:open@sourceRocks.org;");
}

echo "modify bench completed in: ".currentTimeMS(). " ms\n";

comment("starting read Bench: reading ".$repeats."x records");
for($i = 0;$i < $repeats;$i++)
{
	$nodbObj->read($i);
}

echo "read bench completed in: ".currentTimeMS(). " ms\n";

comment("delete Bench: delete ".$repeats."x records");
$repeats_delete = $repeats-1;
for($i = $repeats_delete;$i > -1;$i--)
{
	$nodbObj->delete($i);
}

echo "###################### all benchmarks completed in: ".currentTimeMS(). " ms\n";

comment("delete database");
success($nodbObj->delDatabase("databaseBench"));

/* print an array or variable like print_r would do it but with browser readable <br> instead of \n linebreaks */
function print_r_html($input)
{
	echo print_r($input,true);
}

/* explain what is being done */
function comment($input)
{
	echo strval($input)."____________________________________________________________\n";
}
// colorful output about the outcomes of the functions
function success($worked)
{
	if($worked)
	{
		echo "worked\n";
	}
	else
	{
		echo "failed\n";
	}
}

// destroy the instance, free the ram
unset($nodbObj); // happens automatically on end of program

?>