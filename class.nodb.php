<?php
/* nodb object oriented class style */
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

	public $default_accessRights = 0700; // the access rights, (chmod 0700) that folders and files will have per default when they are created and no access rights are specified
	public function getDefault_accessRights() {
		return $this->default_accessRights;
	}
	
	public function setDefault_accessRights($default_accessRights) {
		$this->default_accessRights = $default_accessRights;
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
			if(!is_long($accessRights)) $accessRights = $this->default_accessRights;
			mkdir($this->absolute_path_to_database_root_folder,$accessRights); // grant only current user access to this folder
		}
	}
	
	/* ================= DATABASE MANAGEMENT ================ */
	
	/* database management commands
	addDatabase(dbname); create a folder inside folder database with the name $dbName */
	public function addDatabase($dbName,$accessRights = "")
	{
		$this->worked = false;
		if(empty($accessRights)) $accessRights = $this->default_accessRights;
		if(empty($dbName))
		{
			$this->error("error: no databaseName given.");
		}
		else
		{
			$path = $this->absolute_path_to_database_root_folder.$this->slash.$dbName;
			if(!is_dir($dbName))
			{
				mkdir($path,$accessRights);
				$this->lastDatabase = $dbName;
				$this->worked = true;
				$this->operation("database ".$dbName." with accessrights ".$accessRights." added.");
			}
			else
			{
				$this->error("error: can not create ".$path." the directory exists - not overwriting.");
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
				$this->error("error: can not copy ".$dbNameDestination." to ".$dbNameSource." the directory ".$dbNameDestination." does not exists.");
			}
		}
		else
		{
			$this->error("error: can not copy ".$dbNameSource." the directory does not exists?");
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
				$this->error("error: can not rename ".$oldpath." to ".$newpath." the directory exists allready.");
			}
		}
		else
		{
			$this->error("error: can not rename ".$oldpath." the directory does not exists?");
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
			echo 'f***';
		}
		if(is_dir($path))
		{
			$this->rmdir_recursive($path);
			$this->worked = true;
		}
		else
		{
			$this->error("error: can not delete ".$path." the directory does not exists.");
			$this->worked = false;
		}
		
		return $this->worked;
	}
	
	/* ================= TABLE MANAGEMENT ================ */
	
	/* addTable(dbname = "",tablename); // effectively create a new folder "tablename" inside the folder "dbname" */
	public function addTable($tableName,$dbName = "",$accessRights = "")
	{
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($accessRights)) $accessRights = $this->default_accessRights;
	
		$path = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName;
		if(!is_dir($path))
		{
			mkdir($path,$accessRights);
			$this->lastTable = $tableName;
			$this->lastDatabase = $dbName;
			$this->worked = true;
		}
		else
		{
			$this->error("error: can not create table-directory ".$path." the directory exists allready.");
		}
	
		$this->operation("addTable ".$tableName." to database ".$dbName." with accessRights ".$accessRights);
	
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
			$this->error("error: no table to copy given (\$tableNameSource is empty)");
		}
		else if(empty($tableNameDestination))
		{
			$this->error("error: no DestinationTable given, don't know where to copy the table. (\$tableNameDestination is empty)");
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
					$this->error("error: can not copy ".$pathsourceource.", destination ".$pathdestination." does not exists.");
				}
			}
			else
			{
				$this->error("error: can not copy directory ".$pathsourceource.", it does not exists?");
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
				$this->error("error: can not rename ".$oldpath." to ".$newpath." the directory exists allready.");
			}
		}
		else
		{
			$this->error("error: can not rename ".$oldpath." does not exists?");
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
			$this->error("error: can not delete ".$path." the directory does not exists.");
		}
	
		$this->operation("delTable ".$dbName."->".$tableName);
	
		return $this->worked;
	}
	
	/* ================= COLUMN MANAGEMENT ================ */
	
	/* addColumn($dbName = "",$tableName = "",$columnName);
	 * effectively creates a file called "columname" inside tablename
	*
	* if there are allready columns inside the directory.
	* fill up this column with as many lines (empty) as the others "synchronizing" them in terms of line-count */
	public function addColumn($columnName,$tableName = "",$dbName = "",$accessRights = "")
	{
		$this->worked = false;
		if(empty($accessRights)) $accessRights = $this->default_accessRights;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		$pathtable = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName;
		$path = $pathtable.$this->slash.$columnName.".php";
		if(!is_file($path))
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
	
			$lineCount = 0;
			if(!empty($first_columnFile))
			{
				$lines = file($pathtable.$this->slash.$first_columnFile);
				$lineCount = count($lines);
			}
	
			touch($path,time());
			chmod($path, $accessRights);
	
			if($lineCount > 0)
			{
				for($i = 0;$i < $lineCount;$i++)
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
			$this->error("error: can not create file ".$path." the file allready exists?");
		}
	
		$this->operation("addColumn ".$dbName."->".$tableName."->".$columnName." with accessRights ".$accessRights);
	
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
				$this->error("error: can not rename ".$oldpath." to ".$newpath." the file exists allready?");
			}
		}
		else
		{
			$this->error("error: can not rename ".$oldpath." the file does not exists?");
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
			$this->error("error: can not delete ".$path." the file does not exists.");
		}
	
		$this->operation("delColumn ".$dbName."->".$tableName."->".$columnName);
	
		return $this->worked;
	}
	
	/* ================= RECORD OPERATIONS ================ */
	
	// database content changing commands
	/* add($index,$columnName_values,$tableName,$dbName) // adds a new line at pos $index
	 $columnName_values has the format key:value,
	example:
	name:tom;age:32;message:so and so;
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
			$this->error("error: can not add to table ".$pathtable." the path does not exist?");
		}
	
		$this->operation("added record ".$columnName_values." to ".$dbName."->".$tableName."->".$columnName_values);
	
		return $this->worked;
	}
	
	/* insert($index,$columnName_values,$tableName,$dbName) // inserts a new line at pos $index
	 $columnName_values has the format key:value,
	example:
	name:tom;age:32;message:so and so;
	*/
	public function insert($index,$columnName_values,$tableName = "",$dbName = "")
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
						$this->insertLineAt($index,$value."\n",$path);
	
						$this->lastDatabase = $dbName;
						$this->lastTable = $tableName;
						$this->lastColumn = $key;
						$this->worked = true;
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
			$this->error("error: can not add to table ".$pathtable." the path does not exist?");
		}
	
		$this->operation("insert ".$dbName."->".$tableName."->".$tableName." at line ".$this->array2string($index)." this data ".$columnName_values);
	
		return $this->worked;
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
	public function change($index,$columnName_values,$tableName = "",$dbName = "")
	{
		$this->worked = false;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
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
						$this->error("function change(): can not change column-file ".$path_to_file." of table ".$pathtable." does it exist?");
					}
				}
			}
			else
			{
				$this->error("function change(): can not change column-file in directory ".$pathtable." does it exist?");
			}
		}
		else
		{
			$this->error("function change(): there is somehting wrong with the \$index=".$this->array2string($index)." given.");
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
		if(empty($accessRights)) $accessRights = $this->default_accessRights;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		$path_to_table = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName;
	
		$files = $this->ls($path_to_table);
		$fileCount = count($files);
	
		// 0. get one record from a table read(0,$tableName,$dbName)
		if(is_int($index))
		{
			for($i = 0;$i < $fileCount;$i++)
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
	
	/* get all records from a table, top-array-keys represent the columns */
	public function readTable($tableName = "",$dbName = "")
	{
		$result = array();
	
		$this->worked = false;
		if(empty($accessRights)) $accessRights = $this->default_accessRights;
		if(empty($dbName)) $dbName = $this->lastDatabase;
		if(empty($tableName)) $tableName = $this->lastTable;
		if(empty($columnName)) $columnName = $this->lastColumn;
	
		$path_to_table = $this->absolute_path_to_database_root_folder.$this->slash.$dbName.$this->slash.$tableName;
	
		$files = $this->ls($path_to_table);
		$fileCount = count($files);
		$allFiles = array();
	
		if(is_dir($path_to_table))
		{
			for($i = 0;$i < $fileCount;$i++)
			{
				$currentFile = $files[$i];
				if(!(($currentFile == ".") || ($currentFile == "..")))
				{
					$key = substr($currentFile, 0, -4); // strip away .php
					$allFiles[$key] = file($path_to_table.$this->slash.$currentFile);
				}
			}
		}
	
		reset($allFiles);
		$first_key = key($allFiles);
		$lines = $allFiles[$first_key];
		$linesCount = count($lines);
	
		// iterate over indices
		for($j = 0;$j < $linesCount;$j++)
		{
			$currentIndex = $j;
			$subArray = array();
	
			// iterate over files/columns
			$line = array();
			foreach ($allFiles as $key => $lines)
			{
				$value = str_replace(array("\r\n", "\r", "\n"), "", $lines[$j]); // remove linebreaks
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
		if(empty($accessRights)) $accessRights = $this->default_accessRights;
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
					$this->error("error: can access ".$path." does it exist?");
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
			$this->error("error: can not search column-file ".$path." the file does not exist?");
		}
	
		$this->operation("where \$searchFor ".$searchFor." \$columnName ".$columnName." \$tableName ".$tableName." $dbName ".$dbName);
	
		return $result;
	}
	
	/* ================= IMPORT / EXPORT ================ */
	
	/* nothing really finished here */
	// import / export commands:
	
	// importMySQL($mysqldumb); // parses the mysqldumb and tries to create a file-based database
	
	// exportMySQL($dbName); // tries to create a MySQL-dumb of the file-based-database
	
	
	/* ================= LIBRARY ================
	 * don't call these functions directly unless you know what you do.
	* these functions are used by the above functons. */
	/* $lineNumber = 1; // lineNumber at which the content will be inserted */
	public function insertLineAt($lineNumber,$line,$path)
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
	public function ls($path,$sort = SORT_REGULAR)
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
	public function recurse_copy($src, $dst) {
		$dir = opendir ( $src );
		@mkdir ( $dst );
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
			$this->error ( "error: could not remove directory: " . $dir );
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
			trigger_error($message);
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