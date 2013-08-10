<?php
$absolute_path_to_database_root_folder = "./databases"; // assume there is a folder called database in the current working directory
$slash = "/"; // windows_or_linux slash? linux slash is / windows slash is \
$default_accessRights = 0700; // the access rights, (chmod 0700) that folders and files will have per default when they are created and no access rights are specified

$worked = false; // is an variable that returns the success of the last operation, e.g. if a directory or file is not found it will be set to false

/* ================= INIT ================ */

// if the database_root_folder does not exist create it
if(!is_dir($absolute_path_to_database_root_folder))
{
	if(!is_long($accessRights)) $accessRights = $default_accessRights;
	mkdir($absolute_path_to_database_root_folder,$accessRights); // grant only current user access to this folder
}

/* ================= DATABASE MANAGEMENT ================ */

// database management commands
// addDatabase(dbname); create a folder inside folder database with the name $dbName
$lastDatabase = ""; // remember the created/last used database
function addDatabase($dbName,$accessRights = "")
{
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($accessRights)) $accessRights = $default_accessRights;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;
	
	$path = $absolute_path_to_database_root_folder.$slash.$dbName;
	if(!is_dir($dbName))
	{
		mkdir($path,$accessRights);
		$lastDatabase = $dbName;
		$worked = true;
	}
	else
	{
		trigger_error("error: can not create ".$path." the directory exists allready.");
	}
	return $worked;
}

// copy all files and folder from $dbNameSource to $dbNameDestination 
function copyDatabase($dbNameSource, $dbNameDestination)
{
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($dbNameSource)) $dbNameSource = $lastDatabase;
	$lastlastDatabase = $dbNameDestination;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;

	$dbNameSource = $absolute_path_to_database_root_folder.$slash.$dbNameSource;
	$dbNameDestination = $absolute_path_to_database_root_folder.$slash.$dbNameDestination;
	if(is_dir($dbNameSource))
	{
		if(!is_dir($dbNameDestination))
		{
			recurse_copy($dbNameSource,$dbNameDestination);
			$lastDatabase = $lastlastDatabase;
			$worked = true;
		}
		else
		{
			trigger_error("error: can not copy ".$dbNameDestination." to ".$dbNameSource." the directory ".$dbNameDestination." does not exists.");
		}
	}
	else
	{
		trigger_error("error: can not copy ".$dbNameSource." the directory does not exists?");
	}

	return $worked;
}

// renameDatabase(dboldname = "",dbnewname); // rename folder dboldname to dbnewname
function renameDatabase($dboldname,$dbnewname)
{
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($dboldname)) $dboldname = $lastDatabase;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;
	
	$oldpath = $absolute_path_to_database_root_folder.$slash.$dboldname;
	$newpath = $absolute_path_to_database_root_folder.$slash.$dbnewname;
	if(is_dir($oldpath))
	{
		if(!is_dir($newpath))
		{
			rename($oldpath,$newpath);
			$lastDatabase = $dbnewname;
			$worked = true;
		}
		else
		{
			trigger_error("error: can not rename ".$oldpath." to ".$newpath." the directory exists allready.");
		}
	}
	else
	{
		trigger_error("error: can not rename ".$oldpath." the directory does not exists?");
	}
	
	return $worked;
}

// delDatabase($dbName); // effectively delete a folder
function delDatabase($dbName)
{
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;
	
	$path = $absolute_path_to_database_root_folder.$slash.$dbName;
	if(is_dir($path))
	{
		rmdir_recursive($path);
		$lastDatabase = $dbName;
		$worked = true;
	}
	else
	{
		trigger_error("error: can not delete ".$path." the directory does not exists.");
	}
	
	return $worked;
}

/* ================= TABLE MANAGEMENT ================ */

