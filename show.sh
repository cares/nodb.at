while true; do (
echo "databases";ls -lah /var/www/nodb.at/databases/;
echo "databases/databaseBench";ls -lah /var/www/nodb.at/databases/databaseBench/;
echo "databases/databaseBench/tableBench1";ls -lah /var/www/nodb.at/databases/databaseBench/tableBench1/;
echo "content of Name.php: ";cat /var/www/nodb.at/databases/databaseBench/tableBench1/Name.php;
); sleep 1; done
