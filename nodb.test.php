<?php
$accessRights = 0770; // create databases/files/tables with these accessrights 
echo "<hr><h1 color='red'>test nodb database management commands</h1><br>";
include("nodb.php");

echo "<hr><h1 color='red'>test database commands</h1><br>";
comment("delete database");
success(delDatabase("databaseTest1"));
success(delDatabase("databaseTest2"));

comment("create database");
success(addDatabase("databaseTest1",$accessRights));

comment("copy database");
success(copyDatabase("databaseTest1","databaseTest2"));

comment("rename database");
success(renameDatabase("databaseTest2","databaseTest3"));

comment("delete database");
success(delDatabase("databaseTest3"));

echo "<hr><h1 color='red'>test table commands</h1><br>";

comment("create table");
success(addTable("testTable1","databaseTest1",$accessRights));

comment("copy table");
success(copyTable("testTable1","testTable2","databaseTest1",$accessRights));

comment("rename table");
success(renameTable("testTable2","testTable3","databaseTest1"));

comment("delete table");
success(delTable("testTable3"));

echo "<hr><h1 color='red'>test column commands</h1><br>";

comment("create column");
success(addColumn("columnTest1","testTable1","databaseTest1",$accessRights));

comment("create column");
success(addColumn("columnTest2","testTable1","databaseTest1",$accessRights));

comment("rename column");
success(renameColumn("columnTest2","columnTest3","testTable1"));

comment("delete column");
success(delColumn("columnTest1"));
success(delColumn("columnTest3"));

echo "<hr><h1 color='red'>test record management commands</h1><br>";

success(addColumn("name","testTable1","databaseTest1",$accessRights));
success(addColumn("street","testTable1","databaseTest1",$accessRights));
success(addColumn("phone","testTable1","databaseTest1",$accessRights));
success(addColumn("mail","testTable1","databaseTest1",$accessRights));

comment("add record at end (no lineNumber given)");
$name = "tom";
success(add("name:".$name.";street:street;phone:+00981232112312;mail:".$name."@mail.com;"));
$name = "jerry";
success(add("name:".$name.";street:street;phone:+00981232112312;mail:".$name."@mail.com;"));

comment("insert record at position (lineNumber given)");
$name = "joe";
success(insert(1,"name:".$name.";street:street;phone:+00981232112312;mail:".$name."@mail.com;"));
$name = "jim";
success(insert(3,"name:".$name.";street:street;phone:+00981232112312;mail:".$name."@mail.com;"));
$name = "jeremy";
success(insert(0,"name:".$name.";street:street;phone:+00981232112312;mail:".$name."@mail.com;"));

comment("change/replace/update record");	
success(change(2,"name:jill;phone:+12345;"));

comment("change/replace/update multiple records");
success(change(array(0,1,2),"name:jill;phone:+12345;"));

comment("change/replace/update the all records where name = jill with joe");
success(change(where("jill","name"),"name:joe;phone:+999999;"));

echo "<hr><h1 color='red'>try read commands</h1><br>";

comment("get one single record from table");
print_r_html(read(0,$tableName,$dbname));
success($worked);

comment("get one mutliple record from a table");
print_r_html(read(array(0,1,2)));
success($worked);

comment("get a range of records from a table");
print_r_html(read("0-3"));
success($worked);

comment("get all records where name == 'jim' read(where('jim'));");
print_r_html(read(where('jim','name')));
success($worked);

comment("get all records from a table, top-array-keys represent the columns");
print_r_html(readTable("name"));
success($worked);

comment("get whole database as a object-array with sub arrays");
print_r_html(readDatabase());
success($worked);

echo "<hr><h1 color='red'>try delete commands</h1><br>";

comment("try to delete with problematic index");
success(delete(null));

comment("delete one record");
success(delete(0));

comment("delete multiple records");
success(delete(array(0,1,2)));

echo "<hr><h1 color='red'>import / export commands</h1><br>";

// importMySQL($mysqldumb); // parses the mysqldumb and tries to create a file-based database

// exportMySQL($dbname); // tries to create a MySQL-dumb of the file-based-database

echo "<hr><h1 color='red'>DESTROY TEST DATABASE</h1><br>";

comment("delete database");
success(delDatabase("databaseTest1"));

echo "<hr><h1 color='red'>test file operations command 'ls'</h1><br>";

comment("read parent directory");
print_r_html(ls(".."));
comment("read absolute path");
print_r_html(ls("/var/www"));

comment("read current directory");
print_r_html(ls("."));
comment("0 = SORT_REGULAR - Default. Compare items normally (don't change types)");
$sort = SORT_REGULAR;
print_r_html(ls(".",$sort));

comment("1 = SORT_NUMERIC - Compare items numerically");
$sort = SORT_NUMERIC;
print_r_html(ls(".",$sort));

comment("2 = SORT_STRING - Compare items as strings");
$sort = SORT_STRING;
print_r_html(ls(".",$sort));

comment("3 = SORT_LOCALE_STRING - Compare items as strings, based on current locale");
$sort = SORT_LOCALE_STRING;
print_r_html(ls(".",$sort));

comment(" 4 = SORT_NATURAL - Compare items as strings using natural ordering");
$sort = SORT_NATURAL;
print_r_html(ls(".",$sort));

comment("5 = SORT_FLAG_CASE -");
$sort = SORT_FLAG_CASE;
print_r_html(ls(".",$sort));

/* print an array or variable like print_r would do it but with browser readable <br> instead of \n linebreaks */
function print_r_html($input)
{
	echo str_replace(array("\r\n", "\r","\n"), "<br>", print_r($input,true));
}

/* explain what is being done */
function comment($input)
{
	echo "<h3>".strval($input)."____________________________________________________________</h3><br>";
}
// colorful output about the outcomes of the functions
function success($worked)
{
	if($worked)
	{
		echo "<h3 style='color:green;'>worked</h3><br>";
	}
	else
	{
		echo "<h3 style='color:red;'>failed</h3><br>";
	}
}
?>