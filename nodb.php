<?php
$absolute_path_to_database_root_folder = "./databases"; // assume there is a folder called database in the current working directory
$slash = "/"; // windows_or_linux slash? linux slash is / windows slash is \
$default_accessRights = 0700; // the access rights, (chmod 0700) that folders and files will have per default when they are created and no access rights are specified

// if the database_root_folder does not exist create it
if(!is_dir($absolute_path_to_database_root_folder))
{
	if(empty($accessRights)) $accessRights = $default_accessRights;
	mkdir($absolute_path_to_database_root_folder,$accessRights); // grant only current user access to this folder
}

// database management commands
// addDatabase(dbname); create a folder inside folder database with the name $dbname
$lastDatabase = ""; // remember the created/last used database
function addDatabase($dbname,$accessRights = "")
{
	$worked = false;
	if(empty($accessRights)) $accessRights = $default_accessRights;
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights;
	if(empty($dbname)) $dbname = $lastDatabase;
	if(empty($tablename)) $tablename = $lastTable;
	if(empty($columname)) $columname = $lastColumn;
	
	$path = $absolute_path_to_database_root_folder.$slash.$dbname;
	if(!is_dir($dbname))
	{
		mkdir($path,$accessRights);
		$lastDatabase = $dbname;
		$worked = true;
	}
	else
	{
		trigger_error("error: can not create ".$path." the directory exists allready.");
	}
	return $worked;
}

// copy all files and folder from $dbnameSource to $dbnameDestination 
function copyDatabase($dbnameSource, $dbnameDestination)
{
	$worked = false;
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights;
	if(empty($dbnameSource)) $dbnameSource = $lastDatabase;
	$lastlastDatabase = $dbnameDestination;
	if(empty($tablename)) $tablename = $lastTable;
	if(empty($columname)) $columname = $lastColumn;

	$dbnameSource = $absolute_path_to_database_root_folder.$slash.$dbnameSource;
	$dbnameDestination = $absolute_path_to_database_root_folder.$slash.$dbnameDestination;
	if(is_dir($dbnameSource))
	{
		if(!is_dir($dbnameDestination))
		{
			recurse_copy($dbnameSource,$dbnameDestination);
			$lastDatabase = $lastlastDatabase;
			$worked = true;
		}
		else
		{
			trigger_error("error: can not copy ".$dbnameDestination." to ".$dbnameSource." the directory ".$dbnameDestination." does not exists.");
		}
	}
	else
	{
		trigger_error("error: can not copy ".$dbnameSource." the directory does not exists?");
	}

	return $worked;
}

