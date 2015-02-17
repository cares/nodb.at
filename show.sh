while true; do (
	echo "============== show.sh ==============";
	echo "show content of: columnTest1.php";
	cat databases/databaseTest1/testTable1/columnTest1.php;
	echo "show content of: ./databases/databaseTest1/testTable1:";ls  ./databases/databaseTest1/testTable1;
	echo "show content of: ./databases/databaseTest1/";ls  ./databases/databaseTest1;
	#echo "show folder databaseTest2:";ls  ./databases/databaseTest2;
	echo "show folder databases:";ls  /var/www/nodb.at/databases/;
	#echo "databases/databaseBench";ls  /var/www/nodb.at/databases/databaseBench/;
	#echo "databases/databaseBench/tableBench1";ls  /var/www/nodb.at/databases/databaseBench/tableBench1/;
	#echo "content of Name.php: ";cat /var/www/nodb.at/databases/databaseBench/tableBench1/Name.php;
	echo "count lines of mail.php name.php phone.php street.php: "; cd /var/www/nodb.at/databases/databaseTest1/testTable1; wc -l mail.php name.php phone.php street.php; cd /var/www/nodb.at;
); sleep 1; done