// addTable(dbname = "",tablename); // effectively create a new folder "tablename" inside the folder "dbname"
$lastTable = ""; // remember the last used/created table
function addTable($tableName,$dbName = "",$accessRights = "")
{
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;
	if(empty($accessRights)) $accessRights = $default_accessRights;

	$path = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableName;
	if(!is_dir($path))
	{
		mkdir($path,$accessRights);
		$lastTable = $tableName;
		$lastDatabase = $dbName;
		$worked = true;
	}
	else
	{
		trigger_error("error: can not create table-directory ".$path." the directory exists allready.");
	}
	
	return $worked;
}

// copy all files and folder from $dbNameSource to $dbNameDestination
function copyTable($tableNameSource, $tableNameDestination, $dbName = "")
{
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($columnName)) $columnName = $lastColumn;

	$pathsourceource = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableNameSource;
	$pathdestination = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableNameDestination;
	if(is_dir($pathsourceource))
	{
		if(!is_dir($pathdestination))
		{
			recurse_copy($pathsourceource,$pathdestination);
			$lastTable = $tableNameDestination;
			$lastDatabase = $dbName;
			$worked = true;
		}
		else
		{
			trigger_error("error: can not copy ".$pathsourceource.", destination ".$pathdestination." does not exists.");
		}
	}
	else
	{
		trigger_error("error: can not copy directory ".$pathsourceource.", it does not exists?");
	}

	return $worked;
}

// renameTable($dbName = "",$tableoldname = "",$tablenewname); // rename table
function renameTable($tableoldname,$tablenewname,$dbName = "")
{
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($tableoldname)) $tableoldname = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;
	
	$oldpath = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableoldname;
	$newpath = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tablenewname;
	if(is_dir($oldpath))
	{
		if(!is_dir($newpath))
		{
			rename($oldpath,$newpath);
			$lastTable = $tablenewname;
			$worked = true;
		}
		else
		{
			trigger_error("error: can not rename ".$oldpath." to ".$newpath." the directory exists allready.");
		}
	}
	else
	{
		trigger_error("error: can not rename ".$oldpath." does not exists?");
	}
	
	return $worked;
}

// delTable($dbName = "",$tableName); // effectively create a new folder "tablename" inside the folder "dbname"
function delTable($tableName,$dbName = "")
{
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;

	$path = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableName;
	if(is_dir($path))
	{
		rmdir_recursive($path);
		$lastTable = $tableName;
		$lastDatabase = $dbName;
		$worked = true;
	}
	else
	{
		trigger_error("error: can not delete ".$path." the directory does not exists.");
	}
	
	return $worked;
}

/* ================= COLUMN MANAGEMENT ================ */

// addColumn($dbName = "",$tableName = "",$columnName); // effectively creates a file called "columname" inside tablename
$lastColumn = ""; // remember the last used/worked with column
function addColumn($columnName,$tableName = "",$dbName = "",$accessRights = "")
{
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($accessRights)) $accessRights = $default_accessRights;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;
	
	$path = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableName.$slash.$columnName.".php";
	if(!is_file($path))
	{
		touch($path,time());
		chmod($path, $accessRights);
		$lastColumn = $columnName;
		$lastTable = $tableName;
		$lastDatabase = $dbName;
		$worked = true;
	}
	else
	{
		trigger_error("error: can not create file ".$path." the file allready exists?");
	}
	$worked;
}

// renameColumn($dbName = "",$tableName = "",$columnoldname = "",$columnnewname); // rename column-file
function renameColumn($columnoldname,$columnnewname = "",$tableName = "",$dbName = "")
{
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;
	
	$oldpath = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableName.$slash.$columnoldname.".php";
	$newpath = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableName.$slash.$columnnewname.".php";
	if(is_file($oldpath))
	{
		if(!is_file($newpath))
		{
			rename($oldpath,$newpath);
			$lastColumn = $columnnewname;
			$worked = true;
		}
		else
		{
			trigger_error("error: can not rename ".$oldpath." to ".$newpath." the file exists allready?");
		}
	}
	else
	{
		trigger_error("error: can not rename ".$oldpath." the file does not exists?");
	}
	
	return $worked;
}