// renameDatabase(dboldname = "",dbnewname); // rename folder dboldname to dbnewname
function renameDatabase($dboldname,$dbnewname)
{
	$worked = false;
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights;
	if(empty($dboldname)) $dboldname = $lastDatabase;
	if(empty($tablename)) $tablename = $lastTable;
	if(empty($columname)) $columname = $lastColumn;
	
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

// delDatabase($dbname); // effectively delete a folder
function delDatabase($dbname)
{
	$worked = false;
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights;
	if(empty($dbname)) $dbname = $lastDatabase;
	if(empty($tablename)) $tablename = $lastTable;
	if(empty($columname)) $columname = $lastColumn;
	
	$path = $absolute_path_to_database_root_folder.$slash.$dbname;
	if(is_dir($path))
	{
		rmdir_recursive($path);
		$lastDatabase = $dbname;
		$worked = true;
	}
	else
	{
		trigger_error("error: can not delete ".$path." the directory does not exists.");
	}
	
	return $worked;
}


// addTable(dbname = "",tablename); // effectively create a new folder "tablename" inside the folder "dbname"
$lastTable = ""; // remember the last used/created table
function addTable($tablename,$dbname = "",$accessRights = "")
{
	$worked = false;
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights;
	if(empty($dbname)) $dbname = $lastDatabase;
	if(empty($tablename)) $tablename = $lastTable;
	if(empty($columname)) $columname = $lastColumn;
	if(empty($accessRights)) $accessRights = $default_accessRights;

	$path = $absolute_path_to_database_root_folder.$slash.$dbname.$slash.$tablename;
	if(!is_dir($path))
	{
		mkdir($path,$accessRights);
		$lastTable = $tablename;
		$lastDatabase = $dbname;
		$worked = true;
	}
	else
	{
		trigger_error("error: can not create table-directory ".$path." the directory exists allready.");
	}
	
	return $worked;
}

// copy all files and folder from $dbnameSource to $dbnameDestination
function copyTable($tablenameSource, $tablenameDestination, $dbname = "")
{
	$worked = false;
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights;
	if(empty($dbname)) $dbname = $lastDatabase;
	if(empty($columname)) $columname = $lastColumn;

	$pathsourceource = $absolute_path_to_database_root_folder.$slash.$dbname.$slash.$tablenameSource;
	$pathdestination = $absolute_path_to_database_root_folder.$slash.$dbname.$slash.$tablenameDestination;
	if(is_dir($pathsourceource))
	{
		if(!is_dir($pathdestination))
		{
			recurse_copy($pathsourceource,$pathdestination);
			$lastTable = $tablenameDestination;
			$lastDatabase = $dbname;
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

// renameTable($dbname = "",$tableoldname = "",$tablenewname); // rename table
function renameTable($tableoldname,$tablenewname,$dbname = "")
{
	$worked = false;
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights;
	if(empty($dbname)) $dbname = $lastDatabase;
	if(empty($tableoldname)) $tableoldname = $lastTable;
	if(empty($columname)) $columname = $lastColumn;
	
	$oldpath = $absolute_path_to_database_root_folder.$slash.$dbname.$slash.$tableoldname;
	$newpath = $absolute_path_to_database_root_folder.$slash.$dbname.$slash.$tablenewname;
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

// delTable($dbname = "",$tablename); // effectively create a new folder "tablename" inside the folder "dbname"
function delTable($tablename,$dbname = "")
{
	$worked = false;
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights;
	if(empty($dbname)) $dbname = $lastDatabase;
	if(empty($tablename)) $tablename = $lastTable;
	if(empty($columname)) $columname = $lastColumn;

	$path = $absolute_path_to_database_root_folder.$slash.$dbname.$slash.$tablename;
	if(is_dir($path))
	{
		rmdir_recursive($path);
		$lastTable = $tablename;
		$lastDatabase = $dbname;
		$worked = true;
	}
	else
	{
		trigger_error("error: can not delete ".$path." the directory does not exists.");
	}
	
	return $worked;
}


// addColumn($dbname = "",$tablename = "",$columname); // effectively creates a file called "columname" inside tablename
$lastColumn = ""; // remember the last used/worked with column
function addColumn($columname,$tablename = "",$dbname = "",$accessRights = "")
{
	$worked = false;
	if(empty($accessRights)) $accessRights = $default_accessRights;
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights;
	if(empty($dbname)) $dbname = $lastDatabase;
	if(empty($tablename)) $tablename = $lastTable;
	if(empty($columname)) $columname = $lastColumn;
	
	$path = $absolute_path_to_database_root_folder.$slash.$dbname.$slash.$tablename.$slash.$columname.".php";
	if(!is_dir($path))
	{
		touch($path,time());
		chmod($path, $accessRights);
		$lastColumn = $columname;
		$lastTable = $tablename;
		$lastDatabase = $dbname;
		$worked = true;
	}
	else
	{
		trigger_error("error: can not create file ".$path." the file allready exists?");
	}
	$worked;
}

// renameColumn($dbname = "",$tablename = "",$columnoldname = "",$columnnewname); // rename column-file
function renameColumn($columnoldname,$columnnewname = "",$tablename = "",$dbname = "")
{
	$worked = false;
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights;
	if(empty($dbname)) $dbname = $lastDatabase;
	if(empty($tablename)) $tablename = $lastTable;
	if(empty($columname)) $columname = $lastColumn;
	
	$oldpath = $absolute_path_to_database_root_folder.$slash.$dbname.$slash.$tablename.$slash.$columnoldname.".php";
	$newpath = $absolute_path_to_database_root_folder.$slash.$dbname.$slash.$tablename.$slash.$columnnewname.".php";
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

// delColumn($dbname = "",$tablename); // effectively delete file called $tablename
function delColumn($columnname,$tablename = "",$dbname = "")
{
	
	$worked = false;
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights;
	if(empty($dbname)) $dbname = $lastDatabase;
	if(empty($tablename)) $tablename = $lastTable;
	if(empty($columname)) $columname = $lastColumn;
	$path = $absolute_path_to_database_root_folder.$slash.$dbname.$slash.$tablename.$slash.$columnname.".php";
	if(is_file($path))
	{
		unlink($path);
		$lastColumn = $columnname;
		$worked = true;
	}
	else
	{
		trigger_error("error: can not delete ".$path." the file does not exists.");
	}
	
	$worked;
}

// database content changing commands
/* add($index,$columname_values,$tablename,$dbname) // adds a new line at pos $index
 $columname_values has the format key:value,
example:
name:tom;age:32;message:so and so;
*/
function add($columname_values,$tablename = "",$dbname = "")
{
	$worked = false;
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights;
	if(empty($dbname)) $dbname = $lastDatabase;
	if(empty($tablename)) $tablename = $lastTable;
	if(empty($columname)) $columname = $lastColumn;

	$pathtable = $absolute_path_to_database_root_folder.$slash.$dbname.$slash.$tablename;
	if(is_dir($pathtable))
	{
		// get a list of all files in the table-directory
		$files = ls($pathtable);
	
		// iterate over key:values and make it accessible
		$columns = explode(";",$columname_values);
	
		// iterate over files, compare filename to columnname, then insert value if available, else insert empty line
		$fileCount = count($files);
		for ($i = 0; $i < $fileCount; $i++) {
			$file = $files[$i];
			if(($file != ".")&&($file != ".."))
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
					// if column name found in $columname_values and as a file, insert line with linebreak
					file_put_contents($path, $value."\n", FILE_APPEND);
						
					$lastDatabase = $dbname;
					$lastTable = $tablename;
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

/* insert($index,$columname_values,$tablename,$dbname) // inserts a new line at pos $index
$columname_values has the format key:value,
example:
name:tom;age:32;message:so and so;
*/ 
function insert($index,$columname_values,$tablename = "",$dbname = "")
{
	$worked = false;
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights;
	if(empty($dbname)) $dbname = $lastDatabase;
	if(empty($tablename)) $tablename = $lastTable;
	if(empty($columname)) $columname = $lastColumn;

	$pathtable = $absolute_path_to_database_root_folder.$slash.$dbname.$slash.$tablename;
	if(is_dir($pathtable))
	{
		// get a list of all files in the table-directory
		$files = ls($pathtable);
	
		// iterate over key:values and make it accessible
		$columns = explode(";",$columname_values);
	
		// iterate over files, compare filename to columnname, then insert value if available, else insert empty line
		$fileCount = count($files);
		for ($i = 0; $i < $fileCount; $i++) {
			$file = $files[$i];
			if(($file != ".")&&($file != ".."))
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
					// if column name found in $columname_values and as a file, insert line with linebreak
					insertLineAt($index,$value."\n",$path);
						
					$lastDatabase = $dbname;
					$lastTable = $tablename;
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

/* function change($index,$columname_values,$merge = true,$tablename = "",$dbname = "")
update/change an existing record.
$merge = true, means that if there is no new value for a key, the original value will be kept
$merge = false, means the whole record will be overwritten with the new one, if there is no value for a column, an empty value will be inserted
example:
name:tom;age:32;message:so and so;
*/
function change($index,$columname_values,$merge = true,$tablename = "",$dbname = "")
{
	$worked = false;
	global $absolute_path_to_database_root_folder; global $slash; global $lastDatabase; global $lastTable; global $lastColumn; global $default_accessRights;
	if(empty($dbname)) $dbname = $lastDatabase;
	if(empty($tablename)) $tablename = $lastTable;
	if(empty($columname)) $columname = $lastColumn;

	$pathtable = $absolute_path_to_database_root_folder.$slash.$dbname.$slash.$tablename;
	if(is_dir($pathtable))
	{
		// get a list of all files in the table-directory
		$files = ls($pathtable);

		// iterate over key:values and make it accessible
		$columns = explode(";",$columname_values);

		// iterate over files, compare filename to columnname, then insert value if available, else insert empty line
		$fileCount = count($files);
		for ($i = 0; $i < $fileCount; $i++) {
			$file = $files[$i];
			if(($file != ".")&&($file != ".."))
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
					if($merge)
					{
						// keep the original value
					}
					else
					{
						// if column name found in $columname_values and as a file, insert line with linebreak
						insertLineAt($index,$value."\n",$path);
					}

					$lastDatabase = $dbname;
					$lastTable = $tablename;
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

// change("newvalue",$index,$columname,"newvalue",$tablename,$dbname); // change value at index(linenumber) index to "newvalue" inside columname.php

// delete($index,$tablename,$dbname); // delete the given $index line in all columns of that table

// read($index,$columname,$tablename,$dbname); // returns the exact value

// read($columname,$tablename,$dbname); // returns the content of the whole columname.php-file as array

// read($tablename,$dbname); // returns the whole table as a object-array

// read($dbname); // returns the whole database as a object-array with sub arrays

// read($index,$columname,$tablename,$dbname); // delete entry

// read($columname,$tablename,$dbname); // delete file columname.php

// read($tablename,$dbname); // delete folder tablename

// read($dbname); // delete the directory dbname with all files !!! WARNING !!! ;)

// import / export commands:

// importMySQL($mysqldumb); // parses the mysqldumb and tries to create a file-based database

// exportMySQL($dbname); // tries to create a MySQL-dumb of the file-based-database
?>