<?php
//phpinfo();
require_once('initialize.php');

//Error check. This (if) structure was chosen for readability and modularity
$check_mySQL = false;
$check_field_construct = false;
$check_xml_array = false;
$check_record_objects = false;
$check_sync_job = false;
$check_mismatch = false;

//Step 1 - mySQL connect
$database = new MySQLDatabase();
$check_mySQL = $database->construct_connection();

//Step 2 - prepare Record class
if($check_mySQL) {
    $check_field_construct = Record::conStruct_fields();
}

//Step 3 - read .xml
if($check_field_construct) {
    $xml_reader = new XMLRead();
    $check_xml_array = $xml_reader->readXML(X_TESTFILE);
}

//Step 4 - populate record objects
if($check_xml_array) {
    $check_record_objects = Record::construct_objects($check_xml_array);
}

//Step 5 - populate objects with xml and sql data, match by sync_key
if($check_record_objects) {
    $check_sync_job = Record::xml_sync_job();
}

//Step 5-EXTRA - loop through all objects and check if date is expired
if($check_sync_job) {
    Record::date_checker();
}

//Step 6 - compare sql and xml individual data values
if($check_sync_job){
    $check_mismatch = Record::build_mismatch();
}

//Step 7 - create/update to sql
if($check_mismatch){
    Record::upload_sql_records();
}

//Log Progress
MessageLogger::print_log();


//Page construction time
$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
echo "Page constructed in $time seconds".PHP_EOL;
?>