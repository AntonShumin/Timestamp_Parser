<?php

//Database constanten
define('DB_SERVER','localhost');
define('DB_USER','timeslots_user');
define('DB_PASS','parse');
define('DB_NAME','db_timeslots');

//resengo
define('RN_CALL',"RN_LASTMINUTES");
define("RN_ID","292493");
define("RN_SECRET","NLo4bkphb3dqUWGmE8VUcxqwQowwTlwcrsxFJqg7FpTdJSNWjR");
/*vb url
https://www.resengo.com/Code/API/?
APIClientID=1020139&
Call=RN_LASTMINUTES&
APISECRET=NLo4bkphb3dqUWGmE8VUcxqwQowwTlwcrsxFJqg7FpTdJSNWjR&
RN_CompanyID=688451&
RN_CompanyIDs=292493
*/

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
//xml

//LOCAL for quick testing
//define('X_TESTFILE','..'.DS.'public'.DS.'testXML.xml');
//RESENGO
define('X_TESTFILE', "https://www.resengo.com/Code/API/?APIClientID=1020139&Call=RN_LASTMINUTES&APISECRET=NLo4bkphb3dqUWGmE8VUcxqwQowwTlwcrsxFJqg7FpTdJSNWjR&RN_CompanyID=688451&RN_CompanyIDs=292493");
?>