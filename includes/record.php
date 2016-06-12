<?php
//require_once('initialize.php');
require_once("database_object.php");
require_once("logger.php");

//DatabaseObject is verantwoordelijk voor mySQL queries
class Record extends DatabaseObject {
    
    //mySQL
    protected static $table_name="records";
    //field names are build dynamically from mySQL columns
    protected static $db_fields = [];
    //original mySQL values before formatting. associative
    public $mySQL_fields_values = [];
    //original xml values before formatting. associative
    public $xml_fields_values = [];
    //stores a reference for all created objects
    public static $object_collection = [];
    
    
    //Synchronizing key between xml and mySQL
    protected static $sync_key;
    protected static $syn_date_start;
    protected static $sync_date_end;
    protected static $sync_deleted;
    
    
    //Build db_fields names from the mySQL columns. Can be moved to the parent object
    public static function construct_fields() {
        global $database;
        $result_array = self::get_columns();
        foreach($result_array as $key => $value) :
            array_push(self::$db_fields,$value[0]);
        endforeach;
        if(self::$db_fields) {
            MessageLogger::add_log("Construct field definitions for <b>" . self::$table_name . "</b> class from mySQL: (".count(self::$db_fields).") " . join(", ", self::$db_fields) );
            self::sync_vars(); //store field names that are used to synchronize xml and mySQL
            return true;
        } else {
            MessageLogger::add_log("Construction of Record class field names from mySQL columns failed");
            return false;
        }
    }
    
    //Construct record objects from xml array. Can be moved to the parent object
    public static function construct_objects($array) {
        //populate with objects
        foreach ($array as $record) {
            $new_object = new self;
            $new_object->xml_fields_values = $record;
            self::$object_collection[] = &$new_object;
        }
        //return result
        $b_column_match = self::check_column_match(); //check if xml fields match mySQL columns, first object
         if(self::$object_collection && $b_column_match){
             MessageLogger::add_log("Record objects construction successful ".count(self::$object_collection)." objects created");
             return true;
         } else {
             MessageLogger::add_log("Record object construction failed");
         return false;
         }
    }   
    
    //checks first object for matching xml and sql column names
    public static function check_column_match() {
        $log_array = [];
        foreach(self::$db_fields as $col_name) {
            if($col_name != "id") {
                if( !array_key_exists($col_name,self::$object_collection[0]->xml_fields_values)){
                    $log_array[] = $col_name;
                }
            }
        }
        if($log_array){
            MessageLogger::add_log("ERROR: mySQL column and .xml fieldname mismatch. Following mySQL names were not found: ".join(", ",$log_array));
            return false;
        } else {
            MessageLogger::add_log("mySQL columns and .xml field names match");
            return true;
        }
        
    }
    
    //sync values are class specific
    public static function sync_vars() {
        self::$sync_key = self::$db_fields[1];
        self::$syn_date_start = self::$db_fields[3];
        self::$sync_date_end = self::$db_fields[4];
        self::$sync_deleted = self::$db_fields[10];
    }
    
    public function getArray() {
        return $xml_fields_values;
    }
    
}    


?>