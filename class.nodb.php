<?php
/* nodb "engine" in oop-style (class->object) */

/* display all errors */
error_reporting(E_ALL);
ini_set('display_errors', '1');

class nodb {
	
	/* massive amounts of getters and setters */
	public $logging = true; // if there should be error and operation logging
	public function getlogging() {
		return $this->logging;
	}

	public function setlogging($logging) {
		$this->logging = $logging;
		
		if($logging)
		{
			$this->settings_log_errors = "nodb.error.log";
			$this->settings_log_operations = "nodb.operations.log";
		}
		else
		{
			$this->settings_log_errors = "";
			$this->settings_log_operations = "";
		}
	}

	public $warnings = true; // if you want to get a warning e.g. when you try to create a database that allready exists or writing to a table that does not exist
	public function getwarnings() {
		return $this->warnings;
	}
	
	public function setwarnings($warnings) {
		$this->warnings = $warnings;
	}

	public $absolute_path_to_database_root_folder = "./databases"; // assume there is a folder called database in the current working directory
	public function getAbsolute_path_to_database_root_folder() {
		return $this->absolute_path_to_database_root_folder;
	}
	
	public function setAbsolute_path_to_database_root_folder($absolute_path_to_database_root_folder) {
		$this->absolute_path_to_database_root_folder = $absolute_path_to_database_root_folder;
	}
	
	public $slash = "/"; // windows_or_linux slash? linux slash is / windows slash is \
	public function getSlash() {
		return $this->slash;
	}
	
	public function setSlash($slash) {
		$this->slash = $slash;
	}

	public $accessRights = 0700; // default access rights, (chmod 0700 = drwx------ = only creating user (www-data) has access) that folders and files will have per default when they are created and no access rights are specified
	public function getaccessRights() {
		return $this->accessRights;
	}
	
	public function setaccessRights($accessRights) {
		$this->accessRights = $accessRights;
	}

	public $settings_log_errors = "nodb.error.log"; // if errors should be logged to file, if not leave this empty
	public function getSettings_log_errors() {
		return $this->settings_log_errors;
	}
	
	public function setSettings_log_errors($settings_log_errors) {
		$this->settings_log_errors = $settings_log_errors;
	}
	
	public $settings_log_operations = "nodb.operations.log"; // if there should be a line written for every operation done (so you may be able to track problems)
	public function getSettings_log_operations() {
		return $this->settings_log_operations;
	}
	
	public function setSettings_log_operations($settings_log_operations) {
		$this->settings_log_operations = $settings_log_operations;
	}

	public $worked = false; // is an variable that returns the success of the last operation, e.g. if a directory or file is not found it will be set to false
	public function getWorked() {
		return $this->worked;
	}
	
	public function setWorked($worked) {
		$this->worked = $worked;
	}
	
	public $lastDatabase = ""; // remember the created/last used database
	public function getlastDatabase() {
		return $this->lastDatabase;
	}
	
	public function setlastDatabase($lastDatabase) {
		$this->lastDatabase = $lastDatabase;
	}
	
	public $lastDeletedDatabase = ""; // remember the created/last deleted database
	public function getlastDeletedDatabase() {
		return $this->lastDeletedDatabase;
	}
	
	public function setlastDeletedDatabase($lastDeletedDatabase) {
		$this->lastDeletedDatabase = $lastDeletedDatabase;
	}
	
	public $lastColumn = ""; // remember the last used/worked with column
	public function getlastColumn() {
		return $this->lastColumn;
	}
	
	public function setlastColumn($lastColumn) {
		$this->lastColumn = $lastColumn;
	}
	
	public $lastTable = ""; // remember the last used/worked with table
	public function getlastTable() {
		return $this->lastTable;
	}
	
	public function setlastTable($lastTable) {
		$this->lastTable = $lastTable;
	}

	/* ================= INIT ================
	creates the root folder where all databases reside in */
	public function __construct($absolute_path_to_database_root_folder) {

		if(isset($absolute_path_to_database_root_folder))
		{
			if(!empty($absolute_path_to_database_root_folder))
			{
				$this->absolute_path_to_database_root_folder = $absolute_path_to_database_root_folder;
			}
		}
		// if the database_root_folder does not exist create it now
		if(!is_dir($this->absolute_path_to_database_root_folder))
		{
			if(mkdir($this->absolute_path_to_database_root_folder,$this->accessRights))
			{
				// success
				// chmod($this->absolute_path_to_database_root_folder,$this->accessRights);
			}
			else
			{
				$this->error("could not create folder \"databases\".");
			}
		}
	}
	
