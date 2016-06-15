<?php
//Platform independant separator
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

//Database constanten
define('DB_SERVER','localhost');
define('DB_USER','timeslots_user');
define('DB_PASS','parse');
define('DB_NAME','db_timeslots');

//XML file
define('X_TESTFILE', "https://www.resengo.com/Code/API/?APIClientID=1020139&Call=RN_LASTMINUTES&APISECRET=NLo4bkphb3dqUWGmE8VUcxqwQowwTlwcrsxFJqg7FpTdJSNWjR&RN_CompanyID=688451&RN_CompanyIDs=292493");
//XML TEST file
//define('X_TESTFILE','..'.DS.'public'.DS.'testXML.xml');

//Log file
define('LOG_FILE','..'.DS.'public'.DS.'log.txt');
?>