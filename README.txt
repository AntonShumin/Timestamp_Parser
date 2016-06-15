************
INSTALLATION: 

1. Create a new mySQL database (ex. db_timeslots)
2. Import records.sql table from this repo. (empty, contains column definitions)
3. Configure config.php (includes folder) for mySQL access
4. Run central_logic.php 
extra: change (uncomment) X_TESTFILE constant in config.php from the url to a local file path to rapidly test .xml changes 

*****
ABOUT

Timestamp_Parser is a raw php object oriented application that reads .xml files (Resengo) and makes a copy/updates to the local mySQL database. The result will output a detailed log file (browser or commandline). Written by Anton Shumin (june 2016, Belgium)



********************************
Version 1.1 created on 15/6/2015
********************************
- Write log to file. Log is now platform independent 
- Check old sql records for expired date

- Fixed query logic in case array is empty
- Fixed date compare logic

******************************
Version 1 created on 14/6/2016
******************************
-Dynamic browser log. 
-Read xml and sql
-Check xml for expired date
-Compare xml and sql mismatch
-Write relevant update


************
How it works

central_logic.php will loop through the following steps and only continue if the previous step was successful.

Step 1(mySQL connect) 
creates MySQLDatabase object (database.php) and makes mySQL connection. 

Step 2 (prepare record.php)
record.php dynamically constructs field names based on mySQL column names.  
(Wanted to try it in the name of scalability, removing redundant work in case .xml field definitions would change.) 
Column names in mySQL are designed to match .xml field names for synchronization. The application will check if sql columns and xml field names match. If not, log mismatch. 
Possible addition, construct mySQL table (if empty) on the fly based on .xml fields. (not included, would be a fancy feature though, able to fetch any other XML file)

Step 3(.xml read) 
xml_read.php will load the .xml file and construct an array with record values.
Warning: Simplexml only checks the first record for getName() definitions. Expects all other records to have identical name definitions.
Warning2: xml_reader.php, build_array() assumes that the parent field of each record is called RECORD. 

Step 4(populate objects) 
Create Record instances (record.php) for each <RECORD> in the .xml file.

Step 5(get SQL data) 
Create a single mySQL query from the collection of xml based objects to retrieve stored data. 
If returned result is not empty, match sql rows with xml fields for each object using predefined sync_key

I assume that querying sql database is the only scalable resource tasking process, which is why i only want to pull fields that are relevant to the xml file (which is loaded fully every time anyway) and request as little READ queries as possible. Needs testing with oversized requests. (currently runs with 100 records). Write queries are always individual tasks. To create/update 100 rows, the application will send 100 requests.

5-Extra:(date check) 
Check if the date for in the imported .xml records has expired, if so set deleted to true. 

Step 6:(Compare xml and sql)  
If .xml and sql mismatch, construct mismatch array for relevant objects and mark for update.

Step 7: (create/update mySQL)  
Create new sql rows or update relevant columns for existing rows.

Step 8: (update existing sql)
Update existing and expired SQL records

Step 9: (Log)
Output log and write to log.txt

Step 9: Conquer the world. 