	/* ================= DATABASE MANAGEMENT ================ */
	
	/* database management commands
	addDatabase(dbname); create a folder inside folder database with the name $dbName */
	public function addDatabase($dbName)
	{
		$this->worked = false;
		if(empty($dbName))
		{
			$this->error("no databaseName given.");
		}
		else
		{
			$path = $this->absolute_path_to_database_root_folder.$this->slash.$dbName;
			if(!is_dir($dbName))
			{
				if(mkdir($path,$this->accessRights))
				{
					chmod($path,$this->accessRights);
					$this->lastDatabase = $dbName;
					$this->worked = true;
					$this->operation("database ".$dbName." with accessrights ".$this->accessRights." added.");
				}
				else
				{
					$this->error("could not create folder/database: \"".$dbName."\"");
				}
			}
			else
			{
				$this->error("can not create ".$path." the directory exists - not overwriting.");
			}
		}
	
		return $this->worked;
	}
	
	/* copy all files and folder from $dbNameSource to $dbNameDestination */
	public function copyDatabase($dbNameSource, $dbNameDestination)
	{
		$this->worked = false;
		$lastlastDatabase = $dbNameDestination;
	
		$dbNameSource = $this->absolute_path_to_database_root_folder.$this->slash.$dbNameSource;
		$dbNameDestination = $this->absolute_path_to_database_root_folder.$this->slash.$dbNameDestination;
		if(is_dir($dbNameSource))
		{
			if(!is_dir($dbNameDestination))
			{
				$this->recurse_copy($dbNameSource,$dbNameDestination);
				$this->lastTable = $lastlastDatabase;
				$this->worked = true;
			}
			else
			{
				$this->error("can not copy ".$dbNameDestination." to ".$dbNameSource." the directory ".$dbNameDestination." does not exists.");
			}
		}
		else
		{
			$this->error("can not copy ".$dbNameSource." the directory does not exists?");
		}
	
		$this->operation("copied database from ".$dbNameSource." to ".$dbNameDestination."");
	
		return $this->worked;
	}
	
	// renameDatabase(dboldname = "",dbnewname); // rename folder dboldname to dbnewname
	public function renameDatabase($dboldname,$dbnewname)
	{
		$this->worked = false;
		if(empty($dboldname)) $dboldname = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		$oldpath = $this->absolute_path_to_database_root_folder.$this->slash.$dboldname;
		$newpath = $this->absolute_path_to_database_root_folder.$this->slash.$dbnewname;
		if(is_dir($oldpath))
		{
			if(!is_dir($newpath))
			{
				rename($oldpath,$newpath);
				$this->lastTable = $dbnewname;
				$this->worked = true;
			}
			else
			{
				$this->error("can not rename ".$oldpath." to ".$newpath." the directory exists allready.");
			}
		}
		else
		{
			$this->error("can not rename ".$oldpath." the directory does not exists?");
		}
	
		$this->operation("renameDatabase \$dboldname ".$dboldname." \$dbnewname ".$dbnewname."");
	
		return $this->worked;
	}
	
	/* deleta a database: like that: delDatabase($dbName); - effectively delete a folder in your filesystem. */
	public function delDatabase($dbName_s)
	{
		$this->worked = false;
		// give error if(empty($dbName_s)) $dbName_s = $this->lastDatabase;

		if (is_array($dbName_s)) {
			$counter = count( $dbName_s );
			for($i = 0; $i < $counter; $i ++) {
				$dbName = $dbName_s[$i];
				$path = $this->absolute_path_to_database_root_folder.$this->slash.$dbName;
				$this->deleteFolder($path);
			}
		}
		else
		{
			$path = $this->absolute_path_to_database_root_folder.$this->slash.$dbName_s;
			$this->deleteFolder($path);
		}
		$this->lastDeletedDatabase = $path;
	
		return $this->worked;
	}
	
	private function deleteFolder($path)
	{
		if(empty($path))
		{
			echo 'f*** ... you just found a EasterHeck :) ';
		}
		if(is_dir($path))
		{
			$this->rmdir_recursive($path);
			$this->worked = true;
		}
		else
		{
			$this->error("can not delete ".$path." the directory does not exists.");
			$this->worked = false;
		}

		return $this->worked;
	}
	
	/* ================= TABLE MANAGEMENT ================ */
	
