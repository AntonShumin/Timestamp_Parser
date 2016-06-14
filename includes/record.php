<?php
//require_once('initialize.php');
require_once("database_object.php");
require_once("logger.php");

//DatabaseObject is verantwoordelijk voor mySQL queries
class Record extends DatabaseObject {
    
    //matches mySQL table name
    protected static $table_name="records";
    //field names (build dynamically from mySQL columns)
    protected static $db_fields = [];
    //stores a reference for all created objects
    public static $object_collection = [];
    //every sql table should have primary key = id
    public $id;
    //original mySQL values before formatting. associative
    public $mySQL_fields_values = [];
    //original xml values before formatting. associative
    public $xml_fields_values = [];
    //holds values to be updated/created in mySQL
    public $mismatch_fields_values = [];
    //if .xml data differs from mySQL it will be marked true for upload
    public $mark_for_update = false;
    
    
    //Synchronizing key between xml and mySQL
    protected static $sync_key;
    protected static $syn_date_start;
    protected static $sync_date_end;
    protected static $sync_deleted;
    
    
    //sync values are class specific, matching keys for xml and mysql
    public static function sync_vars() {
        self::$sync_key = self::$db_fields[1];
        self::$syn_date_start = self::$db_fields[3];
        self::$sync_date_end = self::$db_fields[4];
        self::$sync_deleted = self::$db_fields[10];
    }
    /*
    public function getArray() {
        return $xml_fields_values;
    }
    */
}    


?>