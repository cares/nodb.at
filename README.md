# ==== nodb.at ====

file and directory based php database

is my own implementation of a file-based database for php. ASAP - as simple as possible

the ide is like this

database = folder

table = subfolder

column = file in subfolder ;)
so theoretically you could have a table inside a table or even work with filesystem links (linux ln -sv command)

## implemented commands:

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

o implement all the functions

o build a nodbmyadmin.php that like phpmyadmin let you easily modify the database.

o remember the last used database and table and make all following operations on them