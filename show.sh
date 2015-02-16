while true; do (
	echo "============== show.sh ==============";
	echo "show content of: columnTest1.php";
	cat databases/databaseTest1/testTable1/columnTest1.php;
	#echo "show folder databaseTest2:";ls -lah ./databases/databaseTest2;
	echo "show folder databases:";ls -lah /var/www/nodb.at/databases/;
	#echo "databases/databaseBench";ls -lah /var/www/nodb.at/databases/databaseBench/;
	#echo "databases/databaseBench/tableBench1";ls -lah /var/www/nodb.at/databases/databaseBench/tableBench1/;
	#echo "content of Name.php: ";cat /var/www/nodb.at/databases/databaseBench/tableBench1/Name.php;
); sleep 1; done
