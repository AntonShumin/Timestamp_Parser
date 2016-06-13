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
    
    public static function build_xml_sync_query() {
        $object_array = Record::$object_collection;
        if( empty($object_array) || !$object_array) {
            MessageLogger::add_log("ERROR: no objects have been instantiated to process sync query. Source: database_object.php build_sync_query");
            return false;
        }
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
            if(!$log_copy) return false;
        }
        MessageLogger::add_log("mySQL has found ".count($result_array)." matching database entries.".$log_copy);
        return true;
    }
    
    public static function copy_xml_all($object_array) {
        foreach($object_array as $object) {
            $object->mySQL_fields_values = $object->xml_fields_values;
        }
        $ok = static::write_all_to_sql();
        return $ok ? " Copying .xml values to mysql fields for all objects. ".$ok : false;    
        
    }
    
    protected static function write_all_to_sql(){
        $index = 0;
        foreach(Record::$object_collection as $object) {
            $ok = $object->create(); //makes a new mySQL row for each object
            $index++; //count number of queries processed
            if(!$ok) { //log error if any of the queries fails
                MessageLogger::add_log("ERROR: failed to created a ".static::$table_name." sql row where ".static::$sync_key." = " .$object->mySQL_fields_values[static::$sync_key]);
                return false;
            }
        }
        return "Successfully created ".$index." rows in the ".static::$table_name." table(mySQL)"; 
    }
    
    
    /****************** ***********************
    ********mySQL general fucntions ***********
    ******************* **********************/
    protected function create() {
        global $database;
        $attributes = $this->mySQL_fields_values; //escape string to be implemented here
        $sql = "INSERT INTO ".static::$table_name." (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";
        if($database->query($sql)) {
            $this->id = $database->insert_id();
            return true;
        } else {
            return false;
        }
    }
     
    
}
?>