// delColumn($dbName = "",$tableName); // effectively delete file called $tableName
function delColumn($columnName,$tableName = "",$dbName = "")
{
	
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;
	$path = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableName.$slash.$columnName.".php";
	if(is_file($path))
	{
		unlink($path);
		$lastColumn = $columnName;
		$worked = true;
	}
	else
	{
		trigger_error("error: can not delete ".$path." the file does not exists.");
	}
	
	$worked;
}

/* ================= RECORD OPERATIONS ================ */

// database content changing commands
/* add($index,$columnName_values,$tableName,$dbName) // adds a new line at pos $index
 $columnName_values has the format key:value,
example:
name:tom;age:32;message:so and so;
*/
function add($columnName_values,$tableName = "",$dbName = "")
{
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;

	$pathtable = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableName;
	if(is_dir($pathtable))
	{
		// get a list of all files in the table-directory
		$files = ls($pathtable);
	
		// iterate over key:values and make it accessible
		$columns = explode(";",$columnName_values);
		$columns = array_filter( $columns, 'strlen' );
	
		// iterate over files, compare filename to columnname, then insert value if available, else insert empty line
		$fileCount = count($files);
		for ($i = 0; $i < $fileCount; $i++) {
			$file = $files[$i];
			if(!(($currentFile == ".") || ($currentFile == "..")))
			{
				$filename_without_ending = substr($file, 0, -4); // strip away .php
	
				// iterate over $columns and check if such columnname:value exists
				$columnsCount = count($columns);
				$found = false;
				for ($j = 0; $j < $columnsCount; $j++) {
					$key_value = explode(":",$columns[$j]);
					$key = $key_value[0];
					$value = $key_value[1];
					if($filename_without_ending == $key)
					{
						$found = true;
						$path = $pathtable.$slash.$key.".php";
						break;
					}
				}
				if($found)
				{
					// if column name found in $columnName_values and as a file, insert line with linebreak
					file_put_contents($path, $value."\n", FILE_APPEND);
						
					$lastDatabase = $dbName;
					$lastTable = $tableName;
					$lastColumn = $key;
					$worked = true;
				}
				else
				{
					// if not, insert a empty line with linebreak
					file_put_contents($path, "\n", FILE_APPEND);
				}
			}
		}
	}
	else
	{
		trigger_error("error: can not add to table ".$pathtable." the path does not exist?");
	}

	return $worked;
}

/* insert($index,$columnName_values,$tableName,$dbName) // inserts a new line at pos $index
$columnName_values has the format key:value,
example:
name:tom;age:32;message:so and so;
*/ 
function insert($index,$columnName_values,$tableName = "",$dbName = "")
{
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;

	$pathtable = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableName;
	if(is_dir($pathtable))
	{
		// get a list of all files in the table-directory
		$files = ls($pathtable);
	
		// iterate over key:values and make it accessible
		$columns = explode(";",$columnName_values);
		$columns = array_filter( $columns, 'strlen' );
	
		// iterate over files, compare filename to columnname, then insert value if available, else insert empty line
		$fileCount = count($files);
		for ($i = 0; $i < $fileCount; $i++) {
			$file = $files[$i];
			if(!(($currentFile == ".") || ($currentFile == "..")))
			{
				$filename_without_ending = substr($file, 0, -4); // strip away .php
	
				// iterate over $columns and check if such columnname:value exists
				$columnsCount = count($columns);
				$found = false;
				for ($j = 0; $j < $columnsCount; $j++) {
					$key_value = explode(":",$columns[$j]);
					$key = $key_value[0];
					$value = $key_value[1];
					if($filename_without_ending == $key)
					{
						$found = true;
						$path = $pathtable.$slash.$key.".php";
						break;
					}
				}
				if($found)
				{
					// if column name found in $columnName_values and as a file, insert line with linebreak
					insertLineAt($index,$value."\n",$path);
						
					$lastDatabase = $dbName;
					$lastTable = $tableName;
					$lastColumn = $key;
					$worked = true;
				}
				else
				{
					// if not, insert a empty line with linebreak
					insertLineAt($index,"\n",$path);
				}
			}
		}
	}
	else
	{
		trigger_error("error: can not add to table ".$pathtable." the path does not exist?");
	}

	return $worked;
}

