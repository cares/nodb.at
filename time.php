<?php
/* just to have some coherent timing functionality */
/* return time as human readable string between bench start and current time*/
function currentTime()
{
	$micro_date = microtime();

	global $start_timestamp;

	$date_array = explode(" ",$micro_date);
	$date = date("Y-m-d H:i:s",$date_array[1]);

	return $date." ". $date_array[0];
}

/* return time between bench start and current time in ms */
function currentTimeMS()
{
	global $start_timestamp;
	$stop_timestamp = getMicrotime(); // save start time

	$timeTaken = $stop_timestamp-$start_timestamp;

	return $timeTaken;
}

/* based on http://stackoverflow.com/questions/3656713/how-to-get-current-time-in-milliseconds-in-php */
function getMicrotime()
{
	return round(microtime(true) * 1000);
}
?>