	/* addTable(dbname = "",tablename); // effectively create a new folder "tablename" inside the folder "dbname" */
	public function addTable($tableName,$dbName = "")
	{
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
	
		$path = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName;
		if(!is_dir($path))
		{
			if(mkdir($path,$this->accessRights))
			{
				// success
				chmod($path,$this->accessRights);
			}
			else
			{
				$this->error("could not create folder(table): \"".$path."\"");
			}
				
			$this->lastTable = $tableName;
			$this->lastDatabase = $dbName;
			$this->worked = true;
		}
		else
		{
			$this->error("can not create table-directory ".$path." the directory exists allready.");
		}
	
		$this->operation("addTable ".$tableName." to database ".$dbName." with accessRights ".$this->accessRights);
	
		return $this->worked;
	}
	
	/* copy all files and folder from $dbNameSource to $dbNameDestination */
	public function copyTable($tableNameSource, $tableNameDestination, $dbName = "")
	{
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($columnName)) $columnName = $this->lastColumn;
		
		if(empty($tableNameSource))
		{
			$this->error("no table to copy given (\$tableNameSource is empty)");
		}
		else if(empty($tableNameDestination))
		{
			$this->error("no DestinationTable given, don't know where to copy the table. (\$tableNameDestination is empty)");
		}
		else
		{
			$pathsourceource = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableNameSource;
			$pathdestination = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableNameDestination;
			
			if(is_dir($pathsourceource))
			{
				if(!is_dir($pathdestination))
				{
					$this->recurse_copy($pathsourceource,$pathdestination);
					$this->lastTable = $tableNameDestination;
					$this->lastDatabase = $dbName;
					$this->worked = true;
				}
				else
				{
					$this->error("can not copy ".$pathsourceource.", destination ".$pathdestination." does not exists.");
				}
			}
			else
			{
				$this->error("can not copy directory ".$pathsourceource.", it does not exists?");
			}
		
			$this->operation("copyTable ".$dbName."->".$tableNameSource." to ".$dbName."->".$tableNameDestination);
		}
			
