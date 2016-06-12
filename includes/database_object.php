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
    
    public static function build_sync_query($object_array) {
        $key = static::$sync_key;
        $sync_array = [];
        foreach($object_array as $object) {
            /*
            var_dump($object);
            echo "<hr/>";
            
            */
            //array_push($sync_array,$object->xml_fields_values["LastMinuteID"]);
        }
        $query = "SELECT * FROM ".static::$table_name." WHERE id IN (";
        $query .= join(",",$sync_array);
        $query .= ");";
        echo $query;
    }
    
}
?>