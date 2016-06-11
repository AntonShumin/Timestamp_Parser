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
        $result_set = self::get_columns();
        while($row = $database->fetch_array($result_set)) {
            echo "<hr/>";
            foreach($row as $attribute=>$value) {
                echo $attribute ."       with value       " . $value . "<br/>";
            }
        }
    }
}    

Record::conStruct_fields();
?>