/* function change($index,$columnName_values,$tableName = "",$dbName = "")

update/change one/multiple existing record(s).

if $index is an integer, only one record (with that index=LineNumber) will be changed.
if $index is a comma,separated,string 1,2,3,4 ... all records with these indices (LineNumbers) will be updated with the given $value

$index = LineNumber starts with 0 (first element/first line in column-file has lineNumber 0)

example:

change(2,"name:jill;phone:+12345;",$tableName);

change/updates/replace/modify the 3rd record/line (index is starting to count with 0) of the $tableName 

change(array(0,1,2),"name:jill;phone:+12345;",$tableName);

change/updates/replace/modify the column "name" with jill at the the first three records of $tableName
*/
function change($index,$columnName_values,$tableName = "",$dbName = "")
{
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;
	
	if(is_int($index)||is_array($index))
	{
		$pathtable = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableName;
		if(is_dir($pathtable))
		{
			// iterate over key:values and make it accessible
			$columns = explode(";",$columnName_values);
			$columns = array_filter( $columns, 'strlen' );
	
			// iterate over $columns and check if such columnname:value exists
			$columnsCount = count($columns);
			for ($j = 0; $j < $columnsCount; $j++)
			{
				$column2Change = explode(":",$columns[$j]);
				$filename = $key = $column2Change[0];
				$filename_with_ending = $filename.".php";
	
				$value = $column2Change[1];
				$path_to_file = $pathtable.$slash.$filename_with_ending;
				if(is_file($path_to_file))
				{
					$lines = file($path_to_file); // read lines
					// update/change one line
					if(is_int($index)) {
						$lines[$index] = $value."\n"; // make changes // array_splice($lines,$lineNumber,1, array($line)); // 0 = insert between elements 1 = replace element
					}
					// update/change multiple lines
					if(is_array($index)) {
						$lines2ChangeCount = count($index);
						for($x = 0; $x < $lines2ChangeCount; $x++)
						{
							$changeThisLine = $index[$x];
							$lines[$changeThisLine] = $value."\n"; // make changes // array_splice($lines,$lineNumber,1, array($line)); // 0 = insert between elements 1 = replace element
						}
					}
					$lines = array_values($lines); // reindex array
					file_put_contents($path_to_file,implode($lines));	// write back to file
					
					$lastDatabase = $dbName;
					$lastTable = $tableName;
					$lastColumn = $key;
					$worked = true;
				}
				else
				{
					trigger_error("function change(): can not change column-file ".$path_to_file." of table ".$pathtable." does it exist?");
				}
			}
		}
		else
		{
			trigger_error("function change(): can not change column-file in directory ".$pathtable." does it exist?");
		}
	}
	else
	{
		trigger_error("function change(): there is somehting wrong with the \$index=".$index." given.");
	}

	return $worked;
}

/*
 * deletes a record(s) with $index in $tableName
*/
function delete($index,$tableName = "",$dbName = "")
{
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;

	$path2table = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableName;
	
	if(is_int($index)||is_array($index))
	{
		// get all files of the given table
		$files = ls($path2table);
		
		$fileCount = count($files);
		// iterate over all files in table-directory
		for($x = 0;$x < $fileCount;$x++)
		{
			$currentFile = $files[$x];
			$path2column = $path2table.$slash.$currentFile;

			
			if(!(($currentFile == ".") || ($currentFile == "..")))
			{
				if(is_file($path2column))
				{
					$lines = file($path2column); // read lines
					if(is_int($index))
					{
						array_splice($lines,$index,1); // at $index replace 1 element with nothing
						$lines = array_values($lines); // reindex array
						file_put_contents($path2column,implode($lines));	// write back to file
		
						$lastDatabase = $dbName;
						$lastTable = $tableName;
						$lastColumn = $columnName;
						$worked = true;
					}
					else if(is_array($index))
					{
						$indexCount = count($index);
						for($i = 0;$i < $indexCount;$i++)
						{
							$currentIndex = $index[$i];
							$lines[$index[$i]] = ""; // change specified elements to null
						}
						$lines = array_filter( $lines, 'strlen' ); // delete all elements with null
						$lines = array_values($lines); // reindex array
						file_put_contents($path2column,implode($lines)); // write back to file
						
						$lastDatabase = $dbName;
						$lastTable = $tableName;
						$lastColumn = $columnName;
						$worked = true;
					}
				}
				else
				{
					trigger_error("function delete(): can not delete records in file-column ".$path2column." does it exist?");
				}
			}
		}
	}
	else
	{
		trigger_error("function delete(): can not delete record, there is something wrong with the \$index:".$index." given");
	}

	return $worked;
}

