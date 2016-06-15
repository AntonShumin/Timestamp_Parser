************
INSTALLATION: 

1. Create a new mySQL database (ex. db_timeslots)
2. Import records.sql table from this repo. (empty, contains column definitions)
3. Configure config.php (includes folder) for mySQL access
4. Run central_logic.php 
extra: change (uncomment) X_TESTFILE constant in config.php from the url to a local file path to rapidly test .xml changes 

*****
ABOUT

Timestamp_Parser is a standalone php script that reads xml files and makes a copy/updates to the local mySQL database. The result will output a detailed log file (browser or commandline). Written by Anton Shumin (june 2016, Belgium)



********************************
Version 1.1 created on 15/6/2015
********************************
- Write log to file. Log is now platform independent 
- Check old sql records for expired date

******************************
Version 1 created on 14/6/2016
******************************
-Dynamic browser log. 
-Read xml and sql
-Check xml for expired date
-Compare xml and sql mismatch
-Write relevant update
