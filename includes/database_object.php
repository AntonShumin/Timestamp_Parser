<?php 
//require_once("database.php");
//require_once("central_logic.php");

//Parent of classes like Record. All methods here can be re-used by any similar class.   
class DatabaseObject {
    
    public static function get_columns() {
        return static::find_by_sql("SHOW COLUMNS FROM " . static::$table_name);
    }
    
    public static function find_by_sql($sql="") {
        global $database;
        $result_set = $database->query($sql);
        $result_array = [];
        while($row = $database->fetch_array($result_set)) {
            $result_array[] = $row;
        }
        return $result_array;
    }
    
    //Step 5. Constructs an sql query, populates objects and 
    public static function xml_sync_job() {
        //Construct query
        $object_array = Record::$object_collection;
        if( empty($object_array) || !$object_array) {
            MessageLogger::add_log("ERROR: no objects have been instantiated to process sync query. Source: database_object.php xml_sync_job()");
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
        $log_match = "";
        MessageLogger::add_log("mySQL has found ".count($result_array)." matching database entries.");
        //if matching records are found, match xml to sql for each object.
        if(!empty($result_array)) { 
            $log_match = static::query_match($result_array);
        }
        if($log_match) MessageLogger::add_log($log_match);
        return true;
    }
    
    //Step 5.1: loops through each object and matches sql to xml
    public static function query_match($result_array){
        $column_names = static::$db_fields;
        $count_objects = 0;
        $count_matching_records = 0;
        $count_missing_records = 0;
        foreach (static::$object_collection as $object) { //each object
            $count_objects++;
            foreach($result_array as $row_array) { //each sql row
                if($row_array[static::$sync_key] == $object->xml_fields_values[static::$sync_key]) {
                    $count_matching_records++;
                    $id = $row_array["id"];
                    $object->mySQL_fields_values = $row_array;
                }
            }
        }
        $difference = $count_objects - $count_matching_records;
        return "Processed ".$count_objects." xml records where ".$count_matching_records." match ".static::$sync_key." with existing sql rows (".$difference." new entries).";
    }
    
    //Step 6: Loops through every object and build a mismatch array
    public static function build_mismatch(){
        $object_array = Record::$object_collection;
        $count_empty = 0;
        $count_mismatched_records = 0;
        $count_mismatched_objects = 0;
        foreach($object_array as $object) {
            if(empty($object->mySQL_fields_values)) {
                $object->mismatch_fields_values = $object->xml_fields_values;
                $object->mark_for_update = true;
                $count_empty++;
            } else { //if objects are populated by sql data, check for mismatches with xml data
                foreach(static::$db_fields as $field){
                    if($object->mySQL_fields_values[$field] != $object->xml_fields_values[$field]){
                        $object->mismatch_fields_values[$field] = $object->xml_fields_values[$field]; //assumes that xml is the most recent data
                        $count_mismatched_records++;
                        $object->mark_for_update = true;
                    }
                }
                if($object->mark_for_update) $count_mismatched_objects++;
            } 
        }
        MessageLogger::add_log("Mismatch check: ".$count_empty." new records. Existing records: ".$count_mismatched_records." oudated fields in ".$count_mismatched_objects." objects");
        
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