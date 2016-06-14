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
                    $object->id = $row_array["id"];
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
                    if($field != "id") {
                        //break process if objects dont have appropriate array keys
                        if(array_key_exists($field,$object->mySQL_fields_values) && array_key_exists($field,$object->xml_fields_values) ) {
                            //if mismatch found for a single value
                            if($object->mySQL_fields_values[$field] != $object->xml_fields_values[$field]){
                                $object->mismatch_fields_values[$field] = $object->xml_fields_values[$field]; //assumes that xml is the most recent data
                                $count_mismatched_records++;
                                $object->mark_for_update = true;
                            }   
                        } else {
                            MessageLogger::add_log("ERROR: build_mismatch has failed to find field value: ".$field);
                            return false;
                        }
                    }
                    
                }
                if($object->mark_for_update) $count_mismatched_objects++;
            } 
        }
        MessageLogger::add_log("Mismatch check sql-xml: ".$count_empty." new sql records. Existing records: ".$count_mismatched_records." outdated fields in ".$count_mismatched_objects." objects");
        return true;
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
    
    //Step 7: loop through every object and upload/create to sql
    public static function upload_sql_records(){
        $object_array = Record::$object_collection;
        $count_created = 0;
        $count_updated = 0;
        foreach($object_array as $object){  
            //create new sql rows
            if(empty($object->mySQL_fields_values)) { //create new sql entry
                if( !$object->create() ){
                    MessageLogger::add_log("ERROR: SQL create query failed!");
                    return false;
                }
                $count_created++;
            } elseif($object->mark_for_update) {
                if( !$object->update() ){
                    MessageLogger::add_log("ERROR: SQL update query failed!");
                    return false;
                }
                $count_updated++;
            }
        }
        if($count_created+$count_updated > 0) {
            MessageLogger::add_log("Query mySQL ".static::$table_name." table with ".$count_created." new rows and updated ".$count_updated." records.");
        } else {
            MessageLogger::add_log("mySQL: Your records are up to date!");
        }
        return true;
            
    }

    //Build db_fields names from the mySQL columns
    public static function construct_fields() {
        global $database;
        $result_array = static::get_columns();
        foreach($result_array as $key => $value) :
            array_push(static::$db_fields,$value[0]);//remove index
        endforeach;
        if(static::$db_fields) {
            MessageLogger::add_log("mySQL table " . static::$table_name . " has following column names: (".count(static::$db_fields).") " . join(", ", static::$db_fields) );
            static::sync_vars(); //store field names that are used to synchronize xml and mySQL
            return true;
        } else {
            MessageLogger::add_log("Construction of Record class field names from mySQL columns failed");
            return false;
        }
    }
    
    //Construct record objects from xml array.
    public static function construct_objects($array) {
        //populate with objects
        foreach ($array as $record) {
            $new_object = new static;
            $new_object->xml_fields_values = $record; //specifically tested during bughunt, objects and fields work as intended
            static::$object_collection[] = $new_object;
            
        }
        $b_column_match = static::check_column_match(); //check if xml fields match mySQL columns, first object
         if(static::$object_collection && $b_column_match){
            MessageLogger::add_log("Record objects construction successful ".count(static::$object_collection)." objects created");
            return true;
         } else {
            MessageLogger::add_log("Record object construction failed");
            return false;
         }
    } 
    
    //checks first object for matching xml and sql column names
    public static function check_column_match() {
        $log_array = [];
        foreach(static::$db_fields as $col_name) {
            if($col_name != "id") {
                if( !array_key_exists($col_name,static::$object_collection[0]->xml_fields_values)){
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
    
    //STEP 5-EXTRA Date checker
    public static function date_checker(){
        $count_expired = 0;
        foreach(Record::$object_collection as $object) {
            $count_expired += $object->check_date();
        }
        MessageLogger::add_log("xml records expired and set to deleted = ".$count_expired);
    }
    
    /****************** ***********************
    ********mySQL general fucntions ***********
    ******************* **********************/
    //Clean table: TRUNCATE TABLE records;
    
    protected function create() {
        global $database;
        $attributes = $this->mismatch_fields_values; //escape string to be implemented here
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
    
    protected function update() {
        global $database;
        $attributes = $this->mismatch_fields_values;
        $attribute_pairs = [];
        foreach($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE ".static::$table_name." SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE id=" . $this->id;
        //echo $sql."<hr/>";
        if($database->query($sql)) {
            return true;
        } else {
            return false;
        }
    }
     
    
}
?>