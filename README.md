# ==== nodb.at ====

is a (text)-file-and-folder based database with the concept of:

all files.php (representing the tables) and folders (representing databases)

    database = folder

    table = subfolder

    column = file in subfolder

    record = a line in column-file ;)

so theoretically you could have a table inside a table or even work with filesystem links (linux ln -sv command)

# ==== files ====

the first version was procedural

nodb.php		-> the database "engine"
nodb.test.php		-> automated test of engine

---> you should use the second version which is written in oop-class-objects style:

class.nodb.php		-> the database "engine"
class.nodb.test.php	-> automated test, use this as getting-started-example. 

class.nodb.bench.php	-> benchmark intended to be used on a webserver and run by a browser
class.nodb.bench.terminal.php -> same benchmark intended to be run on terminal (no 30seconds timeout like on webserver)

# === features: ===
+ smal database size (10000 records less than 500kb)
+ all files are plain-text and can be used/edited with any text-editing software (no cryptic format)
+ speed is ok 1000 records write, modify, read, delete 

+ remembers the last used database/table
+ ASAP simple folder/file based system
+ error and operation/process logging
+ tested quality

check out nodb.test.php for examples.

[https://github.com/developerATdwavesDOTde/nodb.at/blob/master/nodb.test.php]
(https://github.com/developerATdwavesDOTde/nodb.at/blob/master/nodb.test.php)

# ==== implemented commands ====

<pre>
// database management commands
createDatabase(dbname); // create a folder inside folder database that is called dbname

renameDatabase(dboldname,dbnewname); // rename folder dboldname to dbnewname

addTable(dbname,tablename); // effectively create a new folder "tablename" inside the folder "dbname"

addColumn(tablename,columname); // effectively creates a file called "columname" inside tablename

// database content changing commands
write("value",columname,tablename,dbname); // ads a new value at the end of file columname.php

change("newvalue",index,columname,"newvalue",tablename,dbname); // change value at index(linenumber) index to "newvalue" inside columname.php

read(index,columname,tablename,dbname); // returns the exact value

read(columname,tablename,dbname); // returns the content of the whole columname.php-file as array

read(tablename,dbname); // returns the whole table as a object-array

read(dbname); // returns the whole database as a object-array with sub arrays

read(index,columname,tablename,dbname); // delete entry

read(columname,tablename,dbname); // delete file columname.php

read(tablename,dbname); // delete folder tablename

read(dbname); // delete the directory dbname with all files !!! WARNING !!! ;)

</pre>


import / export commands:

<pre>
importMySQL(mysqldumb); // parses the mysqldumb and tries to create a file-based database

exportMySQL(dbname); // tries to create a MySQL-dumb of the file-based-database
</pre>

## ===== FORMAT =====

the format should be:
1. simple to read (and manually edit) with a texteditor
this is possible by a one-file-per-column-system. where simply all the values are separated by a linebreak.

example:

<pre>
/dbname/tablename/columname.php
</pre>

content:

<pre>

value1
value2
value3
value4
value5

</pre>


# ==== TODO ====

ok implement all the functions

ok build a nodbmyadmin.php that like phpmyadmin let you easily modify the database.

ok remember the last used database and table and make all following operations on them
