<?php
require_once('initialize.php');

//DatabaseObject is verantwoordelijk voor mySQL queries
class Record extends DatabaseObject {
    
    //mySQL
    protected static $table_name="records";
    //field names are build dynamically from mySQL columns
    protected static $db_fields = [];
    public $fields_values = [];
    //original xml values before formatting
    public $xml_fields_values = [];
    
    public static function construct_fields() {
        global $database;
        $result_array = self::get_columns();
        foreach($result_array as $key => $value) :
            array_push(self::$db_fields,$value[0]);
        endforeach;
        echo "Construct field definitions for <b>" . self::$table_name . "</b>: " . join(", ", self::$db_fields) . "<hr/>";
    }
}    

Record::conStruct_fields();
?>