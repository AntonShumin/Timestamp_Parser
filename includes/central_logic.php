<?php
require_once('initialize.php');

//Checks
$check_mySQL = false;
$check_field_construct = false;
$check_xml_array = false;
$check_record_objects = false;

//Step 1 - mySQL connect
$database = new MySQLDatabase();
$check_mySQL = $database->construct_connection();

//Step 2 - prepare record.php
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

//Step 5 - generate mySQL query based on xml data
if($check_record_objects) {
    Record::build_sync_query(Record::$object_collection);
    
}

//Log Progress
MessageLogger::print_log();

?>