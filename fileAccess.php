<?php
/* ============================== SUPPORT FUNCTIONS / LIBRARY */
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
// addLine("value",$columname,$tablename,$dbname); // adds a new line at the end of file columname.php
function addLine($value,$columname,$tablename,$dbname)
{
	global $absolute_path_to_database_root_folder;
	$path = $absolute_path_to_database_root_folder.$slash.$dbname.$slash.$tablename.$slash.$columnname.".php";
	if(!is_dir($path))
	{
		file_put_contents($path, $value, FILE_APPEND);
	}
	else
	{
		trigger_error("error: can add line to ".$path." the file does not exists?");
	}
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