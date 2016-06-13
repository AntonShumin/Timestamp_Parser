<?php 
//require_once("database.php");
//require_once("central_logic.php");

  
class DatabaseObject {
    
    public static function get_columns() {
        return static::find_by_sql("SHOW COLUMNS FROM " . static::$table_name);
    }
    
    public static function find_by_sql($sql="") {
        global $database;
        $result_set = $database->query($sql);
        //$result_array = $database->fetch_array($result_set);
        $result_array = [];
        while($row = $database->fetch_array($result_set)) {
            $result_array[] = $row;
        }
        return $result_array;
    }
    
    public static function build_sync_query() {
        $object_array = Record::$object_collection;
        $key = static::$sync_key;
        $sync_array = [];
        foreach($object_array as $object) {
            $sync_array[] = $object->xml_fields_values[$key];
        }
        $query = "SELECT * FROM ".static::$table_name." WHERE ".$key." IN (";
        $query .= join(",",$sync_array);
        $query .= ");";
        $result_array = static::find_by_sql($query);
        $log_copy = "";
        if(empty($result_array)) { //if not sql records are found, copy xml records to xml
            $log_copy = static::copy_xml_all($object_array);    
        }
        MessageLogger::add_log("mySQL has found ".count($result_array)." matching database entries.".$log_copy);
        return $result_array;
    }
    
    public static function copy_xml_all($object_array) {
        foreach($object_array as $object) {
            $object->mySQL_fields_values = $object->xml_fields_values;
            print_r($object->xml_fields_values);
            echo "<hr/>";
        }
        //$this->$mySQL_fields_values  = $this->$xml_fields_values;
        return " Copying .xml values to mysql fields for all objects";
    }
    
    
    
    
    /****************** ***********************
    ********mySQL general fucntions ***********
    ******************* **********************/
    protected function create() {
        global $database;
        $attributes = $this->$mySQL_fields_values; //escape string to be implemented here
        echo $attributes;
        /*
        $sql = "INSERT INTO ".self::$table_name." (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";
        */
    }
     
    
}
?>