/* ================= READ ================ */

/* get one single record from table
 * 	// get mutliple records from a table read(array(0,1,2),$tableName,$dbname)
	// 1. get a range of records "from a table read("0-3")"
	// 2. get all records with name "jim" read(where("jim"));
 * */
function read($index,$tableName = "",$dbname = "")
{
	$result = array();

	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($accessRights)) $accessRights = $default_accessRights;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;

	$path_to_table = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableName;
	
	$files = ls($path_to_table);
	$fileCount = count($files);

	// 0. get one record from a table read(0,$tableName,$dbname)
	if(is_int($index))
	{
		for($i = 0;$i < $fileCount;$i++)
		{
			$currentFile = $files[$i];
			if(!(($currentFile == ".") || ($currentFile == "..")))
			{
				$lines = file($path_to_table.$slash.$currentFile);
				$currentFile = substr($currentFile, 0, -4); // strip away .php
				$lines[$index] = str_replace(array("\r\n", "\r", "\n"), "", $lines[$index]); // remove linebreaks
				$result[$currentFile] = $lines[$index];
			}
		}
	}
	// 1. get mutliple records from a table read(array(0,1,2),$tableName,$dbname)
	// 3. get all records where name == 'jim' read(where('jim'));"
	if(is_array($index))
	{
		$indexCount = count($index);

		// iterate over indices
		for($j = 0;$j < $indexCount;$j++)
		{
			$currentIndex = $index[$j];
			$subArray = array();

			// iterate over files/columns	
			for($i = 0;$i < $fileCount;$i++)
			{
				$currentFile = $files[$i];
				$lines = file($path_to_table.$slash.$currentFile);
				if(!(($currentFile == ".") || ($currentFile == "..")))
				{
					$key = substr($currentFile, 0, -4); // strip away .php
					$value = str_replace(array("\r\n", "\r", "\n"), "", $lines[$currentIndex]); // remove linebreaks
					$subArray[$key] = $value;
				}
			}
			$result[] = $subArray;
			$worked = true;
		}
	}
	// 2. get a range of records "from a table read("0-3")"
	if(is_string($index) && strpos($index,'-'))
	{
		$index_start_stop = split("-",$index);
		$start = $index_start_stop[0];
		$stop = $index_start_stop[1];

		// iterate over indices
		for($j = $start;$j < $stop;$j++)
		{
			$currentIndex = $j;
			$subArray = array();

			// iterate over files/columns
			for($i = 0;$i < $fileCount;$i++)
			{
				$currentFile = $files[$i];
				$lines = file($path_to_table.$slash.$currentFile);
				if(!(($currentFile == ".") || ($currentFile == "..")))
				{
					$key = substr($currentFile, 0, -4); // strip away .php
					$value = str_replace(array("\r\n", "\r", "\n"), "", $lines[$currentIndex]); // remove linebreaks
					$subArray[$key] = $value;
				}
			}
			$result[] = $subArray;
			$worked = true;
		}
	}
	
	return $result;
}