		return $this->worked;
	}
	
	// renameTable($dbName = "",$tableoldname = "",$tablenewname); // rename table
	public function renameTable($tableoldname,$tablenewname,$dbName = "")
	{
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableoldname)) $tableoldname = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		$oldpath = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableoldname;
		$newpath = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tablenewname;
		if(is_dir($oldpath))
		{
			if(!is_dir($newpath))
			{
				rename($oldpath,$newpath);
				$this->lastTable = $tablenewname;
				$this->worked = true;
			}
			else
			{
				$this->error("can not rename ".$oldpath." to ".$newpath." the directory exists allready.");
			}
		}
		else
		{
			$this->error("can not rename ".$oldpath." does not exists?");
		}
	
		$this->operation("renameTable ".$dbName."->".$tableoldname." to ".$dbName."->".$tablenewname);
	
		return $this->worked;
	}
	
	// delTable($dbName = "",$tableName); // effectively create a new folder "tablename" inside the folder "dbname"
	public function delTable($tableName,$dbName = "")
	{
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;

		$path = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName;
		if($this->deleteFolder($path))
		{
			$this->worked = true;
		}
		else
		{
			$this->error("can not delete ".$path." the directory does not exists.");
		}
	
		$this->operation("delTable ".$dbName."->".$tableName);
	
		return $this->worked;
	}
	
	/* ================= COLUMN MANAGEMENT ================ */
	
	/* addColumn($dbName = "",$tableName = "",$columnName);
	 * effectively creates a file called "columname" with the content <?php /* *\/ ?/> inside tablename
	*
	* if there are allready column-files inside the directory -> fill up all columns with as many lines
	* (empty) as the others "synchronizing" them in terms of line-count
	*/
	public function addColumn($columnName,$tableName = "",$dbName = "")
	{
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		$pathtable = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName;
		$path = $pathtable.$this->slash.$columnName.".php";

		if(!is_file($path)) // does the file allready exist?
		{
			// are there other files in this dir
			$files = $this->ls($pathtable);
			$fileCount = count($files);
	
			$first_columnFile = "";
			if($fileCount > 0)
			{
				// get amount of lines of first file
				$fileCount = count($files);
				for($i = 0;$i < $fileCount;$i++)
				{
					$currentFile = $files[$i];
					if(!(($currentFile == ".") || ($currentFile == "..")))
					{
						$first_columnFile = $currentFile;
						break;
					}
				}
			}
	
			$LineTarget = 0;
			if(!empty($first_columnFile))
			{
				$lines = file($pathtable.$this->slash.$first_columnFile);
				$LineTarget = count($lines);
			}
	
			touch($path,time()); // create file
			chmod($path, $this->accessRights); // set access rights
			file_put_contents($path, "<?php /* \n", FILE_APPEND); // fill with initial <?php /* invisible content

			if($LineTarget > 1) // fill up this column with as many lines (empty) as the others "synchronizing" them in terms of line-count 
			{
				for($i = 1;$i < $LineTarget;$i++)
				{
					file_put_contents($path, "\n", FILE_APPEND);
				}
			}

			$this->lastColumn = $columnName;
			$this->lastTable = $tableName;
			$this->lastDatabase = $dbName;
			$this->worked = true;
		}
		else
		{
			$this->error("can not create file ".$path." the file allready exists?");
		}
	
		$this->operation("addColumn ".$dbName."->".$tableName."->".$columnName." with accessRights ".$this->accessRights);
	
		return $this->worked;
	}
	
	// renameColumn($dbName = "",$tableName = "",$columnoldname = "",$columnnewname); // rename column-file
	public function renameColumn($columnoldname,$columnnewname = "",$tableName = "",$dbName = "")
	{
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		$oldpath = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName.$this->slash.$columnoldname.".php";
		$newpath = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName.$this->slash.$columnnewname.".php";
		if(is_file($oldpath))
		{
			if(!is_file($newpath))
			{
				rename($oldpath,$newpath);
				$this->lastColumn = $columnnewname;
				$this->worked = true;
			}
			else
			{
				$this->error("can not rename ".$oldpath." to ".$newpath." the file exists allready?");
			}
		}
		else
		{
			$this->error("can not rename ".$oldpath." the file does not exists?");
		}
	
		$this->operation("renameColumn ".$dbName."->".$tableName."->".$columnoldname." to ".$columnnewname);
	
		return $this->worked;
	}
	
	/* delColumn($dbName = "",$tableName); effectively delete file called $tableName */
	public function delColumn($columnName,$tableName = "",$dbName = "")
	{
		$this->worked = false;
		if(empty($columnName)) $columnName = $this->lastColumn;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		$path = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName.$this->slash.$columnName.".php";
		if(is_file($path))
		{
			unlink($path);
			$this->lastColumn = $columnName;
			$this->worked = true;
		}
		else
		{
			$this->error("can not delete ".$path." the file does not exists.");
		}
	
		$this->operation("delColumn ".$dbName."->".$tableName."->".$columnName);
	
		return $this->worked;
	}
	
	/* ================= RECORD OPERATIONS ================ */
	// database content changing commands

	/*
	 * add($index,$columnName_values,$tableName,$dbName) // adds a new line at pos $index
	 * $columnName_values has the format key:value,
	 * example:
	 * $nodbObj->add("name:jimy;street:street;phone:+00981232112312;mail:mail@mail.com;","tableName","databaseName");
	 * 
	 * if no tableName or databaseName is given, the last in use will be used
	*/
	public function add($columnName_values,$tableName = "",$dbName = "")
	{
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		$pathtable = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName;
		if(is_dir($pathtable))
		{
			// get a list of all files in the table-directory
			$files = $this->ls($pathtable);
	
			// iterate over key:values and make it accessible
			$columns = explode(";",$columnName_values);
			$columns = array_filter( $columns, 'strlen' );
	
			// iterate over files, compare filename to columnname, then insert value if available, else insert empty line
			$fileCount = count($files);
			for ($i = 0; $i < $fileCount; $i++) {
				$currentFile = $files[$i];
				if(!(($currentFile == ".") || ($currentFile == "..")))
				{
					$filename_without_ending = substr($currentFile, 0, -4); // strip away .php
	
					// iterate over $columns and check if such columnname:value exists
					$columnsCount = count($columns);
					$found = false;
					for ($j = 0; $j < $columnsCount; $j++) {
						$key_value = explode(":",$columns[$j]);
						$key = $key_value[0];
						$value = $key_value[1];
						$path = $pathtable.$this->slash.$filename_without_ending.".php";
						if($filename_without_ending == $key)
						{
							$found = true;
							break;
						}
					}
					if($found)
					{
						// if column name found in $columnName_values and as a file, insert line with linebreak
						file_put_contents($path, $value."\n", FILE_APPEND);
	
						$this->lastDatabase = $dbName;
						$this->lastTable = $tableName;
						$this->lastColumn = $key;
						$this->worked = true;
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
			$this->error("can not add to column: ".$columnName_values." the path: ".$pathtable." does not exist?");
		}
	
		$this->operation("added record ".$columnName_values." to ".$dbName."->".$tableName."->".$columnName_values);
	
		return $this->worked;
	}
	
	/* insert($index,$columnName_values,$tableName,$dbName) // inserts a new line at pos $index
	 $columnName_values has the format key:value,
	example:
	name:tom;age:32;message:so and so;
	
	the minimum index(lineNumber) is 1, because at index(lineNumber) 0 there is the <?php /* hide content line
	*/
	public function insert($index,$columnName_values,$tableName = "",$dbName = "")
	{
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		if($index > 0) // the minimum index(lineNumber) is 1, because at index(lineNumber) 0 there is the <?php /* hide content line
		{
			$pathtable = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName;
			if(is_dir($pathtable))
			{
				// get a list of all files in the table-directory
				$files = $this->ls($pathtable);
		
				// iterate over key:values and make it accessible
				$columns = explode(";",$columnName_values);
				$columns = array_filter( $columns, 'strlen' );
		
				// iterate over files, compare filename to columnname, then insert value if available, else insert empty line
				$fileCount = count($files);
				for ($i = 0; $i < $fileCount; $i++) {
					$currentFile = $files[$i];
					if(!(($currentFile == ".") || ($currentFile == "..")))
					{
						$filename_without_ending = substr($currentFile, 0, -4); // strip away .php
		
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
								$path = $pathtable.$this->slash.$key.".php";
								break;
							}
						}
						if($found)
						{
							// if column name found in $columnName_values and as a file, insert line with linebreak
							if($this->insertLineAt($index,$value."\n",$path))
							{
								$this->lastDatabase = $dbName;
								$this->lastTable = $tableName;
								$this->lastColumn = $key;
								$this->worked = true;
							}
						}
						else
						{
							// if not, insert a empty line with linebreak
							$this->insertLineAt($index,"\n",$path);
						}
					}
				}
			}
			else
			{
				$this->error("can not insert into ".$pathtable." - does this path exist?");
			}
		}
		else 
		{
			$this->error("can not insert at index(lineNumber) 0, index needs to be 1 or larger");
		}
		$this->operation("insert ".$dbName."->".$tableName."->".$tableName." at line ".$this->array2string($index)." this data ".$columnName_values);
		return $this->worked;
	}
	
	/* function update($index,$columnName_values,$tableName = "",$dbName = "")
	
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
	public function update($index,$columnName_values,$tableName = "",$dbName = "")
	{
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		if($index > 0) // the minimum index(lineNumber) is 1, because at index(lineNumber) 0 there is the <?php /* hide content line
		{
			if(is_int($index)||is_array($index))
			{
				$pathtable = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName;
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
						$path_to_file = $pathtable.$this->slash.$filename_with_ending;
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
								for($i = 0; $i < $lines2ChangeCount; $i++)
								{
									$changeThisLine = $index[$i];
									$lines[$changeThisLine] = $value."\n"; // make changes // array_splice($lines,$lineNumber,1, array($line)); // 0 = insert between elements 1 = replace element
								}
							}
							$lines = array_values($lines); // reindex array
							file_put_contents($path_to_file,implode($lines));	// write back to file
								
							$this->lastDatabase = $dbName;
							$this->lastTable = $tableName;
							$this->lastColumn = $key;
							$this->worked = true;
						}
						else
						{
							$this->error("function update(): can not change column-file ".$path_to_file." of table ".$pathtable." does it exist?");
						}
					}
				}
				else
				{
					$this->error("function update(): can not change column-file in directory ".$pathtable." does it exist?");
				}
			}
			else
			{
				$this->error("function update(): there is somehting wrong with the \$index=".$this->array2string($index)." given.");
			}
		}
		else
		{
			$this->error("can not modify index(lineNumber) 0, index needs to be 1 or larger");
		}
	
		$this->operation("change ".$dbName."->".$tableName."->".$tableName." at line ".$this->array2string($index)." to this data ".$columnName_values);
	
		return $this->worked;
	}
	
	/*
	 * deletes a record(s) with $index in $tableName
	*/
	public function delete($index,$tableName = "",$dbName = "")
	{
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		$path2table = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName;
	
		if($index > 0) // the minimum index(lineNumber) is 1, because at index(lineNumber) 0 there is the <?php /* hide content line
		{
			if(is_int($index)||is_array($index)||is_string($index))
			{
				// get all files of the given table
				$files = $this->ls($path2table);
		
				$fileCount = count($files);
				// iterate over all files in table-directory
				for($i = 0;$i < $fileCount;$i++)
				{
					$currentFile = $files[$i];
					if(!(($currentFile == ".") || ($currentFile == "..")))
					{
						$path2column = $path2table.$this->slash.$currentFile;
						if(is_file($path2column))
						{
							$lines = file($path2column); // read lines
							if(is_int($index))
							{
								array_splice($lines,$index,1); // at $index replace 1 element with nothing
								$lines = array_values($lines); // reindex array
								file_put_contents($path2column,implode($lines));	// write back to file
		
								$this->lastDatabase = $dbName;
								$this->lastTable = $tableName;
								$this->lastColumn = $columnName;
								$this->worked = true;
							}
							else if(is_array($index)||is_string($index))
							{
								if(is_array($index))
								{
									$start = 0;
									$stop = count($index);
								}
								else if(is_string($index))
								{
									$index_start_stop = split("-",$index);
									$start = $index_start_stop[0];
									$stop = $index_start_stop[1];
									$stop++; // giving "0-2" the following loop would delete line 0,1 but not line 2
								}
		
								// iterate over indices
								for($j = $start;$j < $stop;$j++)
								{
									$lines[$j] = ""; // change specified elements to null
								}
		
								$lines = array_filter( $lines, 'strlen' ); // delete all elements with null
								$lines = array_values($lines); // reindex array
								file_put_contents($path2column,implode($lines)); // write back to file
	
								$this->lastDatabase = $dbName;
								$this->lastTable = $tableName;
								$this->lastColumn = $columnName;
								$this->worked = true;
							}
						}
						else
						{
							$this->error("function delete(): can not delete records in file-column ".$path2column." does it exist?");
						}
					}
				}
			}
			else
			{
				$this->error("function delete(): can not delete record, there is something wrong with the \$index:".$this->array2string($index)." given");
			}
		}
		else
		{
			$this->error("can not delete index(lineNumber) 0, index needs to be 1 or larger");
		}
						
		$this->operation("delete \$index ".$this->array2string($index)." \$tableName ".$tableName." \$dbName ".$dbName);
	
		return $this->worked;
	}
	
	/* ================= READ ================ */
	
	/* get one single record from table
	 // get mutliple records from a table read(array(0,1,2),$tableName,$dbName)
	// 1. get a range of records "from a table read("0-3")"
	// 2. get all records with name "jim" read($this->$this->where("jim"));
	*/
	public function read($index,$tableName = "",$dbName = "")
	{
		$result = array();
	
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		$path_to_table = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName;
	
		$files = $this->ls($path_to_table);
		$fileCount = count($files);

		// 0. get one record from a table read(0,$tableName,$dbName)
		if(is_int($index))
		{
			if($index > 0) // the minimum index(lineNumber) is 1, because at index(lineNumber) 0 there is the <?php /* hide content line
			{
				for($i = 1;$i < $fileCount;$i++)
				{
					$currentFile = $files[$i];
					if(!(($currentFile == ".") || ($currentFile == "..")))
					{
						$lines = file($path_to_table.$this->slash.$currentFile);
						$currentFile = substr($currentFile, 0, -4); // strip away .php
						$lines[$index] = str_replace(array("\r\n", "\r", "\n"), "", $lines[$index]); // remove linebreaks
						$result[$currentFile] = $lines[$index];
					}
				}
			}
			else
			{
				$this->error("can not read index(lineNumber) 0, index needs to be 1 or larger");
			}
		}
		// 1. get mutliple records from a table read(array(0,1,2),$tableName,$dbName)
		// 3. get all records where name == 'jim' read($this->$this->where('jim'));"
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
					$lines = file($path_to_table.$this->slash.$currentFile);
					if(!(($currentFile == ".") || ($currentFile == "..")))
					{
						$key = substr($currentFile, 0, -4); // strip away .php
						$value = str_replace(array("\r\n", "\r", "\n"), "", $lines[$currentIndex]); // remove linebreaks
						$subArray[$key] = $value;
					}
				}
				$result[] = $subArray;
				$this->worked = true;
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
					$lines = file($path_to_table.$this->slash.$currentFile);
					if(!(($currentFile == ".") || ($currentFile == "..")))
					{
						$key = substr($currentFile, 0, -4); // strip away .php
						$value = str_replace(array("\r\n", "\r", "\n"), "", $lines[$currentIndex]); // remove linebreaks
						$subArray[$key] = $value;
					}
				}
				$result[] = $subArray;
				$this->worked = true;
			}
		}
	
		if(!empty($result)) $this->worked = true;
	
		$this->operation("read line ".$this->array2string($index)." of ".$dbName."->".$tableName);
	
		return $result;
	}
	
	/* get all records from a table, every file represents a column */
	public function readTable($tableName = "",$dbName = "")
	{
		$result = array();
	
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		$path_to_table = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName;
	
		$files = $this->ls($path_to_table);
		$fileCount = count($files);
		$allFilesInFolder = array();
	
		if(is_dir($path_to_table))
		{
			for($i = 0;$i < $fileCount;$i++)
			{
				$currentFile = $files[$i];
				if(!(($currentFile == ".") || ($currentFile == "..")))
				{
					$key = substr($currentFile, 0, -4); // strip away .php (ending)
					$allFilesInFolder[$key] = file($path_to_table.$this->slash.$currentFile);
				}
			}
		}
	
		reset($allFilesInFolder);
		$first_key = key($allFilesInFolder);
		$lines = $allFilesInFolder[$first_key];
		$linesCount = count($lines);
	
		// iterate over indices
		for($lineNumber = 1;$lineNumber < $linesCount;$lineNumber++)
		{
			$currentIndex = $lineNumber;
			$subArray = array();
	
			// iterate over files/columns
			$line = array();
			foreach ($allFilesInFolder as $key => $lines)
			{
				$value = str_replace(array("\r\n", "\r", "\n"), "", $lines[$lineNumber]); // remove linebreaks
				$line[$key] = $value;
			}
			$result[] = $line;
			$this->worked = true;
		}
	
		$this->operation("readTable ".$dbName."->".$tableName);
	
		return $result;
	}
	/* get whole database as a object-array with sub arrays */
	public function readDatabase($dbName = "")
	{
		$result = array();
	
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		$path_to_database = $this->absolute_path_to_database_root_folder.$this->slash.$dbName;
	
		$tableDirs = $this->ls($path_to_database);
		$tableDirCount = count($tableDirs);
	
		for($i = 0;$i < $tableDirCount;$i++)
		{
			$current_tableDir = $tableDirs[$i];
			if(!(($current_tableDir == ".") || ($current_tableDir == "..")))
			{
				$path = $path_to_database.$this->slash.$current_tableDir;
				if(is_dir($path))
				{
					$result[$current_tableDir] = $this->readTable($current_tableDir,$dbName);
					$this->worked = true;
				}
				else
				{
					$this->error("can access ".$path." does it exist?");
				}
			}
		}
	
		$this->operation("readDatabase ".$dbName);
	
		return $result;
	}
	/* ================= SEARCH ================ */
	
	/* search all columns, return array of format:
	 * $result[column1] = {1,2,3};
	* $result[column2] = {2,3,4};
	* */
	public function searchTable($searchFor,$tableName = "",$dbName = "")
	{
		$result = array();
	
		$this->operation("searchTable \$searchFor ".$searchFor." \$tableName ".$tableName." \$dbName ".$dbName);
	}
	/* search all columns, return array of format:
	 * $result[column1] = {1,2,3};
	* $result[column2] = {2,3,4};
	* */
	public function searchDatabase($searchFor,$dbName = "")
	{
		$result = array();
	
		$this->operation("searchDatabase \$searchFor ".$searchFor." \$dbName ".$dbName);
	}
	
	/* return all line numbers that contain the given string */
	public function where($searchFor,$columnName = "",$tableName = "",$dbName = "")
	{
		$result = array();
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		$path = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName.$this->slash.$columnName.".php";
		if(is_file($path))
		{
			$lines = file($path);
			$linesCount = count($lines);
			for($lineNumber = 0;$lineNumber < $linesCount;$lineNumber++)
			{
				if($lines[$lineNumber] == $searchFor."\n")
				{
					array_push($result,$lineNumber);
					$this->lastColumn = $columnName;
					$this->lastTable = $tableName;
					$this->lastDatabase = $dbName;
					$this->worked = true;
				}
			}
		}
		else
		{
			$this->error("can not search column-file ".$path." the file does not exist?");
		}
	
		$this->operation("where \$searchFor ".$searchFor." \$columnName ".$columnName." \$tableName ".$tableName." $dbName ".$dbName);
	
		return $result;
	}
	
	/* ================= IMPORT / EXPORT ================ */
	
	/* nothing really finished here */
	// import / export commands:
	
	// importMySQL($mysqldumb); // parses the mysqldumb and tries to create a file-based database
	
	// exportMySQL($dbName); // tries to create a MySQL-dumb of the file-based-database
	
	
	/* ================= LIBRARY of the LIBRARY ================
	 * don't call these functions directly unless you know what you do.
	* these functions are used by the above functons. */
	/* $lineNumber = 1; // lineNumber at which the content will be inserted */
	public function insertLineAt($lineNumber,$line,$path)
	{
		if($lineNumber > 0) // the minimum index(lineNumber) is 1, because at index(lineNumber) 0 there is the <?php /* hide content line
		{
			// read into array
			$lines = file($path);
			// add second line
			array_splice($lines,$lineNumber,0, array($line));
			// reindex array
			$array_reindexed = array_values($lines);
			// write back to file
			file_put_contents($path,implode($array_reindexed));
			return true;
		}
		else
		{
			$this->worked = false;
			$this->error("can not insert at index(lineNumber) 0 into ".$path." - this is not allowerd, because there is the <?php /* hiding content tag ");
			return false;
		}
	}

	/* list directory, return array of all file-names and directory-names except . and .. */
	public function ls($path) {
		$files = array();
		if ($handle = opendir ( $path )) {
			while ( false !== ($file = readdir ( $handle )) ) {
				if ($file != "." && $file != "..") {
					$files[] = $file;
				}
			}
			closedir ( $handle );
		}
		
		return $files;
	}
	
	/* returns strange stuff */
	public function ls_failed($path,$sort = SORT_REGULAR)
	{
		$files = array();
		$current = getcwd();
		$files = array_diff(scandir($path), array('..', '.'));
	
		if($files[2] == 2)
		{
			echo "WTF?";
		}
		
		if($sort != 0)
		{
			sort($files,$sort);
		}
	
		return $files;
	}
	
	/* recursively copy files and folders */
	public function recurse_copy($src, $dst) {
		$dir = opendir ( $src );
		if($dir)
		{
			// success
		}
		else
		{
			$this->error("could not open folder: \"".$dir."\"");
		}
		
		if(@mkdir ( $dst ))
		{
			// success
			chmod($dst, $this->accessRights);
		}
		else
		{
			$this->error("could not create folder: \"".$dst."\"");
		}

		while ( false !== ($file = readdir ( $dir )) ) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir ( $src . '/' . $file )) {
					$this->recurse_copy ( $src . '/' . $file, $dst . '/' . $file );
				} else {
					copy ( $src . '/' . $file, $dst . '/' . $file );
				}
			}
		}
		closedir ( $dir );
	}
		
	/* remove a directory including content recursively */
	private function rmdir_recursive($dir)
	{
		foreach ( scandir ( $dir ) as $file )
		{
			if ('.' === $file || '..' === $file)
			{
				continue;
			}
			if (is_dir ( "$dir/$file" ))
			{
				$this->rmdir_recursive ( "$dir/$file" );
			}
			else
			{
				unlink ( "$dir/$file" );
			}
		}
		if (! rmdir ( $dir )) {
			$this->error ( "could not remove directory: " . $dir );
		}
	}
	
	/* also deletes non-empty directories
	public function deleteDirectory($dir) {
		if (!file_exists($dir)) {
			return true;
		}
	
		if (!is_dir($dir)) {
			return unlink($dir);
		}
	
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') {
				continue;
			}
	
			if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
				return false;
			}
	
		}
	
		return rmdir($dir);
	}
	*/

	/* outputs a warning and if $settings_log_errors == true, outputs to error.log */
	public function error($message)
	{
		$this->worked = false;
		if($this->warnings)
		{
			trigger_error("error: ".$message);
		}
		
		if($this->logging)
		{
			if(!empty($this->settings_log_errors)){
				$this->log2file($this->settings_log_errors,$message);
			}
		}
	}
	
	/* outputs a warning and if $settings_log_errors == true, outputs to error.log */
	public function operation($operation)
	{
		if($this->logging)
		{
			if(!empty($this->settings_log_operations)){
				$this->log2file($this->settings_log_operations,$operation);
			}
		}
	}
	
	/* write the error to a log file */
	public function log2file($file,$message)
	{
		file_put_contents($file, time().": ".$message."\n", FILE_APPEND);
	}

	public function __destruct()
	{
		// not in use
	}

	// allows to ouput the class-instance as a string
	public function __toString()
	{
		return print_r($this,true);
	}
	
	/* convert array2string, if it's no array, return original */
	public function array2string($array)
	{
		if(is_array($array))
		{
			return implode(",", $array);
		}
		else
		{
			return $array;
		}
	}
}
?>