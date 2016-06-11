<?php
require_once('initialize.php');

//DatabaseObject is verantwoordelijk voor mySQL queries
class Record extends DatabaseObject {
    
    //mySQL
    protected static $table_name="records";
    protected static $db_fields = [];
    public $fields_values = [];
    //original xml values before formatting
    public $xml_fields_values = [];
    
    public static function construct_fields() {
        global $database;
        $result_array = self::get_columns();
        foreach($result_array as $key => $value) :
            echo $key . " - " . $value[0] . "<hr/>";
        endforeach;
        
        /*
        while($row = $database->fetch_array($result_set)) {
            if($row[0] != "id") {
                array_push(self::$db_fields,$row[0]);
            }
            
        }
        */
    }
}    

Record::conStruct_fields();
?>