/* get all records from a table, top-array-keys represent the columns */
function readTable($tableName = "",$dbname = "")
{

}
/* get whole database as a object-array with sub arrays */
function readDatabase($dbname = "")
{

}
/* ================= SEARCH ================ */

/* search all columns, return array of format: 
 * $result[column1] = {1,2,3};
 * $result[column2] = {2,3,4};
 * */
function searchTable($searchFor,$tableName = "",$dbName = "")
{
	$result = array();
}
/* search all columns, return array of format:
 * $result[column1] = {1,2,3};
* $result[column2] = {2,3,4};
* */
function searchDatabase($searchFor,$dbName = "")
{
	$result = array();
}

/* return all line numbers that contain the given string */
function where($searchFor,$columnName = "",$tableName = "",$dbName = "")
{
	$result = array();
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights; global $worked;
	$worked = false;
	if(empty($dbName)) $dbName = $lastDatabase;
	if(empty($tableName)) $tableName = $lastTable;
	if(empty($columnName)) $columnName = $lastColumn;

	$path = $absolute_path_to_database_root_folder.$slash.$dbName.$slash.$tableName.$slash.$columnName.".php";
	if(is_file($path))
	{
		$lines = file($path);
		$linesCount = count($lines);
		for($lineNumber = 0;$lineNumber < $linesCount;$lineNumber++)
		{
			if($lines[$lineNumber] == $searchFor."\n")
			{
				array_push($result,$lineNumber);
				$lastColumn = $columnName;
				$lastTable = $tableName;
				$lastDatabase = $dbName;
				$worked = true;
			}
		}
	}
	else
	{
		trigger_error("error: can not search column-file ".$path." the file does not exist?");
	}
	
	return $result;
}

/* ================= IMPORT / EXPORT ================ */

/* nothing really finished here */
// import / export commands:

// importMySQL($mysqldumb); // parses the mysqldumb and tries to create a file-based database

// exportMySQL($dbName); // tries to create a MySQL-dumb of the file-based-database


/* ================= LIBRARY ================ */
/* $lineNumber = 1; // lineNumber at which the content will be inserted */
function insertLineAt($lineNumber,$line,$path)
{
	// read into array
	$lines = file($path);
	// add second line
	array_splice($lines,$lineNumber,0, array($line));
	// reindex array
	$array_reindexed = array_values($lines);
	// write back to file
	file_put_contents($path,implode($array_reindexed));
}
/* list directory, return array
 * $path = the directory to scan
* $sort = Optional. Specifies how to compare the array elements/items. Possible values:
* 0 = SORT_REGULAR - Default. Compare items normally (don't change types)
* 1 = SORT_NUMERIC - Compare items numerically
* 2 = SORT_STRING - Compare items as strings
* 3 = SORT_LOCALE_STRING - Compare items as strings, based on current locale
* 4 = SORT_NATURAL - Compare items as strings using natural ordering
* 5 = SORT_FLAG_CASE -
http://php.net/manual/en/array.sorting.php
*/
function ls($path,$sort = SORT_REGULAR)
{
	$files = array();
	$files = scandir($path);

	if($sort != 0)
	{
		sort($files,$sort);
	}

	return $files;
}

/* recursively copy files and folders */
function recurse_copy($src, $dst) {
	$dir = opendir ( $src );
	@mkdir ( $dst );
	while ( false !== ($file = readdir ( $dir )) ) {
		if (($file != '.') && ($file != '..')) {
			if (is_dir ( $src . '/' . $file )) {
				recurse_copy ( $src . '/' . $file, $dst . '/' . $file );
			} else {
				copy ( $src . '/' . $file, $dst . '/' . $file );
			}
		}
	}
	closedir ( $dir );
}

/* remove a directory including content recursively */
function rmdir_recursive($dir) {
	foreach ( scandir ( $dir ) as $file ) {
		if ('.' === $file || '..' === $file)
			continue;
		if (is_dir ( "$dir/$file" ))
			rmdir_recursive ( "$dir/$file" );
		else
			unlink ( "$dir/$file" );
	}
	rmdir ( $dir );
}
?>