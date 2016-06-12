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
    public $mySQL_fields_values = [];
    //original xml values before formatting
    public $xml_fields_values = [];
    
    //Build db_fields names from the mySQL columns
    public static function construct_fields() {
        global $database;
        $result_array = self::get_columns();
        foreach($result_array as $key => $value) :
            array_push(self::$db_fields,$value[0]);
        endforeach;
        if(self::$db_fields) {
            MessageLogger::add_log("Construct field definitions for <b>" . self::$table_name . "</b> class from mySQL: (".count(self::$db_fields).") " . join(", ", self::$db_fields) );
            return true;
        } else {
            MessageLogger::add_log("Construction of Record class field names from mySQL columns failed");
            return false;
        }
        
    }
